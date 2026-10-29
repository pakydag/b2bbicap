<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopCoupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShopCouponController extends Controller
{
    public function index(Request $request)
    {
        $query = ShopCoupon::query()->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $coupons = $query->paginate(20)->withQueryString();
        $totalCoupons = ShopCoupon::count();
        $activeCoupons = ShopCoupon::where('attivo', true)->count();
        $totalUsages = ShopCoupon::sum('utilizzi');

        return view('admin.shop.coupons.index', compact(
            'coupons',
            'totalCoupons',
            'activeCoupons',
            'totalUsages'
        ));
    }

    public function create()
    {
        return view('admin.shop.coupons.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'code' => strtolower(trim($request->code)),
        ]);

        $rules = [
            'code' => 'required|string|max:50|unique:shop_coupons,code',
            'name' => 'nullable|string|max:255',
            'type' => 'required|in:percentuale,fisso',
            'valore' => 'required|numeric|min:0.01',
            'min_spesa' => 'nullable|numeric|min:0',
            'sempre_attivo' => 'required|boolean',
            'attivo' => 'required|boolean',
        ];

        if ($request->type === 'percentuale') {
            $rules['valore'] .= '|max:100';
        }

        $validated = $request->validate($rules, [
            'code.required' => 'Il codice coupon è obbligatorio.',
            'code.unique' => 'Questo codice coupon esiste già.',
            'valore.required' => 'Il valore dello sconto è obbligatorio.',
            'valore.max' => 'La percentuale di sconto non può superare il 100%.',
        ]);

        $validated['min_spesa'] = $validated['min_spesa'] ?? 0;

        ShopCoupon::create($validated);

        return redirect()->route('admin.shop.coupons.index')->with('success', 'Coupon creato con successo.');
    }

    public function edit(ShopCoupon $coupon)
    {
        return view('admin.shop.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, ShopCoupon $coupon)
    {
        $request->merge([
            'code' => strtolower(trim($request->code)),
        ]);

        $rules = [
            'code' => ['required', 'string', 'max:50', Rule::unique('shop_coupons', 'code')->ignore($coupon->id)],
            'name' => 'nullable|string|max:255',
            'type' => 'required|in:percentuale,fisso',
            'valore' => 'required|numeric|min:0.01',
            'min_spesa' => 'nullable|numeric|min:0',
            'sempre_attivo' => 'required|boolean',
            'attivo' => 'required|boolean',
        ];

        if ($request->type === 'percentuale') {
            $rules['valore'] .= '|max:100';
        }

        $validated = $request->validate($rules, [
            'code.required' => 'Il codice coupon è obbligatorio.',
            'code.unique' => 'Questo codice coupon esiste già.',
            'valore.required' => 'Il valore dello sconto è obbligatorio.',
            'valore.max' => 'La percentuale di sconto non può superare il 100%.',
        ]);

        $validated['min_spesa'] = $validated['min_spesa'] ?? 0;

        $coupon->update($validated);

        return redirect()->route('admin.shop.coupons.index')->with('success', 'Coupon aggiornato con successo.');
    }

    public function toggle(ShopCoupon $coupon)
    {
        $coupon->attivo = !$coupon->attivo;
        $coupon->save();

        $statusMsg = $coupon->attivo ? 'attivato' : 'disattivato';
        return redirect()->back()->with('success', "Coupon {$coupon->code} {$statusMsg} con successo.");
    }

    public function destroy(ShopCoupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.shop.coupons.index')->with('success', 'Coupon eliminato con successo.');
    }
}

