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
        $status = $request->query('status'); // 'all', 'enabled', 'disabled'
        $agentId = $request->query('agent_id');
        
        $query = \App\Models\B2bCustomer::with(['paymentCondition', 'user', 'agents'])
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

        if ($status === 'enabled') {
            $query->whereHas('user', function($q) {
                $q->whereNotNull('email')->where('email', '!=', '');
            });
        } elseif ($status === 'disabled') {
            $query->whereDoesntHave('user');
        }

        if (!empty($agentId)) {
            $query->whereHas('agents', function($q) use ($agentId) {
                $q->where('users.id', $agentId);
            });
        }
        
        $totalCount = \App\Models\B2bCustomer::count();
        $enabledCount = \App\Models\B2bCustomer::whereHas('user', function($q) {
            $q->whereNotNull('email')->where('email', '!=', '');
        })->count();
        $disabledCount = $totalCount - $enabledCount;

        $agents = \App\Models\User::where('role', 'agent')->orderBy('name')->get();
        
        $customers = $query->orderBy('business_name')->paginate(50)->withQueryString();
        
        return view('admin.b2b.customers.index', compact(
            'customers', 'search', 'status', 'agentId', 
            'totalCount', 'enabledCount', 'disabledCount', 'agents'
        ));
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
        $customer->load('user', 'paymentCondition', 'agents', 'priceList');
        $conditions = \App\Models\B2bPaymentCondition::all();
        $priceLists = \App\Models\B2bPriceList::all();
        $agents = \App\Models\User::where('role', 'agent')->orderBy('name')->get();
        $customerUser = $customer->user;
        $assignedAgentIds = $customer->agents->pluck('id')->toArray();
        return view('admin.b2b.customers.edit', compact('customer', 'conditions', 'priceLists', 'customerUser', 'agents', 'assignedAgentIds'));
    }

    public function update(Request $request, \App\Models\B2bCustomer $customer)
    {
        $rules = [
            'code' => 'nullable|string|max:50',
            'business_name' => 'required|string|max:255',
            'vat_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_surname' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'payment_condition_id' => 'nullable|exists:b2b_payment_conditions,id',
            'b2b_price_list_id' => 'nullable|exists:b2b_price_lists,id',
            'b2b_email' => 'nullable|email|max:255|unique:users,email,' . ($customer->user?->id ?? 'NULL'),
            'send_email_notification' => 'nullable',
            'agent_ids' => 'nullable|array',
            'agent_ids.*' => 'exists:users,id',
        ];

        if ($request->filled('b2b_password')) {
            $rules['b2b_password'] = [
                'required',
                'string',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ];
        }

        $request->validate($rules, [
            'b2b_password.min' => 'La password deve contenere almeno :min caratteri.',
            'b2b_password.letters' => 'La password deve contenere almeno una lettera.',
            'b2b_password.mixed' => 'La password deve contenere sia lettere maiuscole che minuscole.',
            'b2b_password.numbers' => 'La password deve contenere almeno un numero.',
            'b2b_password.symbols' => 'La password deve contenere almeno un carattere speciale (!@#$%^&* ecc.).',
            'b2b_email.email' => 'Inserisci un indirizzo email di accesso valido.',
            'b2b_email.unique' => 'Questo indirizzo email è già utilizzato da un altro account.',
        ]);

        $customer->update($request->only([
            'code', 'business_name', 'vat_number', 'contact_name', 
            'contact_surname', 'phone', 'email', 'payment_condition_id', 'b2b_price_list_id'
        ]));

        if ($request->has('agent_ids')) {
            $customer->agents()->sync($request->input('agent_ids', []));
        }

        $emailSent = false;
        if ($request->filled('b2b_email')) {
            if ($customer->user) {
                $userData = [
                    'email' => $request->b2b_email,
                    'name' => $customer->business_name,
                ];
                if ($request->filled('b2b_password')) {
                    $userData['password'] = bcrypt($request->b2b_password);
                }
                $customer->user->update($userData);
            } else {
                \App\Models\User::create([
                    'name' => $customer->business_name,
                    'email' => $request->b2b_email,
                    'password' => bcrypt($request->b2b_password ?? 'Bicap2026!'),
                    'role' => 'customer',
                    'b2b_customer_id' => $customer->id,
                ]);
            }
        }

        $recipientEmail = $request->b2b_email ?: ($customer->user?->email ?: $customer->email);
        $shouldSendEmail = ($request->has('send_email_notification') || $request->input('send_email_notification') == '1') && !empty($recipientEmail);

        if ($shouldSendEmail) {
            $customer->load('agents', 'paymentCondition', 'priceList');
            try {
                if ($request->filled('b2b_password')) {
                    \Illuminate\Support\Facades\Mail::to($recipientEmail)
                        ->send(new \App\Mail\B2bCustomerAccountMail($customer, $recipientEmail, $request->b2b_password));
                } else {
                    \Illuminate\Support\Facades\Mail::to($recipientEmail)
                        ->send(new \App\Mail\B2bCustomerUpdatedMail($customer, $recipientEmail));
                }
                $emailSent = true;
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[CustomerMail] Errore invio email cliente {$recipientEmail}: " . $e->getMessage());
            }
        }

        $msg = 'Cliente B2B aggiornato con successo.';
        if ($emailSent) {
            if ($request->filled('b2b_password')) {
                $msg .= ' È stata inviata un\'email con le credenziali di accesso all\'indirizzo ' . $recipientEmail . '.';
            } else {
                $msg .= ' È stata inviata un\'email con il riepilogo dei dati aggiornati all\'indirizzo ' . $recipientEmail . '.';
            }
        }

        return redirect()->route('admin.b2b.customers.index')->with('success', $msg);
    }

    public function destroy(\App\Models\B2bCustomer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.b2b.customers.index')->with('success', 'Cliente B2B eliminato con successo.');
    }
}
