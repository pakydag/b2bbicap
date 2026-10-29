<?php

namespace App\Http\Controllers;

use App\Models\ShopOrder;
use Illuminate\Http\Request;

class ShopOrderController extends Controller
{
    public function index()
    {
        $ordini = ShopOrder::with('customer')->orderBy('created_at', 'desc')->get();
        return view('admin.shop.ordini.index', compact('ordini'));
    }

    public function edit(ShopOrder $ordine)
    {
        $ordine->load(['customer', 'items.variant.product']);
        return view('admin.shop.ordini.edit', compact('ordine'));
    }

    public function update(Request $request, ShopOrder $ordine)
    {
        $validated = $request->validate([
            'stato' => 'required|string',
            'stato_pagamento' => 'required|string',
            'note_admin' => 'nullable|string',
            'corriere' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'tracking_url' => 'nullable|string|max:500',
            'send_tracking_email' => 'nullable|boolean',
        ]);

        $old_stato = $ordine->stato;
        
        // Se lo stato passa a spedito, valorizza la data spedito_il se non presente
        if ($validated['stato'] === 'spedito' && empty($ordine->spedito_il)) {
            $validated['spedito_il'] = now();
        }

        $ordine->update($validated);

        // Se l'ordine è 'spedito' ed è appena passato a spedito, oppure è stata richiesta esplicitamente la notifica:
        $shouldSendShippingEmail = ($old_stato !== 'spedito' && $ordine->stato === 'spedito') 
            || ($request->boolean('send_tracking_email') && $ordine->stato === 'spedito');

        if ($shouldSendShippingEmail) {
            $customerEmail = $ordine->customer?->email ?? $ordine->contact?->email;
            if ($customerEmail) {
                try {
                    \App\Services\ShopMailService::send($customerEmail, new \App\Mail\OrderShipped($ordine));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Errore invio email spedizione: ' . $e->getMessage());
                }
            }
        } elseif ($old_stato !== $ordine->stato && $ordine->stato !== 'nuovo') {
            // Per gli altri cambi di stato (es. annullato), invia la notifica generica
            $customerEmail = $ordine->customer?->email ?? $ordine->contact?->email;
            if ($customerEmail) {
                try {
                    \App\Services\ShopMailService::send($customerEmail, new \App\Mail\OrderStatusChanged($ordine));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Errore invio email cambio stato: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.shop.ordini.index')->with('success', 'Ordine aggiornato con successo' . ($shouldSendShippingEmail ? ' ed email con tracking inviata al cliente.' : '.'));
    }
    
    public function destroy(ShopOrder $ordine)
    {
        $ordine->delete();
        return redirect()->route('admin.shop.ordini.index')->with('success', 'Ordine eliminato con successo.');
    }
}
