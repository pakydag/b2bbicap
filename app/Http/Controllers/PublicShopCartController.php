<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicShopCartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $shippingCosts = \App\Models\ShopShippingCost::orderByRaw("CASE WHEN nazione = 'Italia' THEN 0 ELSE 1 END, nazione ASC")->get();
        $freeThreshold = (float)(\App\Models\Setting::where('key', 'shop_free_shipping_threshold')->value('value') ?? 89.00);
        $freeShippingScope = \App\Models\Setting::where('key', 'shop_free_shipping_scope')->value('value') ?? 'italy';
        
        $totale = 0;
        if (is_array($cart)) {
            foreach ($cart as $item) {
                $totale += ((float)($item['prezzo'] ?? 0) * (int)($item['quantita'] ?? 1));
            }
        }

        return view('public.shop.cart', compact('cart', 'shippingCosts', 'freeThreshold', 'freeShippingScope', 'totale'));
    }

    public function add(Request $request)
    {
        $cart = session()->get('cart', []);

        // 1. Aggiunta da B2bProduct (catalogo principale del sito pubblico)
        if ($request->filled('b2b_product_id')) {
            $request->validate([
                'b2b_product_id' => 'required|exists:b2b_products,id',
                'quantita' => 'required|integer|min:1',
                'taglia' => 'nullable|string'
            ]);

            $b2bProduct = \App\Models\B2bProduct::with('variants')->findOrFail($request->b2b_product_id);
            $taglia = (string)($request->taglia ?: '');
            $quantita = max(1, (int)$request->quantita);

            // Cerca variante associata per taglia
            $variant = $b2bProduct->variants->firstWhere('size', $taglia);

            // Verifica giacenza disponibile effettiva da Giacenza.csv o DB
            $agentController = app(\App\Http\Controllers\Agent\AgentPortalController::class);
            $giacenzaData = $agentController->getGiacenzaData();
            $match = $agentController->findGiacenzaMatch($b2bProduct, $giacenzaData);

            $availableStock = 0;
            if ($match && isset($match['current_stock'][$taglia])) {
                $availableStock = max(0, (int)$match['current_stock'][$taglia]);
            } elseif ($variant) {
                $availableStock = max(0, (int)$variant->quantity);
            }

            $isEn = app()->getLocale() === 'en';
            if (!$variant || $availableStock <= 0) {
                $errMsg = $isEn ? "Size {$taglia} is currently out of stock." : "La taglia {$taglia} non è al momento disponibile in magazzino.";
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errMsg
                    ], 422);
                }
                return redirect()->back()->with('error', $errMsg);
            }

            $cartKey = 'b2b_' . $b2bProduct->id . '_' . $taglia;
            $currentInCart = isset($cart[$cartKey]) ? (int)$cart[$cartKey]['quantita'] : 0;
            if (($currentInCart + $quantita) > $availableStock) {
                $quantita = max(1, $availableStock - $currentInCart);
                if ($currentInCart >= $availableStock) {
                    $maxMsg = $isEn 
                        ? "Maximum available stock ({$availableStock} pairs) already in cart for size {$taglia}."
                        : "Disponibilità massima ({$availableStock} paia) già raggiunta nel carrello per la taglia {$taglia}.";
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $maxMsg
                        ], 422);
                    }
                    return redirect()->back()->with('error', $maxMsg);
                }
            }

            $price = $b2bProduct->price ? (float)$b2bProduct->price : 89.00;
            $foto = $b2bProduct->image_url ?: ($b2bProduct->characteristics['FOTO-PRINCIPALE-PRODOTTO-WEB'] ?? null);

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantita'] += $quantita;
            } else {
                $cart[$cartKey] = [
                    'variant_id' => $cartKey,
                    'b2b_product_id' => $b2bProduct->id,
                    'b2b_variant_id' => $variant ? $variant->id : null,
                    'prodotto_nome' => $b2bProduct->name,
                    'sku' => $b2bProduct->code,
                    'colore' => $variant?->color ?? 'UNICO',
                    'taglia' => $taglia,
                    'prezzo' => $price,
                    'quantita' => $quantita,
                    'foto' => $foto
                ];
            }

            session()->put('cart', $cart);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $isEn ? 'Product added to cart!' : 'Prodotto aggiunto al carrello!',
                    'cart_count' => array_sum(array_column($cart, 'quantita'))
                ]);
            }

            return redirect()->route('public.shop.cart.index')->with('success', $isEn ? 'Product successfully added to cart!' : 'Prodotto aggiunto al carrello con successo!');
        }

        // 2. Fallback per prodotti legacy ShopVariant
        $request->validate([
            'variant_id' => 'required|exists:shop_variants,id',
            'quantita' => 'required|integer|min:1'
        ]);

        $variant = \App\Models\ShopVariant::with('product')->findOrFail($request->variant_id);
        $cartKey = (string)$variant->id;
        
        $qtyRichiesta = (int)$request->quantita;
        if (isset($cart[$cartKey])) {
            $qtyRichiesta += $cart[$cartKey]['quantita'];
        }

        $settings = \App\Models\Setting::pluck('value', 'key')->all();
        $isInfinite = ($settings['shop_stock_infinite'] ?? '0') == '1';

        if (!$isInfinite && $qtyRichiesta > $variant->quantita) {
             return redirect()->back()->with('error', 'Quantità richiesta superiore alla disponibilità in magazzino.');
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantita'] += (int)$request->quantita;
        } else {
            $prezzo = $variant->prezzo_scontato && $variant->prezzo_scontato > 0 ? $variant->prezzo_scontato : $variant->prezzo;
            $foto = $variant->foto ?? ($variant->product->foto_aggiuntive[0] ?? null);
            
            $cart[$cartKey] = [
                'variant_id' => $cartKey,
                'prodotto_nome' => $variant->product->nome,
                'colore' => $variant->colore,
                'taglia' => $variant->taglia,
                'prezzo' => $prezzo,
                'quantita' => (int)$request->quantita,
                'foto' => $foto
            ];
        }

        session()->put('cart', $cart);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Prodotto aggiunto al carrello!',
                'cart_count' => array_sum(array_column($cart, 'quantita'))
            ]);
        }

        return redirect()->route('public.shop.cart.index')->with('success', 'Prodotto aggiunto al carrello!');
    }

    public function remove($variant_id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$variant_id])) {
            unset($cart[$variant_id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Prodotto rimosso dal carrello.');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('public.home')->with('error', 'Il tuo carrello è vuoto.');
        }

        $shippingCosts = \App\Models\ShopShippingCost::orderByRaw("CASE WHEN nazione = 'Italia' THEN 0 ELSE 1 END, nazione ASC")->get();
        $freeThreshold = (float)(\App\Models\Setting::where('key', 'shop_free_shipping_threshold')->value('value') ?? 89.00);
        $freeShippingScope = \App\Models\Setting::where('key', 'shop_free_shipping_scope')->value('value') ?? 'italy';
        $contrassegnoFee = (float)(\App\Models\Setting::where('key', 'payment_contrassegno_fee')->value('value') ?? 10.00);

        $totale = 0;
        if (is_array($cart)) {
            foreach ($cart as $item) {
                $totale += ((float)($item['prezzo'] ?? 0) * (int)($item['quantita'] ?? 1));
            }
        }

        return view('public.shop.checkout', compact('cart', 'shippingCosts', 'freeThreshold', 'freeShippingScope', 'contrassegnoFee', 'totale'));
    }

    public function processCheckout(Request $request)
    {
        $isEn = app()->getLocale() === 'en';
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('public.home')->with('error', $isEn ? 'Your cart is empty.' : 'Il tuo carrello è vuoto.');
        }

        // 1. Validation for the different fields depending on checkout mode
        $rules = [
            'checkout_mode' => 'required|in:guest,register,login',
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'required|string|max:50',
            'indirizzo' => 'required|string|max:255',
            'citta' => 'required|string|max:255',
            'cap' => 'required|string|max:20',
            'nazione' => 'required|string|max:100',
        ];

        // Se richiede la fattura come azienda
        if ($request->is_azienda) {
            $rules['ragione_sociale'] = 'required|string|max:255';
            $rules['partita_iva'] = 'required|string|max:50';
            // SDI e PEC sono solitamente opzionali
        }

        $messages = $isEn ? [
            'password.required' => 'Password is required to create an account.',
            'password.min' => 'Password must contain at least :min characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.mixed' => 'Password must contain at least one uppercase letter.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one special character (!@#$%&*...).',
            'email.unique' => 'This email address is already registered. Please log in or use a different email.',
        ] : [
            'password.required' => 'La password è obbligatoria per creare un account.',
            'password.min' => 'La password deve contenere almeno :min caratteri.',
            'password.confirmed' => 'La conferma della password non coincide con la password inserita.',
            'password.mixed' => 'La password deve contenere almeno una lettera maiuscola.',
            'password.numbers' => 'La password deve contenere almeno un numero.',
            'password.symbols' => 'La password deve contenere almeno un carattere speciale o strano (es. !@#$%&*...).',
            'email.unique' => 'Questo indirizzo email risulta già registrato. Accedi con il tuo account o usa un\'altra email.',
        ];

        // Se sceglie di registrarsi
        if ($request->checkout_mode === 'register') {
            $rules['password'] = [
                'required',
                'string',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ];
            $rules['email'] .= '|unique:users,email'; // Unico nella tabella users
        }

        $request->validate($rules, $messages);

        // 2. Handle User Creation if 'register'
        if ($request->checkout_mode === 'register') {
            $user = \App\Models\User::create([
                'name' => $request->nome . ' ' . $request->cognome,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => 'user' // Default B2C role
            ]);
            // Log in the user automatically
            \Illuminate\Support\Facades\Auth::login($user);
        }

        // 3. Create or update the Customer record (B2C & Billing profile)
        $customer = \App\Models\Customer::where('email', $request->email)->first();
        if (!$customer) {
            $customer = new \App\Models\Customer();
            $customer->email = $request->email;
            // Genera una password casuale se è ospite e non ha scelto di registrarsi (richiesto da schema DB)
            $customer->password = \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(12));
        }

        if ($request->checkout_mode === 'register') {
            $customer->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $customer->nome = $request->nome;
        $customer->cognome = $request->cognome;
        $customer->telefono = $request->telefono;
        $customer->indirizzo = $request->indirizzo;
        $customer->citta = $request->citta;
        $customer->cap = $request->cap;
        $customer->nazione = $request->nazione;
        
        if ($request->is_azienda) {
            $customer->ragione_sociale = $request->ragione_sociale;
            $customer->partita_iva = $request->partita_iva;
            $customer->sdi = $request->sdi;
            $customer->pec = $request->pec;
        }

        $customer->save();

        // 4. Calculate Totals
        $totaleImponibile = 0;
        foreach ($cart as $item) {
            $totaleImponibile += $item['prezzo'] * $item['quantita'];
        }

        // Calcolo Dinamico Spedizione
        $costoSpedizione = (float)(\App\Models\ShopShippingCost::where('nazione', $request->nazione)->value('costo') ?? 20.00);
        $freeThreshold = (float)(\App\Models\Setting::where('key', 'shop_free_shipping_threshold')->value('value') ?? 89.00);
        $freeShippingScope = \App\Models\Setting::where('key', 'shop_free_shipping_scope')->value('value') ?? 'italy';

        $isFreeEligible = ($freeShippingScope === 'all') || (strtolower(trim($request->nazione)) === 'italia');
        if ($isFreeEligible && $freeThreshold > 0 && $totaleImponibile >= $freeThreshold) {
            $costoSpedizione = 0;
        }

        // Calcolo Contrassegno (solo per l'Italia)
        $costoContrassegno = 0;
        if (($request->payment_method ?? '') === 'contrassegno') {
            if (strtolower(trim($request->nazione)) !== 'italia') {
                $errContr = $isEn
                    ? 'Cash on delivery is exclusively available for shipments with destination in Italy.'
                    : 'Il pagamento in contrassegno è disponibile esclusivamente per le spedizioni con destinazione in Italia.';
                return back()->withErrors(['payment_method' => $errContr])->withInput();
            }
            $costoContrassegno = (float)(\App\Models\Setting::where('key', 'payment_contrassegno_fee')->value('value') ?? 10.00);
        }

        $totaleOrdine = $totaleImponibile + $costoSpedizione + $costoContrassegno;

        // 5. Create the Order record
        $order = new \App\Models\ShopOrder();
        $order->customer_id = $customer->id;
        $order->numero_ordine = 'ORD-' . strtoupper(uniqid());
        $order->stato = 'nuovo';
        $order->stato_pagamento = 'attesa';
        $order->metodo_pagamento = $request->payment_method ?? 'bonifico';
        $order->note_cliente = $request->note_ordine;
        
        $order->totale_imponibile = $totaleImponibile;
        $order->totale_iva = 0; // Se c'è IVA specifica da calcolare, aggiungerla
        $order->totale_ordine = $totaleOrdine;
        $order->costo_spedizione = $costoSpedizione;
        $order->costo_contrassegno = $costoContrassegno;

        $order->spedizione_nome = $request->nome . ' ' . $request->cognome;
        $order->spedizione_indirizzo = $request->indirizzo;
        $order->spedizione_cap = $request->cap;
        $order->spedizione_citta = $request->citta;
        $order->spedizione_provincia = $request->provincia; // Opzionale
        $order->spedizione_nazione = $request->nazione;

        // 5b. Sync with Unified CRM Contact
        $contact = \App\Models\Contact::where('email', $customer->email)->first();
        if (!$contact) {
            $contact = new \App\Models\Contact();
            $contact->email = $customer->email;
            $contact->password = $customer->password;
        }
        $contact->first_name = $customer->nome;
        $contact->last_name = $customer->cognome;
        $contact->phone = $customer->telefono;
        $contact->address = $customer->indirizzo;
        $contact->city = $customer->citta;
        $contact->zip_code = $customer->cap;
        $contact->country = $customer->nazione;
        $contact->company_name = $customer->ragione_sociale;
        $contact->vat_number = $customer->partita_iva;
        $contact->sdi_code = $customer->sdi;
        $contact->pec = $customer->pec;
        $contact->is_shop_customer = true;
        // Tags are synced automatically by the model's booted event
        $contact->save();

        $order->contact_id = $contact->id;
        $order->save();

        // 6. Create Order Items and decrease stock
        foreach ($cart as $variant_id => $item) {
            $variant = \App\Models\ShopVariant::find($variant_id);

            \App\Models\ShopOrderItem::create([
                'shop_order_id' => $order->id,
                'shop_variant_id' => $variant ? $variant->id : null,
                'nome_prodotto' => $item['prodotto_nome'],
                'sku' => $variant ? $variant->sku : ($item['sku'] ?? null),
                'colore' => $item['colore'],
                'taglia' => $item['taglia'],
                'prezzo_unitario' => $item['prezzo'],
                'quantita' => $item['quantita'],
                'subtotale' => $item['prezzo'] * $item['quantita'],
            ]);

            // Aggiorna Magazzino
            if ($variant) {
                $isInfinite = \App\Models\Setting::where('key', 'shop_stock_infinite')->value('value') == '1';
                if (!$isInfinite) {
                    $variant->quantita = max(0, $variant->quantita - $item['quantita']);
                    $variant->save();
                }
            } elseif (!empty($item['b2b_variant_id'])) {
                $b2bVar = \App\Models\B2bProductVariant::find($item['b2b_variant_id']);
                if ($b2bVar) {
                    $b2bVar->quantity = max(0, $b2bVar->quantity - $item['quantita']);
                    $b2bVar->save();
                }
            }
        }

        // 7. Handle Payments and Post-order actions
        if ($order->metodo_pagamento === 'bonifico' || $order->metodo_pagamento === 'contrassegno') {
            try {
                \Illuminate\Support\Facades\Mail::to($customer->email)
                    ->bcc(config('mail.from.address')) // Manda copia anche all'admin
                    ->send(new \App\Mail\OrderConfirmed($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Errore invio email ordine (' . $order->metodo_pagamento . '): ' . $e->getMessage());
            }
        }

        if ($order->metodo_pagamento === 'stripe') {
            try {
                $stripeSecret = \App\Models\Setting::where('key', 'stripe_secret')->value('value');
                \Stripe\Stripe::setApiKey($stripeSecret);

                $line_items = [];
                foreach ($cart as $item) {
                    $line_items[] = [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => $item['prodotto_nome'] . ($item['taglia'] ? ' - ' . $item['taglia'] : '') . ($item['colore'] ? ' - ' . $item['colore'] : ''),
                            ],
                            'unit_amount' => round($item['prezzo'] * 100),
                        ],
                        'quantity' => $item['quantita'],
                    ];
                }
                
                if ($costoSpedizione > 0) {
                    $line_items[] = [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => 'Spedizione',
                            ],
                            'unit_amount' => round($costoSpedizione * 100),
                        ],
                        'quantity' => 1,
                    ];
                }

                $checkout_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => $line_items,
                    'mode' => 'payment',
                    'success_url' => route('public.shop.cart.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('public.shop.cart.stripe.cancel') . '?order_id=' . $order->id,
                    'client_reference_id' => $order->id,
                    'customer_email' => $customer->email,
                ]);

                return redirect($checkout_session->url);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Stripe session creation error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Errore durante la connessione a Stripe. Riprova più tardi. Dettaglio: ' . $e->getMessage());
            }
        }

        if ($order->metodo_pagamento === 'paypal') {
            try {
                $clientId = \App\Models\Setting::where('key', 'paypal_client_id')->value('value');
                $secret = \App\Models\Setting::where('key', 'paypal_secret')->value('value');
                $mode = \App\Models\Setting::where('key', 'paypal_mode')->value('value') ?: 'sandbox';
                $baseUrl = ($mode === 'live') ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

                // 1. Get Access Token
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $baseUrl . '/v1/oauth2/token');
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
                $tokenResult = curl_exec($ch);
                $tokenJson = json_decode($tokenResult);
                
                if (!$tokenJson || !isset($tokenJson->access_token)) {
                    \Illuminate\Support\Facades\Log::error('PayPal Auth Error: ' . $tokenResult);
                    throw new \Exception('Impossibile autenticarsi con PayPal. Controlla le credenziali.');
                }
                
                $accessToken = $tokenJson->access_token;
                curl_close($ch);

                // 2. Create Order
                $orderData = [
                    "intent" => "CAPTURE",
                    "purchase_units" => [[
                        "reference_id" => (string)$order->id,
                        "amount" => [
                            "currency_code" => "EUR",
                            "value" => number_format($order->totale_ordine, 2, '.', '')
                        ]
                    ]],
                    "application_context" => [
                        "return_url" => route('public.shop.cart.paypal.success'),
                        "cancel_url" => route('public.shop.cart.paypal.cancel') . '?order_id=' . $order->id
                    ]
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $baseUrl . '/v2/checkout/orders');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
                $orderResult = curl_exec($ch);
                $paypalOrder = json_decode($orderResult);
                
                if (!$paypalOrder || !isset($paypalOrder->id)) {
                    \Illuminate\Support\Facades\Log::error('PayPal Order Error: ' . $orderResult);
                    throw new \Exception('Impossibile creare l\'ordine su PayPal.');
                }
                
                curl_close($ch);

                if (isset($paypalOrder->links)) {
                    foreach ($paypalOrder->links as $link) {
                        if ($link->rel === 'approve') {
                            return redirect($link->href);
                        }
                    }
                }

                throw new \Exception('PayPal approve link not found.');

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('PayPal order creation error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Errore durante la connessione a PayPal. Riprova più tardi. Dettaglio: ' . $e->getMessage());
            }
        }

        // 8. Clear Cart & Redirect to success
        session()->forget('cart');

        return redirect()->route('public.shop.cart.success')->with('order_number', $order->numero_ordine);
    }

    public function success()
    {
        $order_number = session('order_number');
        if (!$order_number) {
            return redirect()->route('public.home');
        }
        return view('public.shop.checkout_success', compact('order_number'));
    }

    public function stripeSuccess(Request $request)
    {
        $session_id = $request->get('session_id');
        if (!$session_id) {
            return redirect()->route('public.home');
        }

        try {
            $stripeSecret = \App\Models\Setting::where('key', 'stripe_secret')->value('value');
            \Stripe\Stripe::setApiKey($stripeSecret);
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            if ($session->payment_status === 'paid') {
                $order = \App\Models\ShopOrder::find($session->client_reference_id);
                if ($order && $order->stato_pagamento !== 'pagato') {
                    $order->load('customer');
                    $order->stato_pagamento = 'pagato';
                    $order->save();
                    
                    try {
                        \Illuminate\Support\Facades\Mail::to($order->customer->email)
                            ->bcc(config('mail.from.address'))
                            ->send(new \App\Mail\OrderConfirmed($order));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Errore invio email ordine (stripe): ' . $e->getMessage());
                    }

                    session()->forget('cart');
                    return redirect()->route('public.shop.cart.success')->with('order_number', $order->numero_ordine);
                } else if ($order && $order->stato_pagamento === 'pagato') {
                    // Già pagato (refresh pagina)
                    session()->forget('cart');
                    return redirect()->route('public.shop.cart.success')->with('order_number', $order->numero_ordine);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Stripe check error: ' . $e->getMessage());
        }

        return redirect()->route('public.shop.index')->with('error', 'Si è verificato un errore durante la verifica del pagamento.');
    }

    public function stripeCancel(Request $request)
    {
        $order_id = $request->get('order_id');
        if ($order_id) {
            $order = \App\Models\ShopOrder::find($order_id);
            if ($order && $order->stato_pagamento === 'attesa') {
                $order->stato = 'annullato'; // o mark as unpaid/cart
                $order->save();
                
                // Ripristino magazzino (optional)
                foreach($order->items as $item) {
                     if($item->shop_variant_id) {
                         $variant = \App\Models\ShopVariant::find($item->shop_variant_id);
                         if($variant) {
                             $variant->quantita += $item->quantita;
                             $variant->save();
                         }
                     }
                }
            }
        }
        
        return redirect()->route('public.shop.cart.checkout')->with('error', 'Hai annullato il pagamento. L\'ordine non è stato completato.');
    }

    public function paypalSuccess(Request $request)
    {
        $paypalOrderId = $request->get('token');
        if (!$paypalOrderId) {
            return redirect()->route('public.home');
        }

        try {
            $clientId = \App\Models\Setting::where('key', 'paypal_client_id')->value('value');
            $secret = \App\Models\Setting::where('key', 'paypal_secret')->value('value');
            $mode = \App\Models\Setting::where('key', 'paypal_mode')->value('value') ?: 'sandbox';
            $baseUrl = ($mode === 'live') ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

            // 1. Get Access Token Again
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl . '/v1/oauth2/token');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $accessToken = json_decode($result)->access_token;
            curl_close($ch);

            // 2. Capture Order
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl . '/v2/checkout/orders/' . $paypalOrderId . '/capture');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ]);
            $result = curl_exec($ch);
            $capture = json_decode($result);
            curl_close($ch);

            if (isset($capture->status) && $capture->status === 'COMPLETED') {
                $dbOrderId = $capture->purchase_units[0]->reference_id;
                $order = \App\Models\ShopOrder::find($dbOrderId);
                
                if ($order && $order->stato_pagamento !== 'pagato') {
                    $order->load('customer');
                    $order->stato_pagamento = 'pagato';
                    $order->save();
                    
                    try {
                        \Illuminate\Support\Facades\Mail::to($order->customer->email)
                            ->bcc(config('mail.from.address'))
                            ->send(new \App\Mail\OrderConfirmed($order));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Errore invio email ordine (paypal): ' . $e->getMessage());
                    }

                    session()->forget('cart');
                    return redirect()->route('public.shop.cart.success')->with('order_number', $order->numero_ordine);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PayPal capture error: ' . $e->getMessage());
        }

        return redirect()->route('public.shop.index')->with('error', 'Si è verificato un errore durante la cattura del pagamento PayPal.');
    }

    public function paypalCancel(Request $request)
    {
        return $this->stripeCancel($request); // Reuse same logic for stock restoration and redirect
    }
}
