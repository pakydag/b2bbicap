<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncGiacenze extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'b2b:sync-giacenze {--force : Forza la sincronizzazione ignorando il cooldown}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scarica l\'ultimo file Giacenza.csv dal server FTPS (51.75.145.169 / Output/Giacenza.csv)';

    /**
     * Cooldown in secondi tra sincronizzazioni (2 minuti = 120s)
     */
    protected int $cooldownSeconds = 120;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lockKey = 'b2b_sync_giacenze_process_lock';
        $lock = Cache::lock($lockKey, 120);

        if (!$lock->get()) {
            $msg = "[SyncGiacenze] Un'altra sincronizzazione giacenze è già in corso. Operazione saltata.";
            $this->warn($msg);
            Log::info($msg);
            return 0;
        }

        try {
            // Controllo Cooldown (2 minuti)
            if (!$this->option('force')) {
                $lastSync = Cache::get('b2b_last_giacenze_sync_timestamp');
                if ($lastSync) {
                    $secondsAgo = (int) abs(now()->diffInSeconds($lastSync));
                    if ($secondsAgo < $this->cooldownSeconds) {
                        $remaining = $this->cooldownSeconds - $secondsAgo;
                        $this->info("[SyncGiacenze] Le giacenze sono già state aggiornate {$secondsAgo} secondi fa. Prossimo aggiornamento tra {$remaining}s.");
                        return 0;
                    }
                }
            }

            $host = env('FTPS_GIACENZE_HOST', '51.75.145.169');
            $username = env('FTPS_GIACENZE_USERNAME', 'bicapb2b');
            $password = env('FTPS_GIACENZE_PASSWORD', 'lB24RiL=^D');
            $remotePath = env('FTPS_GIACENZE_REMOTE_PATH', 'Output/Giacenza.csv');
            $destination = base_path('Giacenza.csv');
            $tempPath = sys_get_temp_dir() . '/Giacenza_temp_' . uniqid() . '.csv';

            $this->info("Connessione al server FTPS ({$host})...");

            // Prova prima con FTPS (ftp_ssl_connect) poi fallback a ftp_connect
            $conn = @ftp_ssl_connect($host, 21, 15);
            if (!$conn) {
                $this->warn("Impossibile connettersi via SSL, provo FTP standard...");
                $conn = @ftp_connect($host, 21, 15);
            }

            if (!$conn) {
                $msg = "Impossibile connettersi al server FTP/FTPS {$host}";
                $this->error($msg);
                Log::error("[SyncGiacenze] {$msg}");
                return 1;
            }

            $login = @ftp_login($conn, $username, $password);
            if (!$login) {
                $msg = "Autenticazione FTP fallita per l'utente {$username}";
                $this->error($msg);
                Log::error("[SyncGiacenze] {$msg}");
                ftp_close($conn);
                return 1;
            }

            ftp_pasv($conn, true);

            $this->info("Scaricamento del file {$remotePath}...");
            $downloadOk = @ftp_get($conn, $tempPath, $remotePath, FTP_BINARY);

            ftp_close($conn);

            if (!$downloadOk || !file_exists($tempPath) || filesize($tempPath) === 0) {
                $msg = "Download del file {$remotePath} fallito oppure file vuoto.";
                $this->error($msg);
                Log::error("[SyncGiacenze] {$msg}");
                if (file_exists($tempPath)) {
                    @unlink($tempPath);
                }
                return 1;
            }

            // Sostituzione sicura del file Giacenza.csv
            @copy($tempPath, $destination);
            @unlink($tempPath);
            @chmod($destination, 0666);

            $size = filesize($destination);
            $this->info("File Giacenza.csv aggiornato con successo! Dimensione: {$size} byte.");
            Log::info("[SyncGiacenze] Giacenza.csv sincronizzato con successo. Dimensione: {$size} byte.");

            // Aggiorna i prezzi dei prodotti nel database
            $this->updateProductPrices($destination);

            Cache::put('b2b_last_giacenze_sync_timestamp', now(), 86400);

            return 0;
        } catch (\Throwable $e) {
            $err = "[SyncGiacenze] Errore durante sincronizzazione giacenze: " . $e->getMessage();
            $this->error($err);
            Log::error($err, ['exception' => $e]);
            return 1;
        } finally {
            optional($lock)->release();
        }
    }

    protected function updateProductPrices($csvPath)
    {
        $this->info("Aggiornamento prezzi prodotti dal listino B2B...");
        
        if (!file_exists($csvPath)) return;

        $file = fopen($csvPath, 'r');
        $headers = fgetcsv($file, 0, ';');
        
        $prices = [];
        while (($row = fgetcsv($file, 0, ';')) !== false) {
            if (count($row) < 6) continue; // Ensure price column exists
            $code = trim($row[0]);
            $price = trim($row[5] ?? '');
            
            if (empty($code) || $price === '') continue;
            
            $normCode = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $code));
            if (str_starts_with($normCode, 'EXP')) {
                $normCode = substr($normCode, 3);
            }
            
            $priceFloat = (float)str_replace(',', '.', $price);
            
            if ($priceFloat > 0) {
                $prices[$normCode] = $priceFloat;
            }
        }
        fclose($file);

        $products = \App\Models\B2bProduct::all();
        $updatedCount = 0;
        foreach ($products as $product) {
            $dbCode = trim($product->code);
            if (empty($dbCode)) continue;
            
            $dbNorm = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $dbCode));
            if (str_starts_with($dbNorm, 'EXP')) {
                $dbNorm = substr($dbNorm, 3);
            }
            
            // Prova match esatto
            $matchPrice = $prices[$dbNorm] ?? null;
            
            // Se non c'è match esatto, prova col findGiacenzaMatch logic (simile)
            if ($matchPrice === null) {
                foreach ($prices as $csvNormCode => $csvPrice) {
                    if (str_starts_with($csvNormCode, $dbNorm) || str_starts_with($dbNorm, $csvNormCode)) {
                        $matchPrice = $csvPrice;
                        break;
                    }
                }
            }
            
            if ($matchPrice !== null && $product->price != $matchPrice) {
                $product->price = $matchPrice;
                $product->save();
                $updatedCount++;
            }
        }
        
        $this->info("Aggiornati $updatedCount prezzi prodotti.");
        Log::info("[SyncGiacenze] Aggiornati $updatedCount prezzi prodotti.");
    }
}
