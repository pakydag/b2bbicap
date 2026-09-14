<?php

namespace App\Http\Controllers\Admin\B2b;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class B2bCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        $query = \App\Models\B2bCustomer::with(['paymentCondition', 'user'])
            ->withCount('orders')
            ->withSum('orders', 'total_amount');
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%")
                  ->orWhere('vat_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $customers = $query->orderBy('business_name')->get();
        return view('admin.b2b.customers.index', compact('customers', 'search'));
    }

    public function import(Request $request, \App\Services\CustomerImportService $importer)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $result = $importer->import($path);

        $msg = "Importazione completata: {$result['total']} clienti elaborati ({$result['created']} nuovi, {$result['updated']} aggiornati con codice gestionale).";
        if (!empty($result['errors'])) {
            return redirect()->back()->with('warning', $msg . ' Alcune righe hanno generato errori: ' . implode(', ', array_slice($result['errors'], 0, 5)));
        }

        return redirect()->back()->with('success', $msg);
    }

    public function syncFtps(\App\Services\CustomerImportService $importer)
    {
        $result = $importer->syncFromFtps();

        if ($result['success'] ?? false) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message'] ?? 'Errore durante la sincronizzazione da FTPS.');
        }
    }

    public function create()
    {
        return redirect()->route('admin.b2b.customers.index')->with('error', 'I clienti B2B non possono essere inseriti manualmente. Vengono importati e sincronizzati automaticamente dal gestionale.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'vat_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'payment_condition_id' => 'nullable|exists:b2b_payment_conditions,id',
            'b2b_price_list_id' => 'nullable|exists:b2b_price_lists,id',
            'b2b_email' => 'nullable|email|max:255|unique:users,email',
            'b2b_password' => 'nullable|string|min:6',
        ]);

        $customer = \App\Models\B2bCustomer::create($request->only([
            'business_name', 'vat_number', 'contact_name', 
            'contact_surname', 'phone', 'email', 'payment_condition_id', 'b2b_price_list_id'
        ]));

        if ($request->filled('b2b_email')) {
            \App\Models\User::create([
                'name' => $customer->business_name,
                'email' => $request->b2b_email,
                'password' => bcrypt($request->b2b_password ?? 'Bicap2026!'),
                'role' => 'customer',
                'b2b_customer_id' => $customer->id,
            ]);
        }

        return redirect()->route('admin.b2b.customers.index')->with('success', 'Cliente B2B creato con successo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\B2bCustomer $customer)
    {
        return redirect()->route('admin.b2b.customers.index')->with('error', 'I dati dei clienti provengono dai file gestionali e non possono essere modificati manualmente.');
    }

    public function update(Request $request, \App\Models\B2bCustomer $customer)
    {
        return redirect()->route('admin.b2b.customers.index')->with('error', 'I dati dei clienti provengono dai file gestionali e non possono essere modificati manualmente.');
    }

    public function destroy(\App\Models\B2bCustomer $customer)
    {
        return redirect()->route('admin.b2b.customers.index')->with('error', 'I clienti provengono dai file gestionali e non possono essere eliminati manualmente.');
    }
}
