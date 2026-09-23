<?php

namespace App\Http\Controllers\Admin\B2b;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class B2bDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'agents_count' => \App\Models\User::where('role', 'agent')->count(),
            'customers_count' => \App\Models\B2bCustomer::count(),
            'pending_orders_count' => \App\Models\B2bOrder::where('status', 'pending')->count(),
            'total_revenue' => \App\Models\B2bOrder::where('status', 'confirmed')->sum('total_amount'),
        ];

        $recent_orders = \App\Models\B2bOrder::with('agent', 'customer')->latest()->take(5)->get();

        return view('admin.b2b.dashboard.index', compact('stats', 'recent_orders'));
    }

    /**
     * Svuota i dati operativi B2B:
     * - Cancella gli agenti abilitati
     * - Cancella gli ordini ricevuti
     * - Disabilita l'accesso fornito alle aziende e disattiva gli agenti di riferimento
     */
    public function wipeData(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Azione non autorizzata.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () {
            // 1. Cancella gli ordini ricevuti B2B e le relative voci d'ordine
            \App\Models\B2bOrderItem::query()->delete();
            \App\Models\B2bOrder::query()->delete();

            // 2. Cancella i carrelli B2B attivi
            \App\Models\B2bCart::query()->delete();

            // 3. Cancella i listini prezzi creati da agenti e le relative voci
            \App\Models\B2bPriceListItem::whereHas('priceList', function ($q) {
                $q->whereNotNull('agent_id');
            })->delete();
            \App\Models\B2bPriceList::whereNotNull('agent_id')->delete();

            // 4. Disattiva gli agenti di riferimento: scollega tutti gli agenti dalle aziende clienti
            \Illuminate\Support\Facades\DB::table('agent_customer')->delete();

            // 5. Rimuove le assegnazioni delle linee/marche agli agenti
            \Illuminate\Support\Facades\DB::table('agent_brand')->delete();

            // 6. Cancella tutti gli account agenti abilitati
            \App\Models\User::where('role', 'agent')->delete();

            // 7. Disabilita l'accesso fornito alle aziende (elimina gli account login associati alle aziende B2B)
            \App\Models\User::where('role', 'customer')->whereNotNull('b2b_customer_id')->delete();
        });

        return redirect()->route('admin.b2b.dashboard')->with('success', 'Operazione completata: agenti abilitati cancellati, ordini ricevuti eliminati, accessi delle aziende disabilitati e agenti di riferimento disattivati con successo.');
    }
}

