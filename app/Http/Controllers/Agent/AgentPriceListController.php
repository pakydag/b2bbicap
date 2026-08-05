<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\B2bPriceList;
use App\Models\B2bPriceListItem;
use App\Models\B2bProduct;
use App\Models\B2bCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentPriceListController extends Controller
{
    /**
     * Verifica che l'utente non sia un cliente (i clienti non possono gestire o vedere i listini).
     */
    private function authorizeAgent()
    {
        if (auth()->check() && auth()->user()->role === 'customer') {
            abort(403, 'Accesso riservato agli agenti.');
        }
    }

    /**
     * Display a listing of price lists for the agent.
     */
    public function index()
    {
        $this->authorizeAgent();

        $user = auth()->user();

        $query = B2bPriceList::withCount(['items', 'customers'])->with('customers');
        if ($user->role === 'agent') {
            $query->where('agent_id', $user->id);
        }
        $priceLists = $query->orderBy('created_at', 'desc')->get();

        $customers = $user->role === 'agent' 
            ? $user->b2bCustomers()->with('priceList')->orderBy('business_name')->get()
            : B2bCustomer::with('priceList')->orderBy('business_name')->get();

        return view('agent.price_lists.index', compact('priceLists', 'customers'));
    }

    /**
     * Show the form for creating a new price list.
     */
    public function create()
    {
        $this->authorizeAgent();

        $user = auth()->user();
        $customers = $user->role === 'agent' 
            ? $user->b2bCustomers()->orderBy('business_name')->get()
            : B2bCustomer::orderBy('business_name')->get();

        return view('agent.price_lists.create', compact('customers'));
    }

    /**
     * Store a newly created price list in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAgent();

        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'b2b_customer_id' => 'required|exists:b2b_customers,id',
            'description' => 'nullable|string',
            'general_discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $customer = B2bCustomer::findOrFail($request->b2b_customer_id);
        if ($user->role === 'agent' && !$user->b2bCustomers->contains($customer->id)) {
            abort(403);
        }

        DB::transaction(function () use ($request, $user, $customer, &$priceList) {
            $priceList = B2bPriceList::create([
                'agent_id' => $user->id,
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'general_discount_percent' => floatval($request->input('general_discount_percent', 0)),
            ]);

            // Assegna subito il listino all'azienda selezionata
            $customer->update([
                'b2b_price_list_id' => $priceList->id
            ]);

            // Salva eventuali fasce generali per tutti i prodotti inserite al momento della creazione
            $generalTiers = $request->input('general_tiers', []);
            if (is_array($generalTiers)) {
                foreach ($generalTiers as $gtier) {
                    $min = intval($gtier['min_quantity'] ?? 1);
                    $max = isset($gtier['max_quantity']) && $gtier['max_quantity'] !== '' ? intval($gtier['max_quantity']) : null;
                    $val = floatval($gtier['discount_value'] ?? 0);
                    $val2 = floatval($gtier['discount_2'] ?? 0);
                    $val3 = floatval($gtier['discount_3'] ?? 0);
                    $type = in_array($gtier['discount_type'] ?? '', ['percentage', 'fixed_price']) ? $gtier['discount_type'] : 'percentage';
                    if ($min > 0) {
                        B2bPriceListItem::create([
                            'b2b_price_list_id' => $priceList->id,
                            'b2b_product_id' => null,
                            'min_quantity' => $min,
                            'max_quantity' => $max,
                            'discount_type' => $type,
                            'discount_value' => $val,
                            'discount_2' => $val2,
                            'discount_3' => $val3,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('agent.price-lists.edit', $priceList)
            ->with('success', 'Listino creato ed abbinato a ' . $customer->business_name . '! Ora puoi visualizzare e personalizzare le fasce di quantità per ciascun prodotto.');
    }

    /**
     * Show the form for editing the specified price list.
     */
    public function edit(B2bPriceList $priceList)
    {
        $this->authorizeAgent();

        $user = auth()->user();
        if ($user->role === 'agent' && $priceList->agent_id !== $user->id) {
            abort(403);
        }

        $products = B2bProduct::where('is_active', true)
            ->orderBy('name')
            ->get();

        $itemsGrouped = B2bPriceListItem::where('b2b_price_list_id', $priceList->id)
            ->whereNotNull('b2b_product_id')
            ->get()
            ->groupBy('b2b_product_id');

        $generalTiers = B2bPriceListItem::where('b2b_price_list_id', $priceList->id)
            ->whereNull('b2b_product_id')
            ->orderBy('min_quantity')
            ->get();

        $customers = $user->role === 'agent' 
            ? $user->b2bCustomers()->orderBy('business_name')->get()
            : B2bCustomer::orderBy('business_name')->get();

        $assignedCustomer = B2bCustomer::where('b2b_price_list_id', $priceList->id)->first();

        return view('agent.price_lists.edit', compact('priceList', 'products', 'itemsGrouped', 'generalTiers', 'customers', 'assignedCustomer'));
    }

    /**
     * Update the specified price list in storage.
     */
    public function update(Request $request, B2bPriceList $priceList)
    {
        $this->authorizeAgent();

        $user = auth()->user();
        if ($user->role === 'agent' && $priceList->agent_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'b2b_customer_id' => 'nullable|exists:b2b_customers,id',
            'description' => 'nullable|string',
            'general_discount_percent' => 'nullable|numeric|min:0|max:100',
            'general_tiers' => 'nullable|array',
            'tiers' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $priceList) {
            $priceList->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'general_discount_percent' => floatval($request->input('general_discount_percent', 0)),
            ]);

            // Aggiorna associazione cliente se cambiata
            if ($request->filled('b2b_customer_id')) {
                B2bCustomer::where('b2b_price_list_id', $priceList->id)->update(['b2b_price_list_id' => null]);
                B2bCustomer::where('id', $request->b2b_customer_id)->update(['b2b_price_list_id' => $priceList->id]);
            }

            // Elimina vecchi item
            B2bPriceListItem::where('b2b_price_list_id', $priceList->id)->delete();

            // Salva fasce generali (b2b_product_id IS NULL)
            $generalTiers = $request->input('general_tiers', []);
            if (is_array($generalTiers)) {
                foreach ($generalTiers as $gtier) {
                    $min = intval($gtier['min_quantity'] ?? 1);
                    $max = isset($gtier['max_quantity']) && $gtier['max_quantity'] !== '' ? intval($gtier['max_quantity']) : null;
                    $val = floatval($gtier['discount_value'] ?? 0);
                    $val2 = floatval($gtier['discount_2'] ?? 0);
                    $val3 = floatval($gtier['discount_3'] ?? 0);
                    $type = in_array($gtier['discount_type'] ?? '', ['percentage', 'fixed_price']) ? $gtier['discount_type'] : 'percentage';
                    if ($min > 0) {
                        B2bPriceListItem::create([
                            'b2b_price_list_id' => $priceList->id,
                            'b2b_product_id' => null,
                            'min_quantity' => $min,
                            'max_quantity' => $max,
                            'discount_type' => $type,
                            'discount_value' => $val,
                            'discount_2' => $val2,
                            'discount_3' => $val3,
                        ]);
                    }
                }
            }

            // Salva fasce per singolo prodotto
            $tiers = $request->input('tiers', []);
            foreach ($tiers as $productId => $productTiers) {
                if (!is_array($productTiers)) continue;

                foreach ($productTiers as $tier) {
                    $min = intval($tier['min_quantity'] ?? 1);
                    $max = isset($tier['max_quantity']) && $tier['max_quantity'] !== '' ? intval($tier['max_quantity']) : null;
                    $type = in_array($tier['discount_type'] ?? '', ['percentage', 'fixed_price']) ? $tier['discount_type'] : 'percentage';
                    $val = floatval($tier['discount_value'] ?? 0);
                    $val2 = floatval($tier['discount_2'] ?? 0);
                    $val3 = floatval($tier['discount_3'] ?? 0);

                    if ($min > 0) {
                        B2bPriceListItem::create([
                            'b2b_price_list_id' => $priceList->id,
                            'b2b_product_id' => $productId,
                            'min_quantity' => $min,
                            'max_quantity' => $max,
                            'discount_type' => $type,
                            'discount_value' => $val,
                            'discount_2' => $val2,
                            'discount_3' => $val3,
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Listino prezzi e fasce di quantità aggiornati con successo!');
    }

    /**
     * Remove the specified price list from storage.
     */
    public function destroy(B2bPriceList $priceList)
    {
        $this->authorizeAgent();

        $user = auth()->user();
        if ($user->role === 'agent' && $priceList->agent_id !== $user->id) {
            abort(403);
        }

        $priceList->delete();
        return redirect()->route('agent.price-lists.index')->with('success', 'Listino prezzi eliminato.');
    }

    /**
     * Assegna o rimuove un listino prezzi ad un'azienda.
     */
    public function assignCustomer(Request $request)
    {
        $this->authorizeAgent();

        $request->validate([
            'b2b_customer_id' => 'required|exists:b2b_customers,id',
            'b2b_price_list_id' => 'nullable|exists:b2b_price_lists,id',
        ]);

        $customer = B2bCustomer::findOrFail($request->b2b_customer_id);
        
        $user = auth()->user();
        if ($user->role === 'agent') {
            if (!$user->b2bCustomers->contains($customer->id)) {
                abort(403);
            }
        }

        $customer->update([
            'b2b_price_list_id' => $request->b2b_price_list_id
        ]);

        return redirect()->back()->with('success', 'Abbinamento listino per ' . $customer->business_name . ' aggiornato con successo!');
    }
}
