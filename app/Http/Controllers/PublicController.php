<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Article;
use App\Models\HomeBlock;
use App\Models\Setting;
use App\Models\ContactRequest;
use App\Models\TransferRequest;
use App\Models\CarRentalRequest;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Models\B2bProduct;
use App\Models\B2bBrand;

class PublicController extends Controller
{
    protected array $global_seo = [];

    public function __construct()
    {
        // Let AppServiceProvider handle shared_sezioni to ensure it's loaded for all public views

        // Fetch Global SEO Settings once
        $this->global_seo = [
            'home_seo_title' => Setting::where('key', 'home_seo_title')->value('value'),
            'home_seo_description' => Setting::where('key', 'home_seo_description')->value('value'),
            'home_seo_image' => Setting::where('key', 'home_seo_image')->value('value'),
        ];
    }

    public function home(Request $request)
    {
        $products = B2bProduct::with('brand', 'variants')
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($p) {
                return (int)($p->characteristics['ORDINE ARTICOLO'] ?? 999);
            })
            ->values();

        // Tracciamento ricerche per la Dashboard Shop
        $searchQuery = trim($request->get('q') ?? $request->get('cerca') ?? '');
        if (!empty($searchQuery)) {
            $matchingCount = $products->filter(function ($p) use ($searchQuery) {
                return stripos($p->name, $searchQuery) !== false 
                    || stripos($p->code, $searchQuery) !== false 
                    || stripos($p->description, $searchQuery) !== false;
            })->count();

            $log = \App\Models\ShopSearchLog::firstOrNew(['query' => \Illuminate\Support\Str::limit($searchQuery, 100)]);
            $log->type = 'keyword';
            $log->results_count = $matchingCount;
            $log->hits = ($log->hits ?? 0) + 1;
            $log->ip_address = $request->ip();
            $log->user_id = auth()->id();
            $log->save();
        }

        // Collezioni (Brands)
        $collezioni = B2bBrand::orderBy('name')->pluck('name')->all();

        // Accumulators for available filters
        $settori = [];
        $tipologie = [];
        $categorie = [];
        $materiali = [];
        $puntali = [];
        $suole = [];
        $calzate = [];
        $norme = [];

        $formattedProducts = $products->map(function ($p) use (&$settori, &$tipologie, &$categorie, &$materiali, &$puntali, &$suole, &$calzate, &$norme) {
            $c = $p->characteristics ?? [];
            
            $brandName = $p->brand?->name ?? '';
            $tipologia = trim($c['TIPOLOGIA-FILTRO-IT'] ?? '');
            $cat = trim($c['CAT-SICUREZZA-FILTRO'] ?? $c['CAT-SICUREZZA'] ?? '');
            $catFull = trim($c['CAT-SICUREZZA'] ?? $cat);
            $materiale = trim($c['MATERIALE-FILTRO-IT'] ?? '');
            $puntale = trim($c['PUNTALE-FILTRO-IT'] ?? $c['PUNTALE'] ?? '');
            $suola = trim($c['SUOLA-FILTRO-IT'] ?? '');
            $calzata = trim($c['CALZATA'] ?? '');
            $norma = trim($c['NORMA'] ?? '');

            // Sectors
            $prodSettori = [];
            if (!empty($c['SETTORE-DI-UTILIZZO-IT'])) {
                $parts = preg_split('/[\/,]/', $c['SETTORE-DI-UTILIZZO-IT']);
                foreach ($parts as $part) {
                    $trimmed = trim($part);
                    if ($trimmed) {
                        $prodSettori[] = $trimmed;
                        $settori[$trimmed] = ($settori[$trimmed] ?? 0) + 1;
                    }
                }
            }

            if ($tipologia) $tipologie[$tipologia] = ($tipologie[$tipologia] ?? 0) + 1;
            if ($cat) $categorie[$cat] = ($categorie[$cat] ?? 0) + 1;
            if ($materiale) $materiali[$materiale] = ($materiali[$materiale] ?? 0) + 1;
            if ($puntale) $puntali[$puntale] = ($puntali[$puntale] ?? 0) + 1;
            if ($suola) $suole[$suola] = ($suole[$suola] ?? 0) + 1;
            if ($calzata) $calzate[$calzata] = ($calzate[$calzata] ?? 0) + 1;
            if ($norma) $norme[$norma] = ($norme[$norma] ?? 0) + 1;

            $subtitle = trim($p->code . ' ' . $catFull);

            return [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->code,
                'subtitle' => $subtitle,
                'brand' => $brandName,
                'image' => $p->image_url,
                'price' => (float)$p->price,
                'tipologia' => $tipologia,
                'categoria' => $cat,
                'materiale' => $materiale,
                'puntale' => $puntale,
                'suola' => $suola,
                'calzata' => $calzata,
                'norma' => $norma,
                'settori' => $prodSettori,
                'url' => route('public.product.detail', $p->slug ?: $p->id),
            ];
        });

