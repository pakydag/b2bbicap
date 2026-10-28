<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\B2bBrand;
use App\Models\B2bProduct;

class SyncB2bAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'b2b:sync-all {--force : Forza la sincronizzazione ignorando il cooldown di 5 minuti}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizza in modo sicuro sia il catalogo prodotti da Google Sheet che le giacenze da FTPS con gestione della concorrenza';

    /**
     * Cooldown in secondi tra sincronizzazioni automatiche (5 minuti)
     */
    protected int $cooldownSeconds = 300;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $lockKey = 'b2b_sync_process_lock';
        $lock = Cache::lock($lockKey, 600); // 10 minuti di scadenza massima di sicurezza

        // 1. Verifica se un altro processo sta già sincronizzando
        if (!$lock->get()) {
            $msg = "[SyncB2bAll] Un'altra sincronizzazione è già in corso. Operazione saltata per evitare conflitti e collisioni.";
            $this->warn($msg);
            Log::info($msg);
            return 0;
        }

        try {
            // 2. Controllo Cooldown (debouncing degli accessi ravvicinati)
            if (!$this->option('force')) {
                $lastSync = Cache::get('b2b_last_sync_timestamp');
                if ($lastSync) {
                    $secondsAgo = (int) abs(now()->diffInSeconds($lastSync));
                    if ($secondsAgo < $this->cooldownSeconds) {
                        $remaining = $this->cooldownSeconds - $secondsAgo;
                        $this->info("[SyncB2bAll] I dati sono già stati aggiornati {$secondsAgo} secondi fa. Prossimo aggiornamento automatico tra {$remaining}s.");
                        return 0;
                    }
                }
            }

            $this->info("[SyncB2bAll] Avvio sincronizzazione completa B2B (Google Sheet + FTPS)...");
            Log::info("[SyncB2bAll] Inizio processo di sincronizzazione automatica...");

            // 3. Sincronizzazione Catalogo da Google Sheet
            $sheetSuccess = $this->syncProductsFromGoogleSheet();

            // 4. Sincronizzazione Giacenze da FTPS
            $ftpsSuccess = $this->syncGiacenzeFromFtps();

            // 5. Sincronizzazione Clienti & Codici da FTPS
            $customersSuccess = $this->syncCustomersFromFtps();

            if ($sheetSuccess || $ftpsSuccess || ($customersSuccess['success'] ?? false)) {
                Cache::put('b2b_last_sync_timestamp', now(), 86400);
            }

            $this->info("[SyncB2bAll] Sincronizzazione completata con successo!");
            Log::info("[SyncB2bAll] Sincronizzazione completata con successo.");

            return 0;
        } catch (\Throwable $e) {
            $err = "[SyncB2bAll] Errore critico durante la sincronizzazione: " . $e->getMessage();
            $this->error($err);
            Log::error($err, ['exception' => $e]);
            return 1;
        } finally {
            // Rilascio garantito del lock
            optional($lock)->release();
        }
    }

    /**
     * Importa e aggiorna i prodotti dal foglio Google Sheet CSV
     */
    protected function syncProductsFromGoogleSheet(): bool
    {
        $url = 'https://docs.google.com/spreadsheets/d/11HQN1nTtHUPt29p9ZFGH5jHaSk90Ltc19RGlNDis5Dw/export?format=csv&gid=1019847442';
        $this->info("1/2 - Scaricamento prodotti da Google Sheet...");

        try {
            $response = Http::timeout(60)->get($url);
            if ($response->failed()) {
                $this->error("Impossibile scaricare il file da Google Sheet (HTTP Status: " . $response->status() . ")");
                Log::error("[SyncB2bAll] Download Google Sheet fallito (HTTP " . $response->status() . ")");
                return false;
            }
            $csvContent = $response->body();
        } catch (\Exception $e) {
            $this->error("Errore di connessione a Google Sheet: " . $e->getMessage());
            Log::error("[SyncB2bAll] Errore connessione Google Sheet: " . $e->getMessage());
            return false;
        }

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $csvContent);
        rewind($stream);

        $headers = null;
        $importedCount = 0;
        $ignoreSuffixes = ['-fr', ' fr', '-es', ' es', '-de', ' de', '-ru', ' ru'];

        while (($row = fgetcsv($stream, 0, ',', '"', '\\')) !== false) {
            if (!$headers) {
                if (in_array('ORDINE ARTICOLO', $row)) {
                    $headers = $row;
                }
                continue;
            }

            if (count($row) < count($headers)) {
                $row = array_pad($row, count($headers), '');
            } else {
                $row = array_slice($row, 0, count($headers));
            }

            $data = array_combine($headers, $row);

            $nome = trim($data['NOME'] ?? '');
            $codice = trim($data['CODICE'] ?? '');

            if (empty($nome) || empty($codice)) {
                continue;
            }

            // Solo Pronta consegna (Colonna C)
            $dispValue = trim($data['DISPONIBILITA'] ?? $data['DISPONIBILITÀ'] ?? $row[2] ?? '');
            if (strcasecmp($dispValue, 'Pronta consegna') !== 0) {
                if (!empty($codice) && !empty($nome)) {
                    B2bProduct::where('code', $codice)->where('name', $nome)->update(['is_active' => false]);
                }
                continue;
            }

            $brandName = trim($data['LINEA'] ?? 'BICAP');
            if (empty($brandName)) {
                $brandName = 'BICAP';
            }
            $brand = B2bBrand::firstOrCreate(['name' => $brandName]);

            // Caratteristiche IT, EN e neutre
            $characteristics = [];
            foreach ($data as $key => $value) {
                if (in_array($key, ['NOME', 'CODICE', 'PREZZO CON IVA', 'LINEA'])) {
                    continue;
                }

                $keyLower = strtolower($key);
                $ignored = false;
                foreach ($ignoreSuffixes as $suffix) {
                    if (str_ends_with($keyLower, $suffix) || str_contains($keyLower, $suffix . ' ') || str_contains($keyLower, ' ' . $suffix)) {
                        $ignored = true;
                        break;
                    }
                }
                if ($ignored) {
                    continue;
                }

                $characteristics[$key] = trim($value);
            }

            // Estrazione Prezzo dal file Google Sheet (colonna "PREZZO CON IVA")
            $rawGooglePrice = $data['PREZZO CON IVA'] ?? ($data['prezzo-con-iva'] ?? null);
            $googlePrice = 0.0;
            if (!empty($rawGooglePrice)) {
                $cleanPrice = str_replace(',', '.', preg_replace('/[^\d.,]/', '', $rawGooglePrice));
                $googlePrice = (float)$cleanPrice;
            }

            // Create or update B2bProduct: il prezzo è preso direttamente dal Google Sheet
            $characteristics['PREZZO CON IVA'] = $googlePrice;

            // Identificato univocamente dalla combinazione di codice E nome
            $product = B2bProduct::updateOrCreate(
                [
                    'code' => $codice,
                    'name' => $nome,
                ],
                [
                    'b2b_brand_id' => $brand->id,
                    'description' => $data['descrizione-articolo-it'] ?? '',
                    'image' => $data['FOTO-PRINCIPALE-PRODOTTO-WEB'] ?? null,
                    'price' => $googlePrice,
                    'has_stock' => true,
                    'is_active' => true,
                    'characteristics' => $characteristics
                ]
            );

            // Varianti (taglie 35-49)
            $existingSizes = [];
            for ($size = 35; $size <= 49; $size++) {
                $gtinKey = "GTIN TAGLIA $size";
                if (!empty($data[$gtinKey])) {
                    $sizeStr = (string)$size;
                    $existingSizes[] = $sizeStr;
                    $product->variants()->firstOrCreate(
                        ['size' => $sizeStr, 'color' => 'UNICO'],
                        ['quantity' => 0]
                    );
                }
            }

            if (!empty($existingSizes)) {
                $product->variants()->whereNotIn('size', $existingSizes)->update(['quantity' => 0]);
            }

            $importedCount++;
        }

        fclose($stream);
        $this->info("Google Sheet sincronizzato: {$importedCount} prodotti pronta consegna aggiornati.");
        return true;
    }

    /**
     * Scarica Giacenza.csv da server FTPS e aggiorna i prezzi
     */
    protected function syncGiacenzeFromFtps(): bool
    {
        $this->info("2/2 - Scaricamento Giacenza.csv da server FTPS...");

        $host = env('FTPS_GIACENZE_HOST', '51.75.145.169');
        $username = env('FTPS_GIACENZE_USERNAME', 'bicapb2b');
        $password = env('FTPS_GIACENZE_PASSWORD', 'lB24RiL=^D');
        $remotePath = env('FTPS_GIACENZE_REMOTE_PATH', 'Output/Giacenza.csv');
        $destination = base_path('Giacenza.csv');
        
        // Uso di un file temporaneo univoco per evitare collisioni tra processi
        $tempPath = sys_get_temp_dir() . '/Giacenza_temp_' . uniqid() . '.csv';

        $conn = @ftp_ssl_connect($host, 21, 15);
        if (!$conn) {
            $conn = @ftp_connect($host, 21, 15);
        }

        if (!$conn) {
            $msg = "Impossibile connettersi al server FTP/FTPS {$host}";
            $this->error($msg);
            Log::error("[SyncB2bAll] {$msg}");
            return false;
        }

        $login = @ftp_login($conn, $username, $password);
        if (!$login) {
            $msg = "Autenticazione FTP fallita per l'utente {$username}";
            $this->error($msg);
            Log::error("[SyncB2bAll] {$msg}");
            @ftp_close($conn);
            return false;
        }

        @ftp_pasv($conn, true);

        $downloadOk = @ftp_get($conn, $tempPath, $remotePath, FTP_BINARY);
        @ftp_close($conn);

        if (!$downloadOk || !file_exists($tempPath) || filesize($tempPath) === 0) {
            $msg = "Download del file {$remotePath} da FTPS fallito o vuoto.";
            $this->error($msg);
            Log::error("[SyncB2bAll] {$msg}");
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
            return false;
        }

        // Sostituzione sicura del file Giacenza.csv
        @copy($tempPath, $destination);
        @unlink($tempPath);
        @chmod($destination, 0666);
        $size = filesize($destination);
        $this->info("Giacenza.csv scaricato con successo ({$size} byte).");

        // Aggiorna disponibilità e giacenze varianti dal file
        $this->updateProductStocksFromGiacenza();

        return true;
    }

    /**
     * Aggiorna le giacenze effettive delle varianti dal file Giacenza.csv
     */
    protected function updateProductStocksFromGiacenza(): void
    {
        $this->info("Aggiornamento disponibilità varianti da Giacenza.csv...");
        $agentController = app(\App\Http\Controllers\Agent\AgentPortalController::class);
        $giacenzaData = $agentController->getGiacenzaData();
        $products = B2bProduct::with('variants')->get();
        $updatedVariantsCount = 0;

        foreach ($products as $product) {
            $match = $agentController->findGiacenzaMatch($product, $giacenzaData);
            if ($match && isset($match['current_stock'])) {
                $totalStock = 0;
                foreach ($product->variants as $variant) {
                    $size = $variant->size;
                    // Quantità netta disponibile in pronta consegna (giacenza fisica - impegnato)
                    $qty = max(0, (int)($match['current_stock'][$size] ?? 0));
                    if ((int)$variant->quantity !== $qty) {
                        $variant->quantity = $qty;
                        $variant->save();
                        $updatedVariantsCount++;
                    }
                    $totalStock += $qty;
                }
                $hasStock = ($totalStock > 0);
                if ($product->has_stock !== $hasStock) {
                    $product->has_stock = $hasStock;
                    $product->save();
                }
            }
        }

        $this->info("Disponibilità giacenze aggiornate per {$updatedVariantsCount} varianti.");
        Log::info("[SyncB2bAll] Disponibilità giacenze aggiornate per {$updatedVariantsCount} varianti da Giacenza.csv.");
    }

    /**
     * Scarica Clienti_Bicap.xlsx (o .csv) da server FTPS e aggiorna anagrafiche e codici clienti
     */
    public function syncCustomersFromFtps(): array
    {
        if ($this->output) {
            $this->info("3/3 - Verifica e scaricamento anagrafica clienti da FTPS (Output/Clienti_Bicap.xlsx)...");
        }

        $importer = new \App\Services\CustomerImportService();
        $result = $importer->syncFromFtps();

        if ($this->output) {
            if ($result['success'] ?? false) {
                $this->info($result['message']);
            } else {
                $this->warn($result['message'] ?? 'Sincronizzazione clienti fallita');
            }
        }

        return $result;
    }
}

