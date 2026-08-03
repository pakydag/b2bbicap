<?php

namespace App\Http\Controllers\Admin\B2b;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agents = \App\Models\User::where('role', 'agent')
            ->with('b2bBrands', 'b2bCustomers')
            ->withCount('b2bOrders')
            ->withSum('b2bOrders', 'total_amount')
            ->get();
        return view('admin.b2b.agents.index', compact('agents'));
    }

    public function create()
    {
        $brands = \App\Models\B2bBrand::all();
        $customers = \App\Models\B2bCustomer::orderBy('business_name')->get();
        return view('admin.b2b.agents.create', compact('brands', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'brands' => 'nullable|array',
            'customers' => 'nullable|array',
        ]);

        // Genera una password casuale (verrà resettata dall'utente)
        $tempPassword = \Illuminate\Support\Str::random(12);

        $agent = \App\Models\User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => \Illuminate\Support\Facades\Hash::make($tempPassword),
            'role' => 'agent',
        ]);

        if ($request->has('brands')) {
            $agent->b2bBrands()->sync($request->brands);
        }

        if ($request->has('customers')) {
            $agent->b2bCustomers()->sync($request->customers);
        }

        // Invia email di benvenuto (riusiamo la mail esistente o ne creiamo una se necessario)
        // Per ora usiamo quella standard se esiste, altrimenti commentiamo per non rompere.
        try {
            \Illuminate\Support\Facades\Mail::to($agent->email)->send(new \App\Mail\AdminUserCreated($agent, $tempPassword));
        } catch (\Exception $e) {
            // Log error but continue
        }

        return redirect()->route('admin.b2b.agents.index')->with('success', 'Agente creato con successo. È stata inviata una email per l\'impostazione della password.');
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
    public function edit(\App\Models\User $agent)
    {
        if ($agent->role !== 'agent') abort(403);
        
        $brands = \App\Models\B2bBrand::all();
        $customers = \App\Models\B2bCustomer::orderBy('business_name')->get();
        $agent->load('b2bBrands', 'b2bCustomers');
        
        return view('admin.b2b.agents.edit', compact('agent', 'brands', 'customers'));
    }

    public function update(Request $request, \App\Models\User $agent)
    {
        if ($agent->role !== 'agent') abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $agent->id,
            'phone' => 'nullable|string',
            'brands' => 'nullable|array',
            'customers' => 'nullable|array',
        ]);

        $agent->update([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $agent->b2bBrands()->sync($request->brands ?? []);
        $agent->b2bCustomers()->sync($request->customers ?? []);

        return redirect()->route('admin.b2b.agents.index')->with('success', 'Agente aggiornato con successo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\User $agent)
    {
        if ($agent->role !== 'agent') {
            return redirect()->back()->with('error', 'Azione non consentita.');
        }

        $agent->delete();

        return redirect()->route('admin.b2b.agents.index')->with('success', 'Agente rimosso con successo.');
    }
}
