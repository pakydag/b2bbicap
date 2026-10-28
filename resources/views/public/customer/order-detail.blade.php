@extends('public.layouts.main')

@section('title', 'Dettaglio Ordine #' . $order->numero_ordine . ' - ' . config('app.name'))

@section('content')
<div class="bg-zinc-50 min-h-screen pt-8 pb-24">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center text-xs font-semibold text-zinc-500 mb-6 uppercase tracking-wider">
            <a href="{{ route('public.home') }}" class="hover:text-black transition-colors">Home</a>
            <span class="mx-2 text-zinc-400">/</span>
            <a href="{{ route('public.account.dashboard') }}" class="hover:text-black transition-colors">Area Personale</a>
            <span class="mx-2 text-zinc-400">/</span>
            <a href="{{ route('public.account.dashboard') }}" class="hover:text-black transition-colors">I Miei Ordini</a>
            <span class="mx-2 text-zinc-400">/</span>
            <span class="text-[#e35205] font-bold">Ordine #{{ $order->numero_ordine }}</span>
        </nav>

        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('public.account.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-zinc-600 hover:text-black transition-colors">
                <span>&larr;</span> <span>Torna a tutti gli ordini</span>
            </a>

            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 bg-white border border-zinc-300 hover:border-black text-black px-4 py-2 text-xs font-bold uppercase tracking-wider transition-colors shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Stampa / PDF</span>
            </button>
        </div>

        <!-- Main Order Detail Card -->
        <div class="bg-white border border-zinc-200 shadow-md overflow-hidden">
            
            <!-- Header Ordine -->
            <div class="bg-zinc-950 p-6 sm:p-8 text-white border-b-4 border-[#fedb00]">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-[#fedb00] text-black text-[10px] font-black uppercase tracking-wider mb-2">
                            ORDINE BICAP
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
                            Ordine #{{ $order->numero_ordine }}
                        </h1>
                        <p class="text-xs sm:text-sm text-zinc-400 mt-1">
                            Effettuato il {{ $order->created_at ? $order->created_at->format('d/m/Y \a\l\l\e H:i') : '-' }}
                        </p>
                    </div>

                    <div class="text-left sm:text-right">
                        @php
                            $statoClass = match(strtolower($order->stato ?? '')) {
                                'nuovo' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'in_lavorazione' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'spedito' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                'consegnato' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'annullato' => 'bg-rose-100 text-rose-800 border-rose-200',
                                default => 'bg-zinc-200 text-zinc-800 border-zinc-300',
                            };
                            $statoLabel = match(strtolower($order->stato ?? '')) {
                                'nuovo' => 'Ricevuto',
                                'in_lavorazione' => 'In Lavorazione',
                                'spedito' => 'Spedito',
                                'consegnato' => 'Consegnato',
                                'annullato' => 'Annullato',
                                default => ucfirst($order->stato ?? 'In attesa'),
                            };
                        @endphp
                        <span class="inline-block px-3 py-1 text-xs font-black uppercase tracking-wider border {{ $statoClass }}">
                            {{ $statoLabel }}
                        </span>
                        <div class="text-xl sm:text-2xl font-black text-[#fedb00] mt-2">
                            € {{ number_format($order->totale_ordine, 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tracker Spedizione -->
            <div class="bg-zinc-100/70 p-6 border-b border-zinc-200">
                <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-4 text-center">
                    Stato di Elaborazione e Spedizione
                </div>
                <div class="grid grid-cols-4 text-center text-xs max-w-2xl mx-auto">
                    @php
                        $step = match(strtolower($order->stato ?? '')) {
                            'nuovo' => 1,
                            'in_lavorazione' => 2,
                            'spedito' => 3,
                            'consegnato' => 4,
                            'annullato' => 0,
                            default => 1,
                        };
                    @endphp
                    <div class="space-y-1.5">
                        <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center text-xs font-black {{ $step >= 1 ? 'bg-black text-[#fedb00]' : 'bg-zinc-200 text-zinc-400' }}">1</div>
                        <span class="block text-xs font-bold {{ $step >= 1 ? 'text-black' : 'text-zinc-400' }}">Ricevuto</span>
                    </div>
                    <div class="space-y-1.5">
                        <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center text-xs font-black {{ $step >= 2 ? 'bg-black text-[#fedb00]' : 'bg-zinc-200 text-zinc-400' }}">2</div>
                        <span class="block text-xs font-bold {{ $step >= 2 ? 'text-black' : 'text-zinc-400' }}">In Preparazione</span>
                    </div>
                    <div class="space-y-1.5">
                        <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center text-xs font-black {{ $step >= 3 ? 'bg-black text-[#fedb00]' : 'bg-zinc-200 text-zinc-400' }}">3</div>
                        <span class="block text-xs font-bold {{ $step >= 3 ? 'text-black' : 'text-zinc-400' }}">Spedito</span>
                    </div>
                    <div class="space-y-1.5">
                        <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center text-xs font-black {{ $step >= 4 ? 'bg-emerald-600 text-white' : 'bg-zinc-200 text-zinc-400' }}">4</div>
                        <span class="block text-xs font-bold {{ $step >= 4 ? 'text-emerald-700' : 'text-zinc-400' }}">Consegnato</span>
                    </div>
                </div>
            </div>

            <!-- Tabella Articoli Acquistati -->
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-wider text-black mb-4">
                        Dettaglio Articoli Ordinati ({{ $order->items->count() }})
                    </h2>
                    <div class="border border-zinc-200 divide-y divide-zinc-200">
                        @foreach($order->items as $item)
                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    @if($item->image)
                                        <img src="{{ $item->image }}" alt="{{ $item->nome_prodotto }}" class="w-20 h-20 object-contain bg-white border border-zinc-200 p-1 shrink-0">
                                    @else
                                        <div class="w-20 h-20 bg-zinc-100 text-zinc-400 flex items-center justify-center shrink-0 border border-zinc-200">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-base font-black text-black uppercase tracking-tight">{{ $item->nome_prodotto }}</h3>
                                        <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-zinc-600">
                                            @if($item->sku)
                                                <span>SKU: <strong class="text-zinc-800">{{ $item->sku }}</strong></span>
                                            @endif
                                            <span>Colore: <strong class="text-zinc-800">{{ $item->colore ?? 'Standard' }}</strong></span>
                                            <span>Taglia: <strong class="text-zinc-900 font-black text-sm">{{ $item->taglia ?? 'Unica' }}</strong></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right sm:text-right flex sm:flex-col justify-between items-center sm:items-end">
                                    <div class="text-xs text-zinc-500">
                                        {{ $item->quantita }} x € {{ number_format($item->prezzo_unitario, 2, ',', '.') }}
                                    </div>
                                    <div class="text-base font-black text-black mt-1">
                                        € {{ number_format($item->subtotale, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Riepilogo Finanziario -->
                <div class="bg-zinc-50 border border-zinc-200 p-6">
                    <div class="max-w-xs ml-auto space-y-2 text-xs">
                        <div class="flex justify-between text-zinc-600">
                            <span>Subtotale Articoli:</span>
                            <span class="font-bold text-zinc-800">€ {{ number_format($order->totale_imponibile, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-zinc-600">
                            <span>Spese di Spedizione:</span>
                            <span class="font-bold text-zinc-800">
                                @if((float)$order->costo_spedizione == 0)
                                    <span class="text-emerald-700 font-bold uppercase">Gratuita</span>
                                @else
                                    € {{ number_format($order->costo_spedizione, 2, ',', '.') }}
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-base font-black text-black pt-3 border-t border-zinc-300">
                            <span>Totale Pagato:</span>
                            <span class="text-lg text-[#e35205]">€ {{ number_format($order->totale_ordine, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Dati Spedizione e Fatturazione -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-zinc-200">
                    <div class="bg-zinc-50 border border-zinc-200 p-5">
                        <h4 class="text-xs font-black uppercase tracking-wider text-black mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#fedb00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <span>Indirizzo di Consegna</span>
                        </h4>
                        <div class="text-xs text-zinc-700 space-y-1">
                            <p class="font-bold text-black text-sm">{{ $order->spedizione_nome }}</p>
                            <p>{{ $order->spedizione_indirizzo }}</p>
                            <p>{{ $order->spedizione_cap }} {{ $order->spedizione_citta }} ({{ $order->spedizione_provincia ?? '' }})</p>
                            <p class="font-semibold">{{ $order->spedizione_nazione }}</p>
                            @if(!empty($order->customer?->telefono))
                                <p class="text-zinc-500 pt-2">Telefono: {{ $order->customer->telefono }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-zinc-50 border border-zinc-200 p-5">
                        <h4 class="text-xs font-black uppercase tracking-wider text-black mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#fedb00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Dati di Pagamento</span>
                        </h4>
                        <div class="text-xs text-zinc-700 space-y-1.5">
                            <p>Metodo Scelto: <strong class="text-black uppercase">{{ $order->metodo_pagamento }}</strong></p>
                            <p>
                                Stato Transazione: 
                                <span class="font-bold {{ $order->stato_pagamento === 'pagato' ? 'text-emerald-700' : 'text-amber-700' }}">
                                    {{ strtoupper($order->stato_pagamento ?? 'attesa') }}
                                </span>
                            </p>
                            @if(!empty($order->customer?->ragione_sociale))
                                <div class="mt-3 pt-3 border-t border-zinc-200 text-xs text-zinc-600">
                                    <p class="font-bold text-black">Intestazione Fattura:</p>
                                    <p>{{ $order->customer->ragione_sociale }}</p>
                                    <p>Partita IVA: {{ $order->customer->partita_iva }}</p>
                                    @if(!empty($order->customer->sdi))
                                        <p>SDI: {{ $order->customer->sdi }}</p>
                                    @endif
                                    @if(!empty($order->customer->pec))
                                        <p>PEC: {{ $order->customer->pec }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!empty($order->note_cliente))
                    <div class="bg-amber-50 border border-amber-200 p-4 text-xs text-amber-900">
                        <span class="font-bold uppercase tracking-wider block mb-1">Note fornite durante il checkout:</span>
                        {{ $order->note_cliente }}
                    </div>
                @endif

            </div>

        </div>

    </div>
</div>
@endsection
