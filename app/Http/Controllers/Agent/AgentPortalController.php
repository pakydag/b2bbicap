<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\B2bProduct;
use App\Models\B2bOrder;
use App\Models\B2bCustomer;
use App\Models\B2bBrand;

class AgentPortalController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role === 'customer') {
            $query = B2bOrder::where('b2b_customer_id', $user->b2b_customer_id);
        } else {
            $customerIds = $user->b2bCustomers()->pluck('b2b_customers.id');
            $query = B2bOrder::where(function($q) use ($user, $customerIds) {
                $q->where('agent_id', $user->id)
                  ->orWhereIn('b2b_customer_id', $customerIds);
            });
        }

        $stats = [
            'orders_count' => (clone $query)->count(),
            'pending_orders' => (clone $query)->where('status', 'pending')->count(),
            'total_volume' => (clone $query)->where('status', 'confirmed')->sum('total_amount'),
        ];
        
        $recent_orders = (clone $query)->with('customer')->latest()->take(5)->get();
        
        return view('agent.dashboard', compact('stats', 'recent_orders'));
    }

    public function catalog(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'customer') {
            $brandIds = \App\Models\B2bBrand::pluck('id');
            $authorizedBrands = \App\Models\B2bBrand::all();
            $allAgentProducts = B2bProduct::where('is_active', true)->get();
        } else {
            $user->load('b2bBrands');
            $brandIds = $user->b2bBrands->pluck('id');
            $authorizedBrands = $user->b2bBrands;
            $allAgentProducts = B2bProduct::whereIn('b2b_brand_id', $brandIds)->where('is_active', true)->get();
        }
        
        $filterOptions = [
            'settori' => [],
            'tipologie' => [],
            'categorie' => [],
            'materiali' => [],
            'puntali' => [],
            'suole' => [],
            'calzate' => [],
            'norme' => [],
        ];
        
        foreach ($allAgentProducts as $p) {
            $c = $p->characteristics ?? [];
            if (!empty($c['SETTORE-DI-UTILIZZO-IT'])) {
                $parts = array_map('trim', explode('/', $c['SETTORE-DI-UTILIZZO-IT']));
                foreach ($parts as $part) {
                    if ($part !== '') $filterOptions['settori'][$part] = true;
                }
            }
            if (!empty($c['TIPOLOGIA-FILTRO-IT'])) $filterOptions['tipologie'][trim($c['TIPOLOGIA-FILTRO-IT'])] = true;
            if (!empty($c['CAT-SICUREZZA-FILTRO'])) $filterOptions['categorie'][trim($c['CAT-SICUREZZA-FILTRO'])] = true;
            if (!empty($c['MATERIALE-FILTRO-IT'])) $filterOptions['materiali'][trim($c['MATERIALE-FILTRO-IT'])] = true;
            if (!empty($c['PUNTALE-FILTRO-IT'])) $filterOptions['puntali'][trim($c['PUNTALE-FILTRO-IT'])] = true;
            if (!empty($c['SUOLA-FILTRO-IT'])) $filterOptions['suole'][trim($c['SUOLA-FILTRO-IT'])] = true;
            if (!empty($c['CALZATA'])) $filterOptions['calzate'][trim($c['CALZATA'])] = true;
            if (!empty($c['NORMA'])) $filterOptions['norme'][trim($c['NORMA'])] = true;
        }
        
        foreach ($filterOptions as $key => &$values) {
            $values = array_keys($values);
            sort($values);
        }
        
        // 2. Applica i filtri alla query
        $query = B2bProduct::whereIn('b2b_brand_id', $brandIds)->where('is_active', true);
        
        if ($request->filled('brands')) {
            $query->whereIn('b2b_brand_id', $request->brands);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('settori')) {
            $query->where(function($q) use ($request) {
                foreach ($request->settori as $sector) {
                    $q->orWhere('characteristics->SETTORE-DI-UTILIZZO-IT', 'like', '%' . $sector . '%');
                }
            });
        }
        
        if ($request->filled('tipologie')) {
            $query->whereIn('characteristics->TIPOLOGIA-FILTRO-IT', $request->tipologie);
        }
        
        if ($request->filled('categorie')) {
            $query->whereIn('characteristics->CAT-SICUREZZA-FILTRO', $request->categorie);
        }
        
        if ($request->filled('materiali')) {
            $query->whereIn('characteristics->MATERIALE-FILTRO-IT', $request->materiali);
        }
        
        if ($request->filled('puntali')) {
            $query->whereIn('characteristics->PUNTALE-FILTRO-IT', $request->puntali);
        }
        
        if ($request->filled('suole')) {
            $query->whereIn('characteristics->SUOLA-FILTRO-IT', $request->suole);
        }
        
        if ($request->filled('calzate')) {
            $query->whereIn('characteristics->CALZATA', $request->calzate);
        }
        
        if ($request->filled('norme')) {
            $query->whereIn('characteristics->NORMA', $request->norme);
        }
        
        $products = $query->with('brand', 'variants')->get();
        
        $customer = $this->getSelectedCustomer($request);
        $giacenzaData = $this->getGiacenzaData();
        $products->each(function($p) use ($giacenzaData, $customer) {
            $match = $this->findGiacenzaMatch($p, $giacenzaData);
            if ($match) {
                $p->is_synchronized = true;
                $p->giacenza_total = $match['total_qty'];
            } else {
                $p->is_synchronized = false;
                $p->giacenza_total = 0;
            }

            $p->price_details = $p->getPriceDetailsForCustomer($customer, 1);
            $p->calculated_price = $p->price_details['unit_price'];
        });
        
        return view('agent.catalog', compact('products', 'authorizedBrands', 'filterOptions', 'customer'));
    }

    protected function getSelectedCustomer(Request $request = null)
    {
        $user = auth()->user();
        if (!$user) return null;

        if ($user->role === 'customer') {
            return $user->b2bCustomer;
        }

        if ($user->role === 'agent') {
            $customerId = ($request ? $request->input('b2b_customer_id') : null)
                ?? session('b2b_selected_customer_id');

            if ($customerId) {
                session()->put('b2b_selected_customer_id', $customerId);
                return B2bCustomer::find($customerId);
            }
        }

        return null;
    }

    public function product(B2bProduct $product)
    {
        $user = auth()->user();
        if ($user->role === 'agent') {
            if (!$user->b2bBrands->contains($product->b2b_brand_id)) {
                abort(403);
            }
        }
        
        $product->load('brand', 'variants');
        
        $customer = $this->getSelectedCustomer();
        $priceDetails = $product->getPriceDetailsForCustomer($customer, 1);

        $giacenzaData = $this->getGiacenzaData();
        $giacenzaMatch = $this->findGiacenzaMatch($product, $giacenzaData);
        
        return view('agent.product', compact('product', 'giacenzaMatch', 'priceDetails', 'customer'));
    }

    public function productVariant(B2bProduct $product)
    {
        $user = auth()->user();
        if ($user->role === 'agent') {
            if (!$user->b2bBrands->contains($product->b2b_brand_id)) {
                abort(403);
            }
        }
        
        $product->load('brand', 'variants');
        
        $customer = $this->getSelectedCustomer();
        $priceDetails = $product->getPriceDetailsForCustomer($customer, 1);

        $giacenzaData = $this->getGiacenzaData();
        $giacenzaMatch = $this->findGiacenzaMatch($product, $giacenzaData);
        
        return view('agent.product_variant', compact('product', 'giacenzaMatch', 'priceDetails', 'customer'));
    }

    private function recalculateCartPrices(array $cart, $customer)
    {
        $groupedQuantities = [];
        foreach ($cart as $item) {
            $key = empty($item['delivery_date']) ? 'immediate' : $item['delivery_date'];
            $groupedQuantities[$key] = ($groupedQuantities[$key] ?? 0) + $item['quantity'];
        }

        foreach ($cart as $index => $item) {
            if (!empty($item['product_id'])) {
                $product = B2bProduct::find($item['product_id']);
                if ($product) {
                    $key = empty($item['delivery_date']) ? 'immediate' : $item['delivery_date'];
                    $groupQuantity = $groupedQuantities[$key] ?? 0;
                    
                    // Prezzo scontato in base alla quantità
                    $details = $product->getPriceDetailsForCustomer($customer, $groupQuantity);
                    $cart[$index]['price'] = $details['unit_price'];
                    $cart[$index]['original_price'] = $details['original_price'];
                }
            }
        }
        return $cart;
    }

    public function cart()
    {
        $customer = $this->getSelectedCustomer();
        $cart = session()->get('b2b_cart', []);
        
        $newCart = $this->recalculateCartPrices($cart, $customer);
        
        $updated = false;
        foreach ($cart as $index => $item) {
            if (($item['price'] ?? 0) != ($newCart[$index]['price'] ?? 0)) {
                $updated = true;
                break;
            }
        }
        $cart = $newCart;

        if ($updated) {
            session()->put('b2b_cart', $cart);
            $this->persistCart($cart);
        }

        $giacenzaData = $this->getGiacenzaData();
        foreach ($cart as $index => &$item) {
            $item['available_qty'] = 0;
            if (!empty($item['product_id'])) {
                $product = B2bProduct::find($item['product_id']);
                if ($product) {
                    $match = $this->findGiacenzaMatch($product, $giacenzaData);
                    if ($match) {
                        $size = $item['size'] ?? '';
                        $deliveryDate = $item['delivery_date'] ?? null;
                        if (empty($deliveryDate)) {
                            $item['available_qty'] = $match['current_stock'][$size] ?? 0;
                        } else {
                            $item['available_qty'] = $match['future_stock'][$deliveryDate][$size] ?? 0;
                        }
                    }
                }
            }
        }
        unset($item);

        $user = auth()->user();
        $customers = $user->role === 'customer' 
            ? collect() 
            : $user->b2bCustomers()->orderBy('business_name')->get();
        return view('agent.cart', compact('cart', 'customers', 'customer'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'items' => 'nullable|array',
            'items.*.variant_id' => 'nullable|exists:b2b_product_variants,id',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.delivery_date' => 'nullable|string',
            'variants' => 'nullable|array',
        ]);

        $customer = $this->getSelectedCustomer($request);
        $cart = session()->get('b2b_cart', []);
        $addedCount = 0;
        $warningMessage = null;
        $giacenzaData = $this->getGiacenzaData();

        $validateStock = function($variant, $deliveryDate, $qtyRequested, $currentCartQty) use ($giacenzaData, &$warningMessage) {
            $match = $this->findGiacenzaMatch($variant->product, $giacenzaData);
            if (!$match) return $qtyRequested;
            
            $size = $variant->size ?? '';
            $availableQty = 0;
            
            if (empty($deliveryDate)) {
                $availableQty = $match['current_stock'][$size] ?? 0;
            } else {
                $availableQty = $match['future_stock'][$deliveryDate][$size] ?? 0;
            }
            
            $totalRequestedQty = $currentCartQty + $qtyRequested;
            
            if ($totalRequestedQty > $availableQty) {
                $qtyToAdd = $availableQty - $currentCartQty;
                if ($qtyToAdd < 0) $qtyToAdd = 0;
                
                $warningMessage = "Una o più quantità richieste superavano la giacenza e sono state ridotte al massimo disponibile.";
                return $qtyToAdd;
            }
            
            return $qtyRequested;
        };

        // Nuovo formato items con data di consegna
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemInput) {
                $qty = (int)($itemInput['quantity'] ?? 0);
                if ($qty <= 0) continue;

                $variantId = $itemInput['variant_id'] ?? null;
                if (!$variantId) continue;
                
                $deliveryDate = !empty($itemInput['delivery_date']) ? $itemInput['delivery_date'] : null;

                $variant = \App\Models\B2bProductVariant::with('product.brand')->find($variantId);
                if (!$variant) continue;
                
                $currentCartQty = 0;
                foreach ($cart as $item) {
                    if ($item['variant_id'] == $variantId && ($item['delivery_date'] ?? null) === $deliveryDate) {
                        $currentCartQty = $item['quantity'];
                        break;
                    }
                }
                
                $qty = $validateStock($variant, $deliveryDate, $qty, $currentCartQty);
                if ($qty <= 0) continue;

                $priceDetails = $variant->product->getPriceDetailsForCustomer($customer, $qty);
                $unitPrice = $priceDetails['unit_price'];

                // Cerca se esiste già nel carrello stessa variante + stessa data consegna
                $found = false;
                foreach ($cart as $index => $item) {
                    if ($item['variant_id'] == $variantId && ($item['delivery_date'] ?? null) === $deliveryDate) {
                        $cart[$index]['quantity'] += $qty;
                        // Ricalcola prezzo per nuova quantità cumulativa
                        $newDetails = $variant->product->getPriceDetailsForCustomer($customer, $cart[$index]['quantity']);
                        $cart[$index]['price'] = $newDetails['unit_price'];
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $cart[] = [
                        'variant_id' => $variant->id,
                        'product_id' => $variant->b2b_product_id,
                        'name' => $variant->product->name,
                        'brand' => $variant->product->brand->name,
                        'size' => $variant->size,
                        'color' => $variant->color,
                        'price' => $unitPrice,
                        'quantity' => $qty,
                        'delivery_date' => $deliveryDate,
                    ];
                }
                $addedCount++;
            }
        } 
        // Fallback per vecchio formato
        elseif ($request->has('variants') && is_array($request->variants)) {
            foreach ($request->variants as $varKey => $variantInput) {
                $qty = (int)($variantInput['quantity'] ?? 0);
                if ($qty <= 0) continue;

                $actualVariantId = $variantInput['id'] ?? $varKey;
                $variant = \App\Models\B2bProductVariant::with('product.brand')->find($actualVariantId);
                if (!$variant) continue;
                
                $currentCartQty = 0;
                foreach ($cart as $item) {
                    if ($item['variant_id'] == $actualVariantId && empty($item['delivery_date'])) {
                        $currentCartQty = $item['quantity'];
                        break;
                    }
                }
                
                $qty = $validateStock($variant, null, $qty, $currentCartQty);
                if ($qty <= 0) continue;

                $priceDetails = $variant->product->getPriceDetailsForCustomer($customer, $qty);
                $unitPrice = $priceDetails['unit_price'];

                $found = false;
                foreach ($cart as $index => $item) {
                    if ($item['variant_id'] == $actualVariantId && empty($item['delivery_date'])) {
                        $cart[$index]['quantity'] += $qty;
                        $newDetails = $variant->product->getPriceDetailsForCustomer($customer, $cart[$index]['quantity']);
                        $cart[$index]['price'] = $newDetails['unit_price'];
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $cart[] = [
                        'variant_id' => $variant->id,
                        'product_id' => $variant->b2b_product_id,
                        'name' => $variant->product->name,
                        'brand' => $variant->product->brand->name,
                        'size' => $variant->size,
                        'color' => $variant->color,
                        'price' => $unitPrice,
                        'quantity' => $qty,
                        'delivery_date' => null,
                    ];
                }
                $addedCount++;
            }
        }

        if ($addedCount > 0) {
            // Ricalcola sconti post-add
            $cart = $this->recalculateCartPrices($cart, $customer);

            session()->put('b2b_cart', $cart);
            $this->persistCart($cart);

            if ($warningMessage) {
                return redirect()->route('agent.cart')->with('warning', $warningMessage);
            }

            return redirect()->route('agent.cart')->with('success', 'Prodotti aggiunti al carrello.');
        }

        return redirect()->back()->with('error', 'Nessuna quantità inserita.');
    }

    public function removeFromCart($index)
    {
        $cart = session()->get('b2b_cart', []);
        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart);
            
            // Ricalcola i prezzi per l'intero carrello usando le quantità per gruppo di consegna
            $customer = $this->getSelectedCustomer();
            $cart = $this->recalculateCartPrices($cart, $customer);

            session()->put('b2b_cart', $cart);
            $this->persistCart($cart);
        }
        return redirect()->back()->with('success', 'Rimosso dal carrello.');
    }

    public function updateCart(Request $request)
    {
        $cart = session()->get('b2b_cart', []);

        $giacenzaData = $this->getGiacenzaData();
        $warningMessage = null;

        $checkAndUpdateQty = function(&$item, $newQty) use ($giacenzaData, &$warningMessage) {
            if ($newQty <= 0) {
                $item['quantity'] = 0;
                return;
            }
            
            $product = B2bProduct::find($item['product_id']);
            if (!$product) {
                $item['quantity'] = $newQty;
                return;
            }
            
            $match = $this->findGiacenzaMatch($product, $giacenzaData);
            if ($match) {
                $size = $item['size'] ?? '';
                $deliveryDate = $item['delivery_date'] ?? null;
                $availableQty = 0;
                
                if (empty($deliveryDate)) {
                    $availableQty = $match['current_stock'][$size] ?? 0;
                } else {
                    $availableQty = $match['future_stock'][$deliveryDate][$size] ?? 0;
                }
                
                if ($newQty > $availableQty) {
                    $newQty = $availableQty;
                    $warningMessage = "La quantità richiesta per {$product->name} (Taglia $size) supera la giacenza ($availableQty pz).";
                }
            }
            $item['quantity'] = $newQty;
        };

        if ($request->has('index')) {
            $index = $request->input('index');
            if (isset($cart[$index])) {
                $action = $request->input('action');
                if ($action === 'increase') {
                    $checkAndUpdateQty($cart[$index], $cart[$index]['quantity'] + 1);
                } elseif ($action === 'decrease') {
                    if ($cart[$index]['quantity'] > 1) {
                        $cart[$index]['quantity'] -= 1;
                    }
                } elseif ($request->has('quantity')) {
                    $checkAndUpdateQty($cart[$index], (int)$request->input('quantity'));
                }
            }
        } elseif ($request->has('quantities') && is_array($request->quantities)) {
            foreach ($request->quantities as $index => $qty) {
                if (isset($cart[$index])) {
                    $checkAndUpdateQty($cart[$index], (int)$qty);
                }
            }
        }

        // Rimuovi eventuali prodotti a cui è stata azzerata la quantità (es. per mancanza di giacenza)
        $cart = array_filter($cart, fn($item) => $item['quantity'] > 0);
        $cart = array_values($cart);

        // Ricalcola i prezzi per ogni riga in base al cliente ed alla nuova quantità per gruppo
        $customer = $this->getSelectedCustomer($request);
        $cart = $this->recalculateCartPrices($cart, $customer);

        session()->put('b2b_cart', $cart);
        $this->persistCart($cart);

        if ($warningMessage) {
            return redirect()->back()->with('warning', $warningMessage);
        }

        return redirect()->back()->with('success', 'Quantità e prezzi nel carrello aggiornati.');
    }

    public function checkout()
    {
        $cart = session()->get('b2b_cart', []);
        if (empty($cart)) {
            return redirect()->route('agent.catalog')->with('error', 'Il carrello è vuoto.');
        }
        
        $customers = B2bCustomer::all();
        return view('agent.checkout', compact('cart', 'customers'));
    }

    public function processCheckout(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'customer') {
            $customerId = $user->b2b_customer_id;
            if (!$customerId) {
                return redirect()->back()->with('error', 'Il tuo account non è associato ad alcuna azienda B2B.');
            }
        } else {
            $request->validate([
                'b2b_customer_id' => [
                    'required',
                    \Illuminate\Validation\Rule::exists('agent_customer', 'b2b_customer_id')->where('user_id', $user->id)
                ],
            ]);
            $customerId = $request->b2b_customer_id;
        }

        $request->validate([
            'notes' => 'nullable|string',
            'delivery_group' => 'required|string',
        ]);

        $cart = session()->get('b2b_cart', []);
        if (empty($cart)) return redirect()->route('agent.catalog');

        $deliveryGroup = $request->input('delivery_group');

        $b2bCustomer = B2bCustomer::find($customerId);

        // Se l'ordine è inviato direttamente dal cliente, assegna l'agent_id dell'agente associato all'azienda
        if ($user->role === 'agent') {
            $agentId = $user->id;
        } else {
            $assignedAgent = $b2bCustomer ? $b2bCustomer->agents()->where('role', 'agent')->first() : null;
            $agentId = $assignedAgent ? $assignedAgent->id : null;
        }

        // 2. Sync with Unified CRM Contact
        $contactId = null;
        if ($b2bCustomer) {
            $contact = \App\Models\Contact::where('email', $b2bCustomer->email)->first();
            if (!$contact) {
                $contact = new \App\Models\Contact();
                $contact->email = $b2bCustomer->email ?? "b2b_" . uniqid() . "@example.com";
            }
            $contact->first_name = $contact->first_name ?? $b2bCustomer->contact_name;
            $contact->last_name = $contact->last_name ?? $b2bCustomer->contact_surname;
            $contact->company_name = $contact->company_name ?? $b2bCustomer->business_name;
            $contact->vat_number = $contact->vat_number ?? $b2bCustomer->vat_number;
            $contact->phone = $contact->phone ?? $b2bCustomer->phone;
            $contact->is_b2b_customer = true;
            $contact->save();
            
            $contactId = $contact->id;
        }

        // Raggruppa i prodotti per data di consegna
        $groupedCart = [];
        $totalCartQuantity = 0;
        foreach ($cart as $index => $item) {
            $key = empty($item['delivery_date']) ? 'immediate' : $item['delivery_date'];
            $groupedCart[$key][$index] = $item;
            $totalCartQuantity += $item['quantity'];
        }

        if (!isset($groupedCart[$deliveryGroup])) {
            return redirect()->back()->with('error', 'L\'ordine selezionato non è più presente nel carrello.');
        }

        $itemsToProcess = $groupedCart[$deliveryGroup];
        
        $order = B2bOrder::create([
            'agent_id' => $agentId,
            'b2b_customer_id' => $customerId,
            'status' => 'pending',
            'notes' => $request->notes,
            'total_amount' => 0,
        ]);

        if ($contactId) {
            $order->update(['contact_id' => $contactId]);
        }

        $total = 0;
        $groupQuantity = array_sum(array_column($itemsToProcess, 'quantity'));

        foreach ($itemsToProcess as $index => $item) {
            $product = B2bProduct::find($item['product_id']);
            $finalUnitPrice = $item['price'];
            if ($product) {
                // Calcoliamo lo sconto basandoci sulla quantità del gruppo di consegna
                $details = $product->getPriceDetailsForCustomer($b2bCustomer, $groupQuantity);
                $finalUnitPrice = $details['unit_price'];
            }

            $order->items()->create([
                'b2b_product_id' => $item['product_id'],
                'b2b_product_variant_id' => $item['variant_id'],
                'quantity' => $item['quantity'],
                'price' => $finalUnitPrice,
                'original_quantity' => $item['quantity'],
                'original_price' => $finalUnitPrice,
                'is_modified' => false,
                'delivery_date' => $item['delivery_date'] ?? null,
            ]);
            $total += $finalUnitPrice * $item['quantity'];
            
            // Rimuoviamo l'articolo processato dal carrello principale
            unset($cart[$index]);
        }

        $order->update(['total_amount' => $total]);
        
        // Genera ed invia il file CSV dell'ordine nella cartella Input su FTP
        $this->exportOrderToFtpCsv($order);

        // Aggiorniamo la sessione con i restanti prodotti non inviati
        $cart = array_values($cart);
        session()->put('b2b_cart', $cart);
        $this->persistCart($cart);

        return redirect()->route('agent.orders')->with('success', 'Ordine #' . $order->id . ' inviato correttamente all\'amministrazione ed inviato su FTP.');
    }

    public function orders()
    {
        $user = auth()->user();
        if ($user->role === 'customer') {
            $orders = B2bOrder::where('b2b_customer_id', $user->b2b_customer_id)->with('customer')->latest()->get();
        } else {
            $customerIds = $user->b2bCustomers()->pluck('b2b_customers.id');
            $orders = B2bOrder::where(function($q) use ($user, $customerIds) {
                $q->where('agent_id', $user->id)
                  ->orWhereIn('b2b_customer_id', $customerIds);
            })->with('customer')->latest()->get();
        }
        return view('agent.orders', compact('orders'));
    }

    public function orderDetail(B2bOrder $order)
    {
        $user = auth()->user();
        if ($user->role === 'customer') {
            if ($order->b2b_customer_id !== $user->b2b_customer_id) abort(403);
        } else {
            $customerIds = $user->b2bCustomers()->pluck('b2b_customers.id');
            if ($order->agent_id !== $user->id && !$customerIds->contains($order->b2b_customer_id)) {
                abort(403);
            }
        }
        $order->load('customer', 'items.product', 'items.variant');
        return view('agent.order_detail', compact('order'));
    }

    public function orderPdf(B2bOrder $order)
    {
        $user = auth()->user();
        if ($user->role === 'customer') {
            if ($order->b2b_customer_id !== $user->b2b_customer_id) abort(403);
        } else {
            $customerIds = $user->b2bCustomers()->pluck('b2b_customers.id');
            if ($order->agent_id !== $user->id && !$customerIds->contains($order->b2b_customer_id)) {
                abort(403);
            }
        }
        $order->load('agent', 'customer', 'items.product', 'items.variant');
        return view('admin.b2b.orders.pdf', compact('order'));
    }

    public function updateOrderItems(Request $request, B2bOrder $order)
    {
        $user = auth()->user();
        if ($user->role === 'customer') {
            abort(403, 'I clienti non possono modificare gli ordini inviati.');
        }

        $customerIds = $user->b2bCustomers()->pluck('b2b_customers.id');
        if ($user->role === 'agent' && $order->agent_id !== $user->id && !$customerIds->contains($order->b2b_customer_id)) {
            abort(403);
        }

        if ($order->status === 'confirmed') {
            return redirect()->back()->with('error', 'Un ordine già confermato ed inviato alla sede non può più essere modificato.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $order->load('customer');
        $orderModified = false;

        foreach ($request->items as $itemId => $itemData) {
            $orderItem = \App\Models\B2bOrderItem::where('b2b_order_id', $order->id)->where('id', $itemId)->first();
            if ($orderItem) {
                $origQty = $orderItem->original_quantity ?? $orderItem->quantity;
                $origPrice = $orderItem->original_price ?? $orderItem->price;

                $newQty = (int)$itemData['quantity'];
                $inputPrice = floatval($itemData['price']);
                $oldQty = (int)$orderItem->quantity;
                $oldPrice = (float)$orderItem->price;

                $finalPrice = $inputPrice;

                // Se la quantità è cambiata, ricalcola il prezzo da listino se l'agente non ha inserito un prezzo manuale personalizzato
                if ($newQty !== $oldQty) {
                    $product = B2bProduct::find($orderItem->b2b_product_id);
                    if ($product && $order->customer) {
                        $expectedOldPrice = $product->getPriceDetailsForCustomer($order->customer, $oldQty)['unit_price'];
                        $expectedNewPrice = $product->getPriceDetailsForCustomer($order->customer, $newQty)['unit_price'];

                        if (abs($inputPrice - $oldPrice) < 0.001 || abs($inputPrice - $expectedOldPrice) < 0.001) {
                            $finalPrice = $expectedNewPrice;
                        }
                    }
                }

                $itemIsModified = ($newQty !== (int)$origQty) || (abs($finalPrice - (float)$origPrice) >= 0.01);
                if ($itemIsModified) {
                    $orderModified = true;
                }

                $orderItem->update([
                    'original_quantity' => $origQty,
                    'original_price' => $origPrice,
                    'quantity' => $newQty,
                    'price' => $finalPrice,
                    'is_modified' => $itemIsModified,
                ]);
            }
        }

        if ($request->has('delete_items') && is_array($request->delete_items) && count($request->delete_items) > 0) {
            \App\Models\B2bOrderItem::where('b2b_order_id', $order->id)->whereIn('id', $request->delete_items)->delete();
            $orderModified = true;
        }

        $totalAmount = \App\Models\B2bOrderItem::where('b2b_order_id', $order->id)->get()->sum(fn($i) => $i->quantity * $i->price);
        $hasAnyModifiedItem = \App\Models\B2bOrderItem::where('b2b_order_id', $order->id)->where('is_modified', true)->exists();

        $isMod = $orderModified || $hasAnyModifiedItem;

        $order->update([
            'total_amount' => $totalAmount,
            'is_modified' => $isMod,
            'status' => $isMod ? 'revision_pending' : $order->status,
        ]);

        // Genera ed invia il file CSV aggiornato dell'ordine nella cartella Input su FTP
        $this->exportOrderToFtpCsv($order);

        return redirect()->back()->with('success', 'Quantità e prezzi dell\'Ordine #' . $order->id . ' aggiornati con successo ed inviati via FTP! Ordine posto in attesa di approvazione del cliente.');
    }

    public function acceptOrderModifications(B2bOrder $order)
    {
        $user = auth()->user();
        if ($user->role !== 'customer' || $order->b2b_customer_id !== $user->b2b_customer_id) {
            abort(403, 'Non sei autorizzato ad accettare questo ordine.');
        }

        $order->update(['status' => 'customer_approved']);

        // Genera ed invia il file CSV dell'ordine nella cartella Input su FTP
        $csvResult = $this->exportOrderToFtpCsv($order);

        $msg = 'Hai accettato le modifiche dell\'Ordine #' . $order->id . '. L\'ordine è in attesa di OK finale dall\'agente/amministrazione.';
        if ($csvResult['uploaded']) {
            $msg .= ' Il file CSV dell\'ordine (' . $csvResult['filename'] . ') è stato inviato al magazzino FTP (Input).';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function rejectOrderModifications(B2bOrder $order)
    {
        $user = auth()->user();
        if ($user->role !== 'customer' || $order->b2b_customer_id !== $user->b2b_customer_id) {
            abort(403, 'Non sei autorizzato a rifiutare questo ordine.');
        }

        $order->update(['status' => 'customer_rejected']);

        return redirect()->back()->with('success', 'Hai rifiutato le modifiche dell\'Ordine #' . $order->id . '. L\'agente è stato notificato.');
    }

    public function confirmOrder(B2bOrder $order)
    {
        $user = auth()->user();
        if ($user->role === 'customer') {
            abort(403, 'I clienti non possono dare l\'OK finale agli ordini.');
        }

        $customerIds = $user->b2bCustomers()->pluck('b2b_customers.id');
        if ($user->role === 'agent' && $order->agent_id !== $user->id && !$customerIds->contains($order->b2b_customer_id)) {
            abort(403);
        }

        if ($order->status === 'confirmed') {
            return redirect()->back()->with('error', 'Questo ordine è già stato confermato.');
        }

        // VERIFICA DISPONIBILITÀ DI MAGAZZINO REALI PRIMA DELL'OK FINALE
        $giacenzaData = $this->getGiacenzaData();
        $order->load('items.product', 'items.variant');

        $insufficientItems = [];

        foreach ($order->items as $item) {
            $product = $item->product;
            $variant = $item->variant;
            if (!$product || !$variant) continue;

            $size = trim($variant->size);
            $reqQty = (int)$item->quantity;
            $deliveryDate = !empty($item->delivery_date) ? trim($item->delivery_date) : null;

            $match = $this->findGiacenzaMatch($product, $giacenzaData);

            if (!$match) {
                $insufficientItems[] = "{$product->name} (Taglia {$size}): Articolo non sincronizzato col magazzino.";
                continue;
            }

            if (empty($deliveryDate)) {
                $availableStock = (int)($match['current_stock'][$size] ?? 0);
                if ($reqQty > $availableStock) {
                    $insufficientItems[] = "{$product->name} (Taglia {$size}): Richiesti {$reqQty} pz, Disponibilità immediata in magazzino {$availableStock} pz.";
                }
            } else {
                $availableStock = (int)($match['future_stock'][$deliveryDate][$size] ?? 0);
                if ($reqQty > $availableStock) {
                    $insufficientItems[] = "{$product->name} (Taglia {$size}): Richiesti {$reqQty} pz, Disponibilità per la data {$deliveryDate}: {$availableStock} pz.";
                }
            }
        }

        if (!empty($insufficientItems)) {
            $errorMessage = "Impossibile registrare l'OK finale per l'Ordine #" . $order->id . " a causa di giacenze di magazzino insufficienti: " . implode(" | ", $insufficientItems);
            return redirect()->back()->with('error', $errorMessage);
        }

        $order->update(['status' => 'confirmed']);

        // Genera ed invia il file CSV dell'ordine nella cartella Input su FTP
        $csvResult = $this->exportOrderToFtpCsv($order);

        $msg = 'OK FINALE REGISTRATO! Giacenze verificate e Ordine #' . $order->id . ' confermato con successo!';
        if ($csvResult['uploaded']) {
            $msg .= ' File ' . $csvResult['filename'] . ' trasmesso nella cartella Input dell\'FTP.';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function exportOrderToFtpCsv(B2bOrder $order)
    {
        $order->load('customer', 'items.product', 'items.variant');
        
        $filename = "{$order->id}.csv";
        $storageDir = storage_path('app/orders');
        if (!file_exists($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }
        $localPath = $storageDir . '/' . $filename;
        
        $file = fopen($localPath, 'w');
        // Intestazione in linea con il formato Giacenza.csv
        fputcsv($file, ['CODICE_ARTICOLO', 'DESCRIZIONE', 'TAGLIA', 'QUANTITA', 'DATA_CONSEGNA', 'PREZZO_UNITARIO', 'CLIENTE', 'NUMERO_ORDINE'], ';');
        
        foreach ($order->items as $item) {
            $code = $item->product ? ($item->product->code ?? $item->product->name) : 'N/D';
            $name = $item->product ? $item->product->name : 'N/D';
            $size = $item->variant ? $item->variant->size : '';
            $qty = $item->quantity;
            $deliveryDate = !empty($item->delivery_date) ? $item->delivery_date : 'Pronta consegna';
            $price = number_format($item->price, 2, '.', '');
            $customerName = $order->customer ? $order->customer->business_name : 'N/D';
            
            fputcsv($file, [$code, $name, $size, $qty, $deliveryDate, $price, $customerName, $order->id], ';');
        }
        fclose($file);

        $uploaded = false;
        $host = env('FTPS_GIACENZE_HOST', '51.75.145.169');
        $username = env('FTPS_GIACENZE_USERNAME', 'bicapb2b');
        $password = env('FTPS_GIACENZE_PASSWORD', 'lB24RiL=^D');
        $remotePath = "Input/{$filename}";

        try {
            $conn = @ftp_ssl_connect($host, 21, 15);
            if (!$conn) {
                $conn = @ftp_connect($host, 21, 15);
            }
            if ($conn) {
                if (@ftp_login($conn, $username, $password)) {
                    ftp_pasv($conn, true);
                    $uploadOk = @ftp_put($conn, $remotePath, $localPath, FTP_BINARY);
                    if ($uploadOk) {
                        $uploaded = true;
                        \Illuminate\Support\Facades\Log::info("[ExportOrderCsv] File {$remotePath} inviato con successo su FTP per Ordine #{$order->id}");
                    } else {
                        \Illuminate\Support\Facades\Log::error("[ExportOrderCsv] Fallito il caricamento FTP del file {$remotePath}");
                    }
                }
                ftp_close($conn);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("[ExportOrderCsv] Errore invio FTP per Ordine #{$order->id}: " . $e->getMessage());
        }

        return [
            'local_path' => $localPath,
            'filename' => $filename,
            'uploaded' => $uploaded,
        ];
    }

    public function profile()
    {
        return view('agent.profile', ['user' => auth()->user()]);
    }

    public function getGiacenzaData()
    {
        $csvPath = base_path('Giacenza.csv');
        
        if (!file_exists($csvPath)) {
            return [];
        }
        
        $file = fopen($csvPath, 'r');
        $headers = fgetcsv($file, 0, ';');
        
        $data = [];
        while (($row = fgetcsv($file, 0, ';')) !== false) {
            if (count($row) < 4) continue;
            $code = trim($row[0]);
            $name = trim($row[1]);
            $size = trim($row[2]);
            $qty = floatval($row[3]);
            $consegna = trim($row[4] ?? '');
            
            if (empty($code)) continue;
            
            $normCode = $this->normalizeCode($code);
            
            if (!isset($data[$normCode])) {
                $data[$normCode] = [
                    'raw_code' => $code,
                    'name' => $name,
                    'current_stock' => [], // sizes with no consegna
                    'future_stock' => [],  // date => [ size => qty ]
                    'total_qty' => 0
                ];
            }
            
            if (empty($consegna)) {
                if (!isset($data[$normCode]['current_stock'][$size])) {
                    $data[$normCode]['current_stock'][$size] = 0;
                }
                $data[$normCode]['current_stock'][$size] += $qty;
            } else {
                if (!isset($data[$normCode]['future_stock'][$consegna][$size])) {
                    $data[$normCode]['future_stock'][$consegna][$size] = 0;
                }
                $data[$normCode]['future_stock'][$consegna][$size] += $qty;
            }
            $data[$normCode]['total_qty'] += $qty;
        }
        fclose($file);
        return $data;
    }

    public function normalizeCode($code)
    {
        $code = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $code));
        if (str_starts_with($code, 'EXP')) {
            $code = substr($code, 3);
        }
        return $code;
    }

    public function findGiacenzaMatch($product, $giacenzaData)
    {
        $dbCode = trim($product->code);
        if (empty($dbCode)) {
            return null;
        }
        $dbNorm = $this->normalizeCode($dbCode);
        
        if (isset($giacenzaData[$dbNorm])) {
            return $giacenzaData[$dbNorm];
        }
        
        foreach ($giacenzaData as $normCode => $data) {
            if (str_starts_with($normCode, $dbNorm) || str_starts_with($dbNorm, $normCode)) {
                return $data;
            }
        }
        
        $cleanedDbName = strtolower(trim(str_replace(['LOW', 'MID', 'ESD'], '', $product->name)));
        if (strlen($cleanedDbName) > 3) {
            foreach ($giacenzaData as $normCode => $data) {
                if (str_contains(strtolower($data['name']), $cleanedDbName)) {
                    return $data;
                }
            }
        }
        
        return null;
    }

    protected function persistCart($cart)
    {
        if (auth()->check()) {
            \App\Models\B2bCart::updateOrCreate(
                ['user_id' => auth()->id()],
                ['cart_data' => $cart]
            );
        }
    }
}