        arsort($settori);
        ksort($tipologie);
        ksort($categorie);
        ksort($materiali);
        ksort($puntali);
        ksort($suole);
        ksort($calzate);
        ksort($norme);

        $filterOptions = [
            'collezioni' => $collezioni,
            'settori' => array_keys($settori),
            'tipologie' => array_keys($tipologie),
            'categorie' => array_keys($categorie),
            'materiali' => array_keys($materiali),
            'puntali' => array_keys($puntali),
            'suole' => array_keys($suole),
            'calzate' => array_keys($calzate),
            'norme' => array_keys($norme),
        ];

        $seo = [
            'title' => 'BICAP Official SHOP | Scarpe Antinfortunistiche e di Sicurezza Made in Italy',
            'description' => 'Scopri il catalogo ufficiale BICAP Absolutely Safe. Calzature di sicurezza sportive, leggere e resistenti con certificazioni S1PS, S3, S3S, S7S ed ESD.',
            'image' => asset('/storage/seo/bicap-og-image.jpg'),
            'url' => url()->current(),
        ];

        return view('public.home', compact('products', 'formattedProducts', 'filterOptions', 'seo'));
    }

    public function productDetail($identifier)
    {
        $product = B2bProduct::with('brand', 'variants')
            ->where('is_active', true)
            ->where(function ($q) use ($identifier) {
                $q->where('slug', $identifier);
                if (is_numeric($identifier)) {
                    $q->orWhere('id', (int)$identifier);
                }
            })
            ->first();

        // Fallback: cerca per slug generato dal nome
        if (!$product) {
            $product = B2bProduct::with('brand', 'variants')
                ->where('is_active', true)
                ->get()
                ->first(function ($p) use ($identifier) {
                    return \Illuminate\Support\Str::slug($p->name) === $identifier;
                });
        }

        if (!$product) {
            abort(404);
        }

        // Se l'utente o un vecchio link usa l'ID numerico, esegui redirect 301 SEO allo slug con nome prodotto
        if (is_numeric($identifier) && !empty($product->slug)) {
            return redirect()->route('public.product.detail', $product->slug, 301);
        }
        
        // Verifica disponibilità effettive da Giacenza.csv per garantire coerenza immediata
        $agentController = app(\App\Http\Controllers\Agent\AgentPortalController::class);
        $giacenzaData = $agentController->getGiacenzaData();
        $giacenzaMatch = $agentController->findGiacenzaMatch($product, $giacenzaData);

        if ($giacenzaMatch && isset($giacenzaMatch['current_stock'])) {
            foreach ($product->variants as $variant) {
                $variant->quantity = max(0, (int)($giacenzaMatch['current_stock'][$variant->size] ?? 0));
            }
        }

        $c = $product->characteristics ?? [];
        
        // Correlati / Upselling ("Potrebbe interessarti" da Google Sheet)
        $relatedProducts = collect();
        $targetNames = [];
        for ($i = 1; $i <= 4; $i++) {
            $val = !empty($c["UPSELLING PRODUCT $i"]) 
                ? trim($c["UPSELLING PRODUCT $i"]) 
                : (!empty($c["PRODOTTO CORRELATO $i"]) ? trim($c["PRODOTTO CORRELATO $i"]) : null);
            if ($val) {
                $targetNames[] = $val;
            }
        }

        foreach ($targetNames as $name) {
            $match = B2bProduct::where('is_active', true)
                ->where(function($q) use ($name) {
                    $q->where('name', $name)
                      ->orWhere('name', 'LIKE', $name . '%')
                      ->orWhere('code', 'LIKE', '%' . $name . '%');
                })
                ->first();

            if ($match && !$relatedProducts->contains('id', $match->id)) {
                $relatedProducts->push($match);
            }
        }

        // Fallback se meno di 4
        if ($relatedProducts->count() < 4) {
            $fallback = B2bProduct::where('is_active', true)
                ->whereNotIn('id', $relatedProducts->pluck('id')->all())
                ->inRandomOrder()
                ->take(4 - $relatedProducts->count())
                ->get();
            $relatedProducts = $relatedProducts->concat($fallback);
        }

        $seo = [
            'title' => $product->name . ' - ' . $product->code . ' | BICAP Official',
            'description' => Str::limit(strip_tags($product->localized_description), 160),
            'image' => $product->image_url,
            'url' => route('public.product.detail', $product->slug ?: $product->id),
        ];

        // Accordion Info (Condizioni di vendita, Resi e rimborsi, Spedizioni, FAQ, Guida alle taglie dal Gestionale)
        $condizioniSection = Section::with(['articles' => function($q) {
            $q->where('visibile', true)->orderBy('ordine', 'asc')->orderBy('id', 'asc');
        }])->where('slug', 'bicap-condizioni-di-vendita')->orWhere('id', 16)->first();

        $accordionArticles = $condizioniSection ? $condizioniSection->articles : collect();

        return view('public.product_detail', compact('product', 'relatedProducts', 'seo', 'accordionArticles'));
    }

    public function sezione($slug)
    {
        $cleanSlug = preg_replace('/^bicap[-_]/i', '', $slug);

        // 1. Cerca prima negli Articoli del Gestionale per slug personalizzato (es: condizioni-di-vendita, resi-e-rimborsi, spedizioni, faq, ecc.)
        $articolo = Article::with('section')
            ->where('visibile', true)
            ->where(function($query) use ($slug, $cleanSlug) {
                $query->where('slug', $slug)
                      ->orWhere('slug', $cleanSlug)
                      ->orWhere('slug', 'bicap-' . $cleanSlug);

                if (str_contains($cleanSlug, 'mission')) {
                    $query->orWhere('slug', 'like', '%mission%vision%')
                          ->orWhere('slug', 'like', '%mission%e%vision%')
                          ->orWhere('titolo', 'like', '%mission%');
                }
                if (str_contains($cleanSlug, 'tagli') || str_contains($cleanSlug, 'size')) {
                    $query->orWhere('titolo', 'like', '%taglie%');
                }
                $query->orWhere('titolo', 'like', '%' . str_replace('-', ' ', $cleanSlug) . '%');
                if (preg_match('/^(\d+)-it$/', $slug, $matches)) {
                    $query->orWhere('id', $matches[1]);
                }
            })
            ->first();

        if ($articolo) {
            $seo = [
                'title' => $articolo->seo_title ?: ($articolo->titolo . ' - ' . ($this->global_seo['home_seo_title'] ?: config('app.name'))),
                'description' => $articolo->seo_description ?: Str::limit(strip_tags($articolo->descrizione), 160) ?: ($this->global_seo['home_seo_description'] ?? 'Leggi l\'articolo ' . $articolo->titolo),
                'image' => $articolo->seo_image ? asset($articolo->seo_image) : ($articolo->foto ? asset($articolo->foto) : ($this->global_seo['home_seo_image'] ? asset($this->global_seo['home_seo_image']) : asset('img/default-share.jpg'))),
                'url' => url()->current()
            ];

            $altriArticoli = Article::where('visibile', true)
                ->where('id', '!=', $articolo->id)
                ->orderBy('ordine')
                ->get();

            return view('public.articolo', compact('articolo', 'seo', 'altriArticoli'));
        }

        // 2. Cerca nelle Sezioni del Gestionale (es: bicap-mission-e-vision, bicap-condizioni-di-vendita, ecc.)
        $sezione = Section::where('visibile', true)
            ->where(function($query) use ($slug, $cleanSlug) {
                $query->where('slug', $slug)
                      ->orWhere('slug', $cleanSlug)
                      ->orWhere('slug', 'bicap-' . $cleanSlug);

                if (str_contains($cleanSlug, 'mission')) {
                    $query->orWhere('slug', 'like', '%mission%vision%')
                          ->orWhere('slug', 'like', '%mission%e%vision%')
                          ->orWhere('nome', 'like', '%mission%');
                }
                if (str_contains($cleanSlug, 'condizioni')) {
                    $query->orWhere('slug', 'like', '%condizioni%');
                }
                if (str_contains($cleanSlug, 'resi')) {
                    $query->orWhere('slug', 'like', '%resi%');
                }
                if (str_contains($cleanSlug, 'spedizioni')) {
                    $query->orWhere('slug', 'like', '%spedizioni%');
                }
                if (str_contains($cleanSlug, 'faq')) {
                    $query->orWhere('slug', 'like', '%faq%');
                }
            })
            ->first();

        if ($sezione) {
            // Se la sezione ha 1 solo articolo o è di tipo pagina, visualizziamo direttamente l'articolo
            if ($sezione->tipo === 'pagina' || $sezione->articles()->where('visibile', true)->count() === 1) {
                $primo_articolo = $sezione->articles()->where('visibile', true)->first();
                if ($primo_articolo) {
                    $articolo = $primo_articolo;
                    $seo = [
                        'title' => $articolo->seo_title ?: ($articolo->titolo . ' - ' . ($this->global_seo['home_seo_title'] ?: config('app.name'))),
                        'description' => $articolo->seo_description ?: Str::limit(strip_tags($articolo->descrizione), 160) ?: ($this->global_seo['home_seo_description'] ?? 'Leggi l\'articolo ' . $articolo->titolo),
                        'image' => $articolo->seo_image ? asset($articolo->seo_image) : ($articolo->foto ? asset($articolo->foto) : ($this->global_seo['home_seo_image'] ? asset($this->global_seo['home_seo_image']) : asset('img/default-share.jpg'))),
                        'url' => url()->current()
                    ];

                    $altriArticoli = Article::where('visibile', true)
                        ->where('id', '!=', $articolo->id)
                        ->orderBy('ordine')
                        ->get();

                    return view('public.articolo', compact('articolo', 'seo', 'altriArticoli'));
                }
            }

            $articoli = $sezione->articles()->where('visibile', true)->orderBy('ordine')->latest()->paginate(9);

            // Prepare SEO for Section (lists)
            $seo = [
                'title' => $sezione->seo_title ?: ($sezione->nome . ' - ' . ($this->global_seo['home_seo_title'] ?: config('app.name'))),
                'description' => $sezione->seo_description ?: Str::limit(strip_tags($sezione->contenuto), 160) ?: ($this->global_seo['home_seo_description'] ?? 'Esplora i contenuti di ' . $sezione->nome),
                'image' => $sezione->seo_image ? asset($sezione->seo_image) : ($sezione->foto ? asset($sezione->foto) : ($this->global_seo['home_seo_image'] ? asset($this->global_seo['home_seo_image']) : asset('img/default-share.jpg'))),
                'url' => url()->current()
            ];

            return view('public.sezione', compact('sezione', 'articoli', 'seo'));
        }

        abort(404);
    }

    public function articolo($sezione_slug, $articolo_slug)
    {
        $sezione = Section::where('slug', $sezione_slug)
                          ->where('visibile', true)
                          ->firstOrFail();

        // Fallback: cerca l'articolo per slug testuale O per ID se lo slug è nel formato 'id-it'
        $articolo = Article::where('visibile', true)
                           ->where(function($query) use ($articolo_slug) {
                               $query->where('slug', $articolo_slug)
                                     ->orWhere(function($query) use ($articolo_slug) {
                                         if (preg_match('/^(\d+)-it$/', $articolo_slug, $matches)) {
                                             $query->where('id', $matches[1]);
                                         }
                                     });
                           })
                           ->firstOrFail();

        // Prepare SEO for Article
        $seo = [
            'title' => $articolo->seo_title ?: ($articolo->titolo . ' - ' . ($this->global_seo['home_seo_title'] ?: config('app.name'))),
            'description' => $articolo->seo_description ?: Str::limit(strip_tags($articolo->descrizione), 160) ?: ($this->global_seo['home_seo_description'] ?? 'Leggi l\'articolo ' . $articolo->titolo),
            'image' => $articolo->seo_image ? asset($articolo->seo_image) : ($articolo->foto ? asset($articolo->foto) : ($this->global_seo['home_seo_image'] ? asset($this->global_seo['home_seo_image']) : asset('img/default-share.jpg'))),
            'url' => url()->current()
        ];

        $altriArticoli = Article::where('visibile', true)
            ->where('id', '!=', $articolo->id)
            ->orderBy('ordine')
            ->get();

        return view('public.articolo', compact('articolo', 'seo', 'altriArticoli'));
    }

    public function submitContactForm(Request $request)
    {
        $request->validate([
            'privacy' => 'required|accepted',
        ]);

        $validated = $request->validate([
            'ragione_sociale' => 'nullable|string|max:255',
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'richiesta' => 'required|string',
        ]);

        $recipient = config('mail.from.address');
        if ($recipient) {
            try {
                \Illuminate\Support\Facades\Mail::to($recipient)->send(new \App\Mail\ContactRequestMail($validated));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send contact request email: " . $e->getMessage());
            }
        }

        ContactRequest::create($validated);

        $successMessage = app()->getLocale() === 'en' 
            ? 'Thank you for contacting us! Your request has been sent successfully. We will reply as soon as possible.'
            : 'Grazie per averci contattato! La tua richiesta è stata inviata con successo. Ti risponderemo il prima possibile.';

        return redirect()->back()->with('success', $successMessage);
    }

    public function submitTransferForm(Request $request)
    {
        $request->validate([
            'privacy' => 'required|accepted',
        ]);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'luogo_arrivo' => 'required|string|max:255',
            'data' => 'required|date',
            'orario' => 'required|string',
            'numero_persone' => 'required|integer|min:1',
            'andata_ritorno' => 'nullable|boolean',
            'data_ritorno' => 'nullable|required_if:andata_ritorno,1|date',
            'orario_ritorno' => 'nullable|required_if:andata_ritorno,1|string',
            'messaggio' => 'nullable|string',
        ]);

        $recipient = config('mail.from.address');
        if ($recipient) {
            \Illuminate\Support\Facades\Mail::to($recipient)->send(new \App\Mail\TransferRequestMail($validated));
        }

        TransferRequest::create($validated);

        $successMessage = app()->getLocale() === 'en'
            ? 'Thank you! Your transfer request has been sent successfully. We will reply as soon as possible.'
            : 'Grazie! La tua richiesta di transfer è stata inviata con successo. Ti risponderemo al più presto.';

        return redirect()->back()->with('transfer_success', $successMessage);
    }

    public function submitCarRentalForm(Request $request)
    {
        $request->validate([
            'privacy' => 'required|accepted',
        ]);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'data_ritiro' => 'required|date',
            'orario_ritiro' => 'required|string',
            'data_riconsegna' => 'required|date',
            'orario_riconsegna' => 'required|string',
            'numero_posti' => 'required|integer|min:1|max:9',
            'messaggio' => 'nullable|string',
        ]);

        $recipient = config('mail.from.address');
        if ($recipient) {
            \Illuminate\Support\Facades\Mail::to($recipient)->send(new \App\Mail\CarRentalRequestMail($validated));
        }

        CarRentalRequest::create($validated);

        $successMessage = app()->getLocale() === 'en'
            ? 'Thank you! Your car rental request has been sent successfully. We will reply as soon as possible.'
            : 'Grazie! La tua richiesta di noleggio auto è stata inviata con successo. Ti risponderemo al più presto.';

        return redirect()->back()->with('car_rental_success', $successMessage);
    }

    public function viewContactThread($token)
    {
        $contactRequest = ContactRequest::where('secure_token', $token)->firstOrFail();

        $seo = [
            'title' => (app()->getLocale() === 'en' ? 'Conversation Thread' : 'Conversazione Richiesta') . ' - ' . config('app.name'),
            'description' => 'Visualizza la conversazione in merito alla richiesta di contatto.',
            'image' => null,
            'url' => url()->current()
        ];

        return view('public.contact_thread', compact('contactRequest', 'seo'));
    }

    public function replyContactThread(Request $request, $token)
    {
        $contactRequest = ContactRequest::where('secure_token', $token)->firstOrFail();

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB limit
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            $attachmentName = $request->file('attachment')->getClientOriginalName();
        }

        $message = \App\Models\ContactMessage::create([
            'contact_request_id' => $contactRequest->id,
            'sender' => 'client',
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        // Imposta letto a false per notificare l'amministratore nel gestionale
        $contactRequest->update(['letto' => false]);

        // Invia email all'amministratore del sito
        $recipient = config('mail.from.address');
        if ($recipient) {
            try {
                \Illuminate\Support\Facades\Mail::to($recipient)->send(
                    new \App\Mail\ClientRepliedMail($contactRequest, $request->message, $attachmentPath, $attachmentName)
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send client reply notification: " . $e->getMessage());
            }
        }

        $successMsg = app()->getLocale() === 'en'
            ? 'Your reply has been sent successfully.'
            : 'La tua risposta è stata inviata con successo.';

        return redirect()->back()->with('success', $successMsg);
    }

    public function viewTransferThread($token)
    {
        $transferRequest = \App\Models\TransferRequest::where('secure_token', $token)->firstOrFail();
        $transferRequest->load('messages');

        $seo = [
            'title' => (app()->getLocale() === 'en' ? 'Transfer Conversation' : 'Conversazione Transfer') . ' - ' . config('app.name'),
            'description' => 'Visualizza la conversazione in merito alla richiesta di transfer.',
            'image' => null,
            'url' => url()->current()
        ];

        return view('public.transfer_thread', compact('transferRequest', 'seo'));
    }

    public function replyTransferThread(Request $request, $token)
    {
        $transferRequest = \App\Models\TransferRequest::where('secure_token', $token)->firstOrFail();

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            $attachmentName = $request->file('attachment')->getClientOriginalName();
        }

        \App\Models\TransferMessage::create([
            'transfer_request_id' => $transferRequest->id,
            'sender' => 'client',
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        $transferRequest->update(['letto' => false]);

        $recipient = config('mail.from.address');
        if ($recipient) {
            try {
                \Illuminate\Support\Facades\Mail::to($recipient)->send(
                    new \App\Mail\ClientTransferRepliedMail($transferRequest, $request->message, $attachmentPath, $attachmentName)
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send client transfer reply notification: " . $e->getMessage());
            }
        }

        $successMsg = app()->getLocale() === 'en'
            ? 'Your reply has been sent successfully.'
            : 'La tua risposta è stata inviata con successo.';

        return redirect()->back()->with('success', $successMsg);
    }

    public function viewCarRentalThread($token)
    {
        $carRentalRequest = \App\Models\CarRentalRequest::where('secure_token', $token)->firstOrFail();
        $carRentalRequest->load('messages');

        $seo = [
            'title' => (app()->getLocale() === 'en' ? 'Rental Conversation' : 'Conversazione Noleggio') . ' - ' . config('app.name'),
            'description' => 'Visualizza la conversazione in merito alla richiesta di noleggio auto.',
            'image' => null,
            'url' => url()->current()
        ];

        return view('public.car_rental_thread', compact('carRentalRequest', 'seo'));
    }

    public function replyCarRentalThread(Request $request, $token)
    {
        $carRentalRequest = \App\Models\CarRentalRequest::where('secure_token', $token)->firstOrFail();

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            $attachmentName = $request->file('attachment')->getClientOriginalName();
        }

        \App\Models\CarRentalMessage::create([
            'car_rental_request_id' => $carRentalRequest->id,
            'sender' => 'client',
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        $carRentalRequest->update(['letto' => false]);

        $recipient = config('mail.from.address');
        if ($recipient) {
            try {
                \Illuminate\Support\Facades\Mail::to($recipient)->send(
                    new \App\Mail\ClientCarRentalRepliedMail($carRentalRequest, $request->message, $attachmentPath, $attachmentName)
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send client car rental reply notification: " . $e->getMessage());
            }
        }

        $successMsg = app()->getLocale() === 'en'
            ? 'Your reply has been sent successfully.'
            : 'La tua risposta è stata inviata con successo.';

        return redirect()->back()->with('success', $successMsg);
    }
}
