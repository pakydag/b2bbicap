<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopShippingCost;
use App\Models\Setting;
use Illuminate\Http\Request;

class ShopShippingCostController extends Controller
{
    public function index()
    {
        $costs = ShopShippingCost::orderByRaw("CASE WHEN nazione = 'Italia' THEN 0 ELSE 1 END, nazione ASC")->get();
        $freeThreshold = Setting::where('key', 'shop_free_shipping_threshold')->value('value') ?? 89;
        
        return view('admin.shop.shipping_costs.index', compact('costs', 'freeThreshold'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nazione' => 'required|string|max:100',
            'costo' => 'required|numeric|min:0'
        ]);

        $nazione = trim($request->nazione);
        $costo = (float)$request->costo;

        $cost = ShopShippingCost::updateOrCreate(
            ['nazione' => $nazione],
            ['costo' => $costo]
        );

        return redirect()->back()->with('success', "Spesa di spedizione per '{$cost->nazione}' salvata con successo (€ " . number_format($cost->costo, 2, ',', '.') . ").");
    }

    public function update(Request $request, ShopShippingCost $shippingCost)
    {
        $request->validate([
            'costo' => 'required|numeric|min:0'
        ]);

        $shippingCost->update([
            'costo' => (float)$request->costo
        ]);

        return redirect()->back()->with('success', "Tariffa per '{$shippingCost->nazione}' aggiornata a € " . number_format($shippingCost->costo, 2, ',', '.') . ".");
    }

    public function initEuCountries()
    {
        $countries = [
            'Italia' => 8.00,
            'Austria' => 20.00,
            'Belgio' => 20.00,
            'Bulgaria' => 20.00,
            'Cipro' => 20.00,
            'Repubblica Ceca' => 20.00,
            'Germania' => 20.00,
            'Danimarca' => 20.00,
            'Estonia' => 20.00,
            'Grecia' => 20.00,
            'Spagna' => 20.00,
            'Finlandia' => 20.00,
            'Francia' => 20.00,
            'Croazia' => 20.00,
            'Ungheria' => 20.00,
            'Irlanda' => 20.00,
            'Lituania' => 20.00,
            'Lussemburgo' => 20.00,
            'Lettonia' => 20.00,
            'Malta' => 20.00,
            'Paesi Bassi' => 20.00,
            'Polonia' => 20.00,
            'Portogallo' => 20.00,
            'Romania' => 20.00,
            'Svezia' => 20.00,
            'Slovacchia' => 20.00,
            'Slovenia' => 20.00,
        ];

        foreach ($countries as $nazione => $costo) {
            ShopShippingCost::updateOrCreate(
                ['nazione' => $nazione],
                ['costo' => $costo]
            );
        }

        Setting::updateOrCreate(
            ['key' => 'shop_free_shipping_threshold'],
            ['value' => '89']
        );

        return redirect()->back()->with('success', 'Nazioni UE (€ 20,00) e Italia (€ 8,00 con soglia spedizione gratuita a € 89) caricate con successo nel gestionale!');
    }

    public function updateThreshold(Request $request)
    {
        $request->validate([
            'shop_free_shipping_threshold' => 'nullable|numeric|min:0'
        ]);

        Setting::updateOrCreate(
            ['key' => 'shop_free_shipping_threshold'],
            ['value' => $request->shop_free_shipping_threshold ?? 0]
        );

        return redirect()->back()->with('success', 'Soglia spedizione gratuita aggiornata con successo.');
    }

    public function destroy(ShopShippingCost $shippingCost)
    {
        $name = $shippingCost->nazione;
        $shippingCost->delete();
        return redirect()->back()->with('success', "Nazione '{$name}' rimossa con successo.");
    }
}
