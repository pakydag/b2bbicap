@extends('public.layouts.main')

@section('title', 'Ordine Ricevuto - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen pt-12 pb-24 flex items-center justify-center">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        
        <div class="bg-white shadow rounded-lg px-4 py-8 sm:p-12 text-center border-t-4 border-green-500">
            
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <svg class="h-10 w-10 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Grazie per il tuo ordine!</h1>
            <p class="text-lg text-gray-500 mb-8">Abbiamo ricevuto il tuo ordine e lo stiamo elaborando.</p>

            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Numero Ordine</p>
                <p class="text-xl font-bold text-gray-900">{{ $order_number }}</p>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Ti abbiamo inviato un'email di conferma con i dettagli del tuo acquisto. Controlla la tua casella di posta (anche nello Spam se non la trovi).</p>
                </div>

                @php
                    $order = \App\Models\ShopOrder::where('numero_ordine', $order_number)->first();
                @endphp

                @if($order && $order->metodo_pagamento === 'bonifico')
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            Coordinate Bancarie per il Pagamento
                        </h3>
                        <div class="bg-indigo-50 border border-indigo-100 rounded-md p-4 text-sm text-gray-800">
                            <p class="mb-2">Per completare il tuo ordine, effettua un bonifico bancario utilizzando le coordinate qui sotto. <strong>Indica come causale il Numero Ordine ({{ $order_number }}).</strong> L'ordine non verrà spedito finché i fondi non risulteranno trasferiti nel nostro conto corrente.</p>
                            
                            <ul class="mt-4 space-y-2 font-medium">
                                <li>Intestato a: <strong class="text-indigo-900 font-bold uppercase tracking-wide">{{ \App\Models\Setting::where('key', 'bonifico_intestazione')->value('value') ?: 'Intestatario non impostato' }}</strong></li>
                                <li>Banca: <strong class="text-indigo-900">{{ \App\Models\Setting::where('key', 'bonifico_banca')->value('value') ?: 'Banca non impostata' }}</strong></li>
                                <li>IBAN: <strong class="text-indigo-900 font-bold text-base bg-white px-2 py-1 rounded border inline-block mt-1 uppercase tracking-widest">{{ \App\Models\Setting::where('key', 'bonifico_iban')->value('value') ?: 'IBAN non impostato' }}</strong></li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('public.home') }}" class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-sm font-bold uppercase tracking-wider rounded-none text-black bg-white hover:bg-gray-50 transition">
                    Continua lo Shopping
                </a>
                <a href="{{ route('public.home') }}" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-bold uppercase tracking-wider rounded-none text-white bg-black hover:bg-zinc-800 transition">
                    Torna alla Home
                </a>
            </div>
            
        </div>
        
    </div>
</div>
@endsection
