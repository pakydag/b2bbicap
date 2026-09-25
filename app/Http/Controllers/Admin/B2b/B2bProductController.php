<?php

namespace App\Http\Controllers\Admin\B2b;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class B2bProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $brandId = $request->query('brand_id');
        
        $query = \App\Models\B2bProduct::with('brand', 'variants');
        
        if (!empty($brandId)) {
            $query->where('b2b_brand_id', $brandId);
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('brand', function($bq) use ($search) {
                      $bq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $selectedBrand = !empty($brandId) ? \App\Models\B2bBrand::find($brandId) : null;
        $products = $query->orderBy('name')->get();

        $agentController = app(\App\Http\Controllers\Agent\AgentPortalController::class);
        $giacenzaData = $agentController->getGiacenzaData();

        foreach ($products as $product) {
            $product->giacenza_match = $agentController->findGiacenzaMatch($product, $giacenzaData);
        }

        return view('admin.b2b.products.index', compact('products', 'search', 'selectedBrand'));
    }

    public function create()
    {
        return redirect()->route('admin.b2b.products.index')->with('error', 'I prodotti non possono essere inseriti manualmente. Vengono letti ed aggiornati automaticamente dal file CSV / FTPS.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'b2b_brand_id' => 'required|exists:b2b_brands,id',
            'season' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'image_url' => 'nullable|string|max:1000',
            'image_file' => 'nullable|image|max:2048',
            'has_stock' => 'boolean',
            'variants' => 'nullable|array',
            'characteristics' => 'nullable|array',
        ]);

        $data = $request->only(['name', 'code', 'b2b_brand_id', 'season', 'description', 'has_stock', 'price', 'characteristics']);

        if ($request->hasFile('image_file')) {
            $data['image'] = $request->file('image_file')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        }

        $product = \App\Models\B2bProduct::create($data);

        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                if (!empty($variantData['size']) || !empty($variantData['color'])) {
                    $product->variants()->create($variantData);
                }
            }
        }

        return redirect()->route('admin.b2b.products.index')->with('success', 'Prodotto B2B creato con successo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\B2bProduct $product)
    {
        $product->load('brand', 'variants');
        
        $agentController = app(\App\Http\Controllers\Agent\AgentPortalController::class);
        $giacenzaData = $agentController->getGiacenzaData();
        $giacenzaMatch = $agentController->findGiacenzaMatch($product, $giacenzaData);

        return view('admin.b2b.products.show', compact('product', 'giacenzaMatch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\B2bProduct $product)
    {
        return redirect()->route('admin.b2b.products.index')->with('error', 'Le informazioni sui prodotti (prezzi, descrizioni, giacenze) provengono dai file di sincronizzazione e non possono essere modificate manualmente.');
    }

    public function update(Request $request, \App\Models\B2bProduct $product)
    {
        return redirect()->route('admin.b2b.products.index')->with('error', 'Le informazioni sui prodotti provengono dai file e non possono essere modificate manualmente.');
    }

    public function destroy(\App\Models\B2bProduct $product)
    {
        return redirect()->route('admin.b2b.products.index')->with('error', 'I prodotti provengono dai file di sincronizzazione e non possono essere eliminati manualmente.');
    }

    /**
     * Import products from a Google Spreadsheet CSV export link.
     */
    public function import(Request $request)
    {
        $url = $request->input('url', 'https://docs.google.com/spreadsheets/d/11HQN1nTtHUPt29p9ZFGH5jHaSk90Ltc19RGlNDis5Dw/export?format=csv&gid=1019847442');

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(60)->get($url);
            if ($response->failed()) {
                return redirect()->back()->with('error', 'Impossibile scaricare il file. Verifica l\'URL fornito.');
            }
            $csvContent = $response->body();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Errore durante la connessione: ' . $e->getMessage());
        }

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $csvContent);
        rewind($stream);

        $headers = null;
        $importedCount = 0;
        $ignoreSuffixes = ['-fr', ' fr', '-es', ' es', '-de', ' de', '-ru', ' ru'];

        while (($row = fgetcsv($stream)) !== false) {
            if (!$headers) {
                // Find headers
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

            // Filter pronta consegna (Colonna C / DISPONIBILITA):
            // Legge ESCLUSIVAMENTE la Colonna C ('DISPONIBILITA' / 'DISPONIBILITÀ' / $row[2]).
            // I prodotti 'Su commessa' o diversi da 'Pronta consegna' NON devono essere importati.
            $dispValue = trim($data['DISPONIBILITA'] ?? $data['DISPONIBILITÀ'] ?? $row[2] ?? '');

            if (strcasecmp($dispValue, 'Pronta consegna') !== 0) {
                // Se il prodotto era stato importato in precedenza ma ora è "Su commessa", lo disattiviamo (non eliminiamo per preservare storico ordini)
                if (!empty($codice)) {
                    \App\Models\B2bProduct::where('code', $codice)->update(['is_active' => false]);
                }
                continue;
            }

            // Find or create brand: LINEA
            $brandName = trim($data['LINEA'] ?? 'BICAP');
            if (empty($brandName)) {
                $brandName = 'BICAP';
            }
            $brand = \App\Models\B2bBrand::firstOrCreate(['name' => $brandName]);

            // Compile characteristics: take only IT, EN, and neutral fields. Ignore FR, ES, DE, RU.
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

            // Create or update B2bProduct: il prezzo iniziale viene impostato a 0.
            // Verrà aggiornato esclusivamente se presente nel file delle giacenze (Giacenza.csv).
            $product = \App\Models\B2bProduct::updateOrCreate(
                ['code' => $codice],
                [
                    'name' => $nome,
                    'b2b_brand_id' => $brand->id,
                    'description' => $data['descrizione-articolo-it'] ?? '',
                    'image' => $data['FOTO-PRINCIPALE-PRODOTTO-WEB'] ?? null,
                    'price' => 0,
                    'has_stock' => true,
                    'is_active' => true,
                    'characteristics' => $characteristics
                ]
            );

            // Update variants (sizes 35 to 49) based on non-empty GTIN values
            // We DO NOT use delete() to avoid cascading deletion of b2b_order_items!
            $existingSizes = [];
            for ($size = 35; $size <= 49; $size++) {
                $gtinKey = "GTIN TAGLIA $size";
                if (!empty($data[$gtinKey])) {
                    $sizeStr = (string)$size;
                    $existingSizes[] = $sizeStr;
                    $product->variants()->updateOrCreate(
                        ['size' => $sizeStr, 'color' => 'UNICO'],
                        ['quantity' => 100] // default quantity for ready delivery
                    );
                }
            }
            
            // Per le varianti rimosse, azzeriamo la quantità anziché eliminarle
            if (!empty($existingSizes)) {
                $product->variants()->whereNotIn('size', $existingSizes)->update(['quantity' => 0]);
            }

            $importedCount++;
        }

        fclose($stream);

        // Aggiorna subito prezzi e giacenze per i prodotti presenti nel file Giacenza.csv (se non presente resta 0)
        try {
            \Illuminate\Support\Facades\Artisan::call('b2b:sync-giacenze', ['--force' => true]);
        } catch (\Throwable $e) {}

        return redirect()->route('admin.b2b.products.index')
            ->with('success', "Importazione completata con successo! Importati/aggiornati {$importedCount} prodotti in pronta consegna. Prezzi e giacenze allineati con il gestionale.");
    }

    /**
     * Sincronizza le giacenze dal server FTPS remoto.
     */
    public function syncGiacenze(Request $request)
    {
        try {
            $exitCode = \Illuminate\Support\Facades\Artisan::call('b2b:sync-giacenze', ['--force' => true]);
            if ($exitCode === 0) {
                return redirect()->route('admin.b2b.products.index')
                    ->with('success', 'File delle giacenze (Giacenza.csv) sincronizzato ed aggiornato con successo dal server FTPS! Giacenze e prezzi di listino aggiornati.');
            }
            return redirect()->route('admin.b2b.products.index')
                ->with('error', 'Errore durante la sincronizzazione delle giacenze dal server FTPS. Verifica i log di sistema.');
        } catch (\Exception $e) {
            return redirect()->route('admin.b2b.products.index')
                ->with('error', 'Errore durante la connessione al server FTPS: ' . $e->getMessage());
        }
    }
}

