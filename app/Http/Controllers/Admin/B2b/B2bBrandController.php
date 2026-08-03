<?php

namespace App\Http\Controllers\Admin\B2b;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class B2bBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = \App\Models\B2bBrand::withCount('products')->orderBy('name')->get();
        return view('admin.b2b.brands.index', compact('brands'));
    }

    public function create()
    {
        return redirect()->route('admin.b2b.brands.index')->with('info', 'Le linee non possono essere create manualmente. Vengono generate automaticamente durante l\'importazione del file CSV.');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.b2b.brands.index')->with('error', 'Creazione manuale disabilitata.');
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\B2bBrand $brand)
    {
        return redirect()->route('admin.b2b.products.index', ['brand_id' => $brand->id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return redirect()->route('admin.b2b.brands.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.b2b.brands.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\B2bBrand $brand)
    {
        return redirect()->route('admin.b2b.brands.index')->with('error', 'Le linee di prodotto provengono dalla sincronizzazione del catalogo e non possono essere eliminate manualmente.');
    }
}
