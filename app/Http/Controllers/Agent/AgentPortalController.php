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

    public function selectCustomer(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'customer') {
            abort(403, 'Azione non consentita per i clienti.');
        }

        $customerId = $request->input('b2b_customer_id');

        if (!empty($customerId)) {
            $customerIds = $user->b2bCustomers()->pluck('b2b_customers.id');
            if ($user->role === 'agent' && !$customerIds->contains($customerId)) {
                abort(403, 'Cliente non assegnato a questo agente.');
            }
            session()->put('b2b_selected_customer_id', $customerId);
            $customer = B2bCustomer::with('priceList')->find($customerId);
        } else {
            session()->forget('b2b_selected_customer_id');
            $customer = null;
        }

        // Ricalcola i prezzi per l'intero carrello con il listino del cliente
        $cart = session()->get('b2b_cart', []);
        if (!empty($cart)) {
            $cart = $this->recalculateCartPrices($cart, $customer);
            session()->put('b2b_cart', $cart);
            $this->persistCart($cart);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'customer_id' => $customerId,
                'customer_name' => $customer ? $customer->business_name : null,
                'price_list_name' => $customer && $customer->b2b_price_list_id ? optional($customer->priceList)->name : null,
                'cart_count' => count($cart),
            ]);
        }

        $msg = $customer 
            ? "Cliente attivo impostato: {$customer->business_name}. I prezzi del catalogo e del carrello sono stati aggiornati al suo listino."
            : "Selezione cliente rimossa. Vengono mostrati i prezzi di listino base.";

        return redirect()->back()->with('success', $msg);
    }

    protected function getSelectedCustomer(?Request $request = null)
    {
        $user = auth()->user();
        if (!$user) return null;

        if ($user->role === 'customer') {
            return $user->b2bCustomer ? $user->b2bCustomer->loadMissing('priceList') : null;
        }

        if ($user->role === 'agent' || $user->role === 'admin') {
            $customerId = ($request ? $request->input('b2b_customer_id') : null)
                ?? session('b2b_selected_customer_id');

            if ($customerId) {
                session()->put('b2b_selected_customer_id', $customerId);
                return B2bCustomer::with('priceList')->find($customerId);
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
        
        $activeCustomer = $customer ?? ($user->role === 'customer' ? $user->b2bCustomer : null);
        $assignedPriceList = null;
        $specificTiers = collect();
        $generalTiers = collect();

        if ($activeCustomer && $activeCustomer->b2b_price_list_id) {
            $assignedPriceList = \App\Models\B2bPriceList::find($activeCustomer->b2b_price_list_id);
            if ($assignedPriceList) {
                $specificTiers = \App\Models\B2bPriceListItem::where('b2b_price_list_id', $assignedPriceList->id)
                    ->where('b2b_product_id', $product->id)
                    ->orderBy('min_quantity', 'asc')
                    ->get();
                $generalTiers = \App\Models\B2bPriceListItem::where('b2b_price_list_id', $assignedPriceList->id)
                    ->whereNull('b2b_product_id')
                    ->orderBy('min_quantity', 'asc')
                    ->get();
            }
        }

        return view('agent.product', compact('product', 'giacenzaMatch', 'priceDetails', 'customer', 'assignedPriceList', 'specificTiers', 'generalTiers'));
    }

    public function productVariant(B2bProduct $product)
    {
        return redirect()->route('agent.product', $product->id);
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
                    $cart[$index]['price_details'] = [
                        'discount_type' => $details['discount_type'],
                        'discount_value' => $details['discount_value'],
                        'discount_2' => $details['discount_2'],
                        'discount_3' => $details['discount_3'],
                        'is_product_exception' => $details['is_product_exception'] ?? false,
                        'price_list_name' => $details['price_list_name'] ?? null,
                        'rule_summary' => $details['rule_summary'] ?? null,
                    ];
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
            if (($item['price'] ?? 0) != ($newCart[$index]['price'] ?? 0) || !isset($item['price_details'])) {
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

        $activeCustomer = $customer ?? ($user->role === 'customer' ? $user->b2bCustomer : null);
        $assignedPriceList = null;
        $generalTiers = collect();
        $productSpecificTiers = collect();

        if ($activeCustomer && $activeCustomer->b2b_price_list_id) {
            $assignedPriceList = \App\Models\B2bPriceList::find($activeCustomer->b2b_price_list_id);
            if ($assignedPriceList) {
                $generalTiers = \App\Models\B2bPriceListItem::where('b2b_price_list_id', $assignedPriceList->id)
                    ->whereNull('b2b_product_id')
                    ->orderBy('min_quantity', 'asc')
                    ->get();
                $productSpecificTiers = \App\Models\B2bPriceListItem::with('product.brand')
                    ->where('b2b_price_list_id', $assignedPriceList->id)
                    ->whereNotNull('b2b_product_id')
                    ->orderBy('b2b_product_id')
                    ->orderBy('min_quantity', 'asc')
                    ->get();
            }
        }

        return view('agent.cart', compact('cart', 'customers', 'customer', 'assignedPriceList', 'generalTiers', 'productSpecificTiers'));
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
                if ($availableQty == 0 && !empty($match['future_stock'])) {
                    $sumFuture = 0;
                    foreach ($match['future_stock'] as $fDate => $fSizes) {
                        $sumFuture += (int)($fSizes[$size] ?? 0);
                    }
                    $availableQty = $sumFuture;
                }
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
                    if ($availableQty == 0 && !empty($match['future_stock'])) {
                        $sumFuture = 0;
                        foreach ($match['future_stock'] as $fDate => $fSizes) {
                            $sumFuture += (int)($fSizes[$size] ?? 0);
                        }
                        $availableQty = $sumFuture;
                    }
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
            'internal_reference' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'delivery_group' => 'required|string',
        ], [
            'internal_reference.required' => 'Il campo Riferimento Ordine Interno è obbligatorio prima dell\'invio.',
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
            'internal_reference' => $request->internal_reference,
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
        
        // Invio notifica ricezione ordine a Cliente e Agente
        try {
            $order->loadMissing('customer.user', 'customer.agents', 'agent', 'items.product.brand', 'items.variant');
            $custEmail = $order->customer?->user?->email ?: $order->customer?->email;
            $agent = $order->agent ?: ($order->customer?->agents ? $order->customer->agents->first() : null);
            $agentEmail = $agent?->email;

            if (!empty($custEmail)) {
                \Illuminate\Support\Facades\Mail::to($custEmail)
                    ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Ricezione Ordine B2B'));
            }
            if (!empty($agentEmail)) {
                \Illuminate\Support\Facades\Mail::to($agentEmail)
                    ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Nuovo Ordine B2B Registrato'));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("[OrderCreatedMail] Errore invio notifica ordine #{$order->id}: " . $e->getMessage());
        }

        // Aggiorniamo la sessione con i restanti prodotti non inviati
        $cart = array_values($cart);
        
        if (count($cart) > 0) {
            $cart = $this->recalculateCartPrices($cart, $b2bCustomer);
            session()->put('b2b_cart', $cart);
            $this->persistCart($cart);

            return redirect()->route('agent.cart')->with('success', 'Ordine #' . $order->id . ' inviato correttamente all\'amministrazione. Nel carrello sono ancora presenti articoli da inviare.');
        }

        session()->put('b2b_cart', $cart);
        $this->persistCart($cart);

        return redirect()->route('agent.orders')->with('success', 'Ordine #' . $order->id . ' inviato correttamente all\'amministrazione.');
    }

    public function orders()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            $orders = B2bOrder::with('customer', 'agent')->latest()->get();
        } elseif ($user->role === 'customer') {
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
        } elseif ($user->role === 'agent') {
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
        } elseif ($user->role === 'agent') {
            $customerIds = $user->b2bCustomers()->pluck('b2b_customers.id');
            if ($order->agent_id !== $user->id && !$customerIds->contains($order->b2b_customer_id)) {
                abort(403);
            }
        }
        $order->load(['agent', 'customer.priceList', 'customer.paymentCondition', 'items.product.brand', 'items.variant']);
        return view('admin.b2b.orders.pdf', compact('order'));
    }

    public function updateOrderNotes(Request $request, B2bOrder $order)
    {
        $user = auth()->user();
        if ($user->role === 'customer') {
            abort(403, 'I clienti non possono modificare le note della sede o dell\'agente.');
        }

        $customerIds = $user->b2bCustomers()->pluck('b2b_customers.id');
        if ($user->role === 'agent' && $order->agent_id !== $user->id && !$customerIds->contains($order->b2b_customer_id)) {
            abort(403, 'Non sei autorizzato a modificare questo ordine.');
        }

        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $oldNotes = (string)($order->admin_notes ?? '');
        $newNotes = (string)($request->admin_notes ?? '');
        $notesChanged = trim($oldNotes) !== trim($newNotes);

        $orderUpdates = [
            'admin_notes' => $request->admin_notes,
        ];

        if ($notesChanged && $order->status !== 'confirmed' && $order->status !== 'cancelled') {
            $orderUpdates['is_modified'] = true;
            $orderUpdates['status'] = 'revision_pending';
        }

        $order->update($orderUpdates);

        // Invio email notifica al cliente ed all'agente
        $sendEmail = $request->input('send_email_to_customer', '1') == '1';
        $emailSent = false;
        $custEmail = null;

        if ($notesChanged && $sendEmail && $order->status !== 'cancelled') {
            $order->loadMissing('customer.user', 'customer.agents', 'agent', 'items.product.brand', 'items.variant');
            $custEmail = $order->customer?->user?->email ?: $order->customer?->email;
            $agent = $order->agent ?: ($order->customer?->agents ? $order->customer->agents->first() : null);
            $agentEmail = $agent?->email;

            if (!empty($custEmail)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($custEmail)
                        ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Rettifica Ordine B2B'));
                    $emailSent = true;
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("[OrderNotesMail] Errore invio email rettifica a {$custEmail}: " . $e->getMessage());
                }
            }

            if (!empty($agentEmail)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($agentEmail)
                        ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Rettifica Ordine B2B per Agente'));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("[OrderNotesMail] Errore invio email rettifica agente {$agentEmail}: " . $e->getMessage());
                }
            }
        }

        $msg = 'Messaggio / Note della sede salvate con successo per l\'Ordine #' . $order->id . '.';
        if ($notesChanged && $order->status !== 'confirmed' && $order->status !== 'cancelled') {
            $msg .= ' Ordine posto in attesa di presa visione del cliente.';
        }
        if ($emailSent && !empty($custEmail)) {
            $msg .= ' Email di notifica inviata al cliente (' . $custEmail . ').';
        }

        return redirect()->back()->with('success', $msg);
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
            'items' => 'nullable|array',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'admin_notes' => 'nullable|string',
        ]);

        $order->load('customer');
        $orderModified = false;

        if ($request->has('items') && is_array($request->items)) {
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
        }

        if ($request->has('delete_items') && is_array($request->delete_items) && count($request->delete_items) > 0) {
            \App\Models\B2bOrderItem::where('b2b_order_id', $order->id)->whereIn('id', $request->delete_items)->delete();
            $orderModified = true;
        }

        $totalAmount = \App\Models\B2bOrderItem::where('b2b_order_id', $order->id)->get()->sum(fn($i) => $i->quantity * $i->price);
        $hasAnyModifiedItem = \App\Models\B2bOrderItem::where('b2b_order_id', $order->id)->where('is_modified', true)->exists();

        $oldNotes = (string)($order->admin_notes ?? '');
        $newNotes = $request->has('admin_notes') ? (string)($request->admin_notes ?? '') : $oldNotes;
        $notesChanged = trim($oldNotes) !== trim($newNotes);

        $isMod = $orderModified || $hasAnyModifiedItem || $notesChanged;

        $orderUpdates = [
            'total_amount' => $totalAmount,
            'is_modified' => $isMod,
            'status' => $isMod ? 'revision_pending' : $order->status,
        ];
        if ($request->has('admin_notes')) {
            $orderUpdates['admin_notes'] = $request->admin_notes;
        }
        $order->update($orderUpdates);

        // Invio notifica email di rettifica al cliente
        $emailSent = false;
        $sendEmail = $request->input('send_email_to_customer', '1') == '1';
        $custEmail = null;

        if ($isMod && $sendEmail) {
            $order->loadMissing('customer.user', 'customer.agents', 'agent', 'items.product.brand', 'items.variant');
            $custEmail = $order->customer?->user?->email ?: $order->customer?->email;
            $agent = $order->agent ?: ($order->customer?->agents ? $order->customer->agents->first() : null);
            $agentEmail = $agent?->email;

            if (!empty($custEmail)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($custEmail)
                        ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Rettifica Ordine B2B'));
                    $emailSent = true;
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("[OrderModificationMail] Errore invio email rettifica a {$custEmail}: " . $e->getMessage());
                }
            }

            if (!empty($agentEmail)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($agentEmail)
                        ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Rettifica Ordine B2B per Agente'));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("[OrderModificationMail] Errore invio email rettifica agente {$agentEmail}: " . $e->getMessage());
                }
            }
        }

        $msg = 'Modifiche dell\'Ordine #' . $order->id . ' salvate con successo! Ordine posto in attesa di approvazione del cliente.';
        if ($emailSent && !empty($custEmail)) {
            $msg .= ' Email di notifica rettifica inviata al cliente (' . $custEmail . ').';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function acceptOrderModifications(B2bOrder $order)
    {
        $user = auth()->user();
        if ($user->role !== 'customer' || $order->b2b_customer_id !== $user->b2b_customer_id) {
            abort(403, 'Non sei autorizzato ad accettare questo ordine.');
        }

        $order->update(['status' => 'customer_approved']);

        // Notifica cliente e agente
        $order->loadMissing('customer.user', 'customer.agents', 'agent', 'items.product.brand', 'items.variant');
        $custEmail = $order->customer?->user?->email ?: $order->customer?->email ?: $user->email;
        $agent = $order->agent ?: ($order->customer?->agents ? $order->customer->agents->first() : null);
        $agentEmail = $agent?->email;

        // 1. Notifica al cliente (conferma accettazione)
        if (!empty($custEmail)) {
            try {
                \Illuminate\Support\Facades\Mail::to($custEmail)
                    ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Modifiche Ordine B2B Accettate'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[OrderAcceptMail] Errore notifica cliente {$custEmail}: " . $e->getMessage());
            }
        }

        // 2. Notifica all'agente commerciale
        if (!empty($agentEmail)) {
            try {
                \Illuminate\Support\Facades\Mail::to($agentEmail)
                    ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Modifiche Ordine B2B Accettate dal Cliente'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[OrderAcceptMail] Errore notifica agente {$agentEmail}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Hai accettato le modifiche dell\'Ordine #' . $order->id . '. L\'ordine è in attesa di OK finale dall\'agente/amministrazione.');
    }

    public function rejectOrderModifications(B2bOrder $order)
    {
        $user = auth()->user();
        if ($user->role !== 'customer' || $order->b2b_customer_id !== $user->b2b_customer_id) {
            abort(403, 'Non sei autorizzato a rifiutare questo ordine.');
        }

        $order->update(['status' => 'customer_rejected']);

        // Notifica cliente e agente
        $order->loadMissing('customer.user', 'customer.agents', 'agent', 'items.product.brand', 'items.variant');
        $custEmail = $order->customer?->user?->email ?: $order->customer?->email ?: $user->email;
        $agent = $order->agent ?: ($order->customer?->agents ? $order->customer->agents->first() : null);
        $agentEmail = $agent?->email;

        // 1. Notifica al cliente (conferma rifiuto)
        if (!empty($custEmail)) {
            try {
                \Illuminate\Support\Facades\Mail::to($custEmail)
                    ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Modifiche Ordine B2B Rifiutate'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[OrderRejectMail] Errore notifica cliente {$custEmail}: " . $e->getMessage());
            }
        }

        // 2. Notifica all'agente commerciale
        if (!empty($agentEmail)) {
            try {
                \Illuminate\Support\Facades\Mail::to($agentEmail)
                    ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Modifiche Ordine B2B Rifiutate dal Cliente'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[OrderRejectMail] Errore notifica agente {$agentEmail}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Hai rifiutato le modifiche dell\'Ordine #' . $order->id . '. L\'agente è stato notificato.');
    }

    public function cancelOrder(Request $request, B2bOrder $order)
    {
        $user = auth()->user();
        if ($user->role === 'customer') {
            abort(403, 'I clienti non possono annullare gli ordini.');
        }

        $customerIds = $user->b2bCustomers()->pluck('b2b_customers.id');
        if ($user->role === 'agent' && $order->agent_id !== $user->id && !$customerIds->contains($order->b2b_customer_id)) {
            abort(403, 'Non sei autorizzato ad annullare questo ordine.');
        }

        if ($order->status === 'confirmed') {
            return redirect()->back()->with('error', 'Impossibile annullare l\'ordine #' . $order->id . ' in quanto è già stato confermato ed inviato alla sede.');
        }

        if ($order->status === 'cancelled') {
            return redirect()->back()->with('warning', 'Questo ordine è già stato annullato.');
        }

        $order->update(['status' => 'cancelled']);

        // Notifica email di annullamento a cliente ed agente
        $order->loadMissing('customer.user', 'customer.agents', 'agent', 'items.product.brand', 'items.variant');
        $custEmail = $order->customer?->user?->email ?: $order->customer?->email;
        $agent = $order->agent ?: ($order->customer?->agents ? $order->customer->agents->first() : null);
        $agentEmail = $agent?->email;

        // 1. Notifica cliente
        if (!empty($custEmail)) {
            try {
                \Illuminate\Support\Facades\Mail::to($custEmail)
                    ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Annullamento Ordine B2B'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[OrderCancelMail] Errore notifica cliente {$custEmail}: " . $e->getMessage());
            }
        }

        // 2. Notifica agente
        if (!empty($agentEmail)) {
            try {
                \Illuminate\Support\Facades\Mail::to($agentEmail)
                    ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Annullamento Ordine B2B per Agente'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[OrderCancelMail] Errore notifica agente {$agentEmail}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Ordine #' . $order->id . ' annullato con successo. Le notifiche sono state inviate al cliente ed all\'agente.');
    }

    public function confirmOrder(Request $request, B2bOrder $order)
    {
        $user = auth()->user();
        if ($user->role === 'customer') {
            abort(403, 'I clienti non possono confermare gli ordini.');
        }

        $customerIds = $user->b2bCustomers()->pluck('b2b_customers.id');
        if ($user->role === 'agent' && $order->agent_id !== $user->id && !$customerIds->contains($order->b2b_customer_id)) {
            abort(403);
        }

        if ($order->status === 'confirmed') {
            return redirect()->back()->with('error', 'Questo ordine è già stato confermato.');
        }

        // VERIFICA DISPONIBILITÀ DI MAGAZZINO REALI PRIMA DELLA CONFERMA ORDINE
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
                if ($availableStock == 0 && !empty($match['future_stock'])) {
                    $sumFuture = 0;
                    foreach ($match['future_stock'] as $fDate => $fSizes) {
                        $sumFuture += (int)($fSizes[$size] ?? 0);
                    }
                    $availableStock = $sumFuture;
                }
                if ($reqQty > $availableStock) {
                    $insufficientItems[] = "{$product->name} (Taglia {$size}): Richiesti {$reqQty} pz, Disponibilità per la data {$deliveryDate}: {$availableStock} pz.";
                }
            }
        }

        if (!empty($insufficientItems)) {
            $errorMessage = "Impossibile confermare l'Ordine #" . $order->id . " a causa di giacenze di magazzino insufficienti: " . implode(" | ", $insufficientItems);
            return redirect()->back()->with('error', $errorMessage);
        }

        $order->update(['status' => 'confirmed']);

        // Genera ed invia il file CSV dell'ordine nella cartella Input su FTP
        $csvResult = $this->exportOrderToFtpCsv($order);

        // Invio email di conferma al cliente ed all'agente
        $emailSent = false;
        if ($request->has('send_email_to_customer') || $request->input('send_email_to_customer') == '1') {
            $order->loadMissing('customer.user', 'customer.agents', 'agent', 'items.product.brand', 'items.variant');
            $custEmail = $order->customer?->user?->email ?: $order->customer?->email;
            $agent = $order->agent ?: ($order->customer?->agents ? $order->customer->agents->first() : null);
            $agentEmail = $agent?->email;

            if (!empty($custEmail)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($custEmail)
                        ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Conferma Definitiva Ordine B2B'));
                    $emailSent = true;
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("[OrderConfirmMail] Errore invio email cliente {$custEmail}: " . $e->getMessage());
                }
            }

            if (!empty($agentEmail)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($agentEmail)
                        ->send(new \App\Mail\B2bOrderCopy($order, null, 'none', 'Conferma Definitiva Ordine B2B per Agente'));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("[OrderConfirmMail] Errore invio email agente {$agentEmail}: " . $e->getMessage());
                }
            }
        }

        $msg = 'Ordine #' . $order->id . ' confermato con successo!';
        if ($csvResult['uploaded']) {
            $msg .= ' File ' . $csvResult['filename'] . ' trasmesso nella cartella Input dell\'FTP.';
        }
        if ($emailSent && !empty($custEmail)) {
            $msg .= ' Email di conferma inviata al cliente (' . $custEmail . ').';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function exportOrderToFtpCsv(B2bOrder $order)
    {
        $order->load('customer', 'items.product', 'items.variant');
        $giacenzaData = $this->getGiacenzaData();
        
        $filename = "{$order->id}.csv";
        $storageDir = storage_path('app/orders');
        if (!file_exists($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }
        $localPath = $storageDir . '/' . $filename;
        
        $file = @fopen($localPath, 'w');
        if (!$file) {
            \Illuminate\Support\Facades\Log::error("[ExportOrderCsv] Impossibile creare/aprire file {$localPath}");
            return ['uploaded' => false, 'filename' => $filename, 'error' => 'Errore scrittura file'];
        }

        // Intestazione con codice articolo dal file giacenze e codice cliente
        fputcsv($file, ['CODICE_ARTICOLO', 'DESCRIZIONE', 'TAGLIA', 'QUANTITA', 'DATA_CONSEGNA', 'PREZZO_UNITARIO', 'CODICE_CLIENTE', 'CLIENTE', 'NUMERO_ORDINE', 'RIFERIMENTO_ORDINE_INTERNO'], ';', '"', "\\");
        
        foreach ($order->items as $item) {
            $code = 'N/D';
            if ($item->product) {
                $match = $this->findGiacenzaMatch($item->product, $giacenzaData);
                $code = ($match && !empty($match['raw_code'])) ? $match['raw_code'] : ($item->product->code ?? $item->product->name);
            }
            $name = $item->product ? $item->product->name : 'N/D';
            $size = $item->variant ? $item->variant->size : '';
            $qty = $item->quantity;
            $deliveryDate = !empty($item->delivery_date) ? $item->delivery_date : 'Pronta consegna';
            $price = number_format($item->price, 2, '.', '');
            $customerCode = $order->customer ? ($order->customer->code ?? '') : '';
            $customerName = $order->customer ? $order->customer->business_name : 'N/D';
            $internalRef = $order->internal_reference ?? '';
            
            fputcsv($file, [$code, $name, $size, $qty, $deliveryDate, $price, $customerCode, $customerName, $order->id, $internalRef], ';', '"', "\\");
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

    public function pingSync()
    {
        $lastSync = \Illuminate\Support\Facades\Cache::get('b2b_last_giacenze_sync_timestamp');
        $needsSync = !$lastSync || abs(now()->diffInSeconds($lastSync)) >= 120;

        if ($needsSync) {
            try {
                $phpBinary = PHP_BINARY ?: 'php';
                $artisan = base_path('artisan');
                exec("{$phpBinary} {$artisan} b2b:sync-giacenze > /dev/null 2>&1 &");
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'status' => 'ok',
            'synced' => $needsSync,
            'last_sync' => $lastSync ? $lastSync->toIso8601String() : null
        ]);
    }

    public function getGiacenzaData()
    {
        // Controllo e avvio automatico sync non-bloccante se sono passati più di 2 minuti
        $lastSync = \Illuminate\Support\Facades\Cache::get('b2b_last_giacenze_sync_timestamp');
        if (!$lastSync || abs(now()->diffInSeconds($lastSync)) >= 120) {
            try {
                $phpBinary = PHP_BINARY ?: 'php';
                $artisan = base_path('artisan');
                exec("{$phpBinary} {$artisan} b2b:sync-giacenze > /dev/null 2>&1 &");
            } catch (\Throwable $e) {}
        }

        $csvPath = base_path('Giacenza.csv');
        
        if (!file_exists($csvPath)) {
            return [];
        }
        
        $file = fopen($csvPath, 'r');
        $headers = fgetcsv($file, 0, ';', '"', '\\');
        
        $data = [];
        while (($row = fgetcsv($file, 0, ';', '"', '\\')) !== false) {
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
