@extends('public.layouts.main')

@section('title', 'I Miei Ordini - Area Personale ' . config('app.name'))

@section('content')
<div class="bg-zinc-50 min-h-screen pt-8 pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center text-xs font-semibold text-zinc-500 mb-6 uppercase tracking-wider">
            <a href="{{ route('public.home') }}" class="hover:text-black transition-colors">Home</a>
            <span class="mx-2 text-zinc-400">/</span>
            <span class="text-zinc-800">Area Personale</span>
            <span class="mx-2 text-zinc-400">/</span>
            <span class="text-[#e35205] font-bold">I Miei Ordini</span>
        </nav>

        <!-- Flash messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-sm font-semibold rounded-none shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 text-sm font-semibold rounded-none shadow-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Banner Benvenuto Utente -->
        <div class="bg-zinc-950 text-white p-6 sm:p-8 rounded-none border-b-4 border-[#fedb00] shadow-lg mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-[#fedb00] text-black text-[10px] font-black uppercase tracking-wider mb-2">
                        PROFILO CLIENTE BICAP
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
                        Bentornato, {{ $user->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-zinc-400 mt-1">
                        {{ $user->email }} &bull; Cliente dal {{ $user->created_at ? $user->created_at->format('M Y') : 'Recente' }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('public.account.profile') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 text-xs font-bold uppercase tracking-wider border border-white/20 transition-colors">
                        <svg class="w-4 h-4 text-[#fedb00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Modifica Dati Personali</span>
                    </a>
                    <form method="POST" action="{{ route('public.account.logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 bg-rose-950/60 hover:bg-rose-900 text-rose-300 px-4 py-2.5 text-xs font-bold uppercase tracking-wider border border-rose-800/50 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Esci</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Grid Layout: Sidebar & Content -->
        <div class="lg:grid lg:grid-cols-12 lg:gap-x-8 items-start">
            
            <!-- Sidebar Navigazione Area Personale -->
            <aside class="lg:col-span-3 mb-8 lg:mb-0 space-y-4">
                <div class="bg-white border border-zinc-200 p-4 shadow-sm">
                    <h2 class="text-xs font-black uppercase tracking-wider text-zinc-400 mb-3 px-3">
                        Navigazione
                    </h2>
                    <nav class="space-y-1">
                        <a href="{{ route('public.account.dashboard') }}" 
                           class="bg-zinc-950 text-[#fedb00] group rounded-none px-3.5 py-2.5 flex items-center justify-between text-xs font-black uppercase tracking-wider">
                            <span class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-[#fedb00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>I Miei Ordini</span>
                            </span>
                            <span class="bg-[#fedb00] text-black px-1.5 py-0.5 text-[10px] font-black rounded-none">
                                {{ $orders->count() }}
                            </span>
                        </a>

                        <a href="{{ route('public.account.profile') }}" 
                           class="text-zinc-700 hover:text-black hover:bg-zinc-100 group rounded-none px-3.5 py-2.5 flex items-center text-xs font-bold uppercase tracking-wider transition-colors">
                            <span class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-zinc-400 group-hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Dati Personali & Spedizione</span>
                            </span>
                        </a>
                    </nav>
                </div>

                <!-- Info Box Assistenza -->
                <div class="bg-zinc-100 border border-zinc-200 p-5 text-xs text-zinc-600">
                    <h3 class="font-black text-black uppercase tracking-wider mb-1.5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#e35205]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Hai bisogno di aiuto?</span>
                    </h3>
                    <p class="mb-3 text-[11px] leading-relaxed">
                        Per qualsiasi domanda sui tuoi ordini o sulle spedizioni, il nostro servizio clienti è a tua disposizione.
                    </p>
                    <a href="mailto:info@bicap.it" class="inline-flex items-center gap-1 font-bold text-black hover:text-[#e35205] underline transition-colors">
                        <span>Contatta info@bicap.it</span> &rarr;
                    </a>
                </div>
            </aside>

            <!-- Main Content: Storico & Dettaglio Ordini -->
            <div class="lg:col-span-9 space-y-6">
                
                <div class="flex items-center justify-between border-b border-zinc-200 pb-4">
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Riepilogo Ordini</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Consulta lo storico completo dei tuoi acquisti e i dettagli di ciascuna spedizione.</p>
                    </div>
                    <span class="text-xs font-bold text-zinc-600 bg-zinc-200 px-3 py-1">
                        {{ $orders->count() }} {{ $orders->count() === 1 ? 'ordine' : 'ordini' }}
                    </span>
                </div>

                @if($orders->isEmpty())
                    <!-- Empty State -->
                    <div class="bg-white border border-zinc-200 p-12 text-center shadow-sm">
                        <div class="w-16 h-16 mx-auto mb-4 bg-zinc-100 flex items-center justify-center text-zinc-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <h3 class="text-lg font-black uppercase tracking-tight text-zinc-900 mb-1">
                            Non hai ancora effettuato nessun ordine
                        </h3>
                        <p class="text-xs text-zinc-500 max-w-md mx-auto mb-6">
                            Esplora il nostro catalogo di calzature antinfortunistiche e trova il modello più adatto alle tue esigenze professionali.
                        </p>
                        <a href="{{ route('public.home') }}" 
                           class="inline-flex items-center gap-2 bg-[#fedb00] hover:bg-[#e6c600] text-black font-black text-xs uppercase tracking-wider px-6 py-3 transition-colors shadow-sm">
                            <span>Vai al Catalogo Prodotti</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                @else
                    <!-- Lista Ordini con Dettaglio Espandibile -->
                    <div class="space-y-6">
                        @foreach($orders as $order)
                            <div class="bg-white border border-zinc-200 shadow-sm overflow-hidden" 
                                 x-data="{ openDetail: false }"
                                 id="ordine-{{ $order->id }}">
                                
                                <!-- Card Header: Informazioni Principali Ordine -->
                                <div class="bg-zinc-100/80 p-4 sm:p-5 border-b border-zinc-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                                        <div>
                                            <span class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400">Numero Ordine</span>
                                            <span class="font-black text-black text-sm">#{{ $order->numero_ordine }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400">Data e Ora</span>
                                            <span class="font-semibold text-zinc-700">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400">Totale Ordine</span>
                                            <span class="font-black text-black text-sm">€ {{ number_format($order->totale_ordine, 2, ',', '.') }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400">Stato Ordine</span>
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
                                            <span class="inline-block mt-0.5 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider border {{ $statoClass }}">
                                                {{ $statoLabel }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Azione Espansione Dettaglio -->
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" 
                                                @click="openDetail = !openDetail" 
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-black hover:bg-zinc-800 text-white text-xs font-bold uppercase tracking-wider transition-colors cursor-pointer border-0">
                                            <span x-text="openDetail ? 'Chiudi Dettaglio' : 'Vedi Dettaglio Ordine'"></span>
                                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': openDetail }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                        <a href="{{ route('public.account.orders.detail', $order->id) }}" 
                                           title="Apri pagina dedicata" 
                                           class="p-2 border border-zinc-300 hover:border-black text-zinc-600 hover:text-black transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                </div>

                                <!-- Anteprima Prodotti (Sempre visibile a colpo d'occhio) -->
                                <div class="p-4 sm:p-5">
                                    <div class="flex flex-wrap items-center gap-4">
                                        @foreach($order->items->take(4) as $item)
                                            <div class="flex items-center gap-3 bg-zinc-50 border border-zinc-200 p-2 pr-4 rounded-none max-w-xs">
                                                @if($item->image)
                                                    <img src="{{ $item->image }}" alt="{{ $item->nome_prodotto }}" class="w-12 h-12 object-contain bg-white border border-zinc-200 shrink-0">
                                                @else
                                                    <div class="w-12 h-12 bg-zinc-200 text-zinc-400 flex items-center justify-center shrink-0">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                @endif
                                                <div class="text-xs truncate">
                                                    <p class="font-bold text-black truncate">{{ $item->nome_prodotto }}</p>
                                                    <p class="text-[11px] text-zinc-500">
                                                        Taglia: <span class="font-bold text-zinc-700">{{ $item->taglia ?? 'Unica' }}</span> &bull; Q.tà: <span class="font-bold text-zinc-700">{{ $item->quantita }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 4)
                                            <div class="text-xs font-bold text-zinc-500">
                                                + altri {{ $order->items->count() - 4 }} articoli...
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- SEZIONE DETTAGLIO COMPLETO ORDINE (Espandibile) -->
                                <div x-show="openDetail" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-2"
                                     style="display: none;" 
                                     class="border-t border-zinc-200 bg-zinc-50/50 p-4 sm:p-6 space-y-6">
                                    
                                    <!-- Timeline Avanzamento Ordine -->
                                    <div class="bg-white p-4 border border-zinc-200">
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-3">
                                            Avanzamento Spedizione
                                        </div>
                                        <div class="grid grid-cols-4 text-center text-xs">
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
                                                <div class="w-6 h-6 mx-auto rounded-full flex items-center justify-center text-[11px] font-black {{ $step >= 1 ? 'bg-black text-[#fedb00]' : 'bg-zinc-200 text-zinc-400' }}">1</div>
                                                <span class="block text-[11px] font-bold {{ $step >= 1 ? 'text-black' : 'text-zinc-400' }}">Ricevuto</span>
                                            </div>
                                            <div class="space-y-1.5">
                                                <div class="w-6 h-6 mx-auto rounded-full flex items-center justify-center text-[11px] font-black {{ $step >= 2 ? 'bg-black text-[#fedb00]' : 'bg-zinc-200 text-zinc-400' }}">2</div>
                                                <span class="block text-[11px] font-bold {{ $step >= 2 ? 'text-black' : 'text-zinc-400' }}">In Preparazione</span>
                                            </div>
                                            <div class="space-y-1.5">
                                                <div class="w-6 h-6 mx-auto rounded-full flex items-center justify-center text-[11px] font-black {{ $step >= 3 ? 'bg-black text-[#fedb00]' : 'bg-zinc-200 text-zinc-400' }}">3</div>
                                                <span class="block text-[11px] font-bold {{ $step >= 3 ? 'text-black' : 'text-zinc-400' }}">Spedito</span>
                                            </div>
                                            <div class="space-y-1.5">
                                                <div class="w-6 h-6 mx-auto rounded-full flex items-center justify-center text-[11px] font-black {{ $step >= 4 ? 'bg-emerald-600 text-white' : 'bg-zinc-200 text-zinc-400' }}">4</div>
                                                <span class="block text-[11px] font-bold {{ $step >= 4 ? 'text-emerald-700' : 'text-zinc-400' }}">Consegnato</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tabella Articoli con Foto e Dettagli -->
                                    <div class="bg-white border border-zinc-200 overflow-hidden">
                                        <div class="px-4 py-3 bg-zinc-950 text-white text-xs font-black uppercase tracking-wider flex items-center justify-between">
                                            <span>Articoli Acquistati</span>
                                            <span>{{ $order->items->sum('quantita') }} Pz</span>
                                        </div>
                                        <div class="divide-y divide-zinc-200">
                                            @foreach($order->items as $item)
                                                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                    <div class="flex items-center gap-4">
                                                        @if($item->image)
                                                            <img src="{{ $item->image }}" alt="{{ $item->nome_prodotto }}" class="w-16 h-16 object-contain bg-white border border-zinc-200 p-1 shrink-0">
                                                        @else
                                                            <div class="w-16 h-16 bg-zinc-100 text-zinc-400 flex items-center justify-center shrink-0 border border-zinc-200">
                                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h4 class="text-sm font-black text-black uppercase tracking-tight">{{ $item->nome_prodotto }}</h4>
                                                            <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-zinc-500">
                                                                @if($item->sku)
                                                                    <span>Codice: <strong class="text-zinc-700">{{ $item->sku }}</strong></span>
                                                                @endif
                                                                <span>Colore: <strong class="text-zinc-700">{{ $item->colore ?? 'Standard' }}</strong></span>
                                                                <span>Taglia: <strong class="text-zinc-700 font-black">{{ $item->taglia ?? 'Unica' }}</strong></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="text-right sm:text-right flex sm:flex-col justify-between items-center sm:items-end">
                                                        <div class="text-xs text-zinc-500">
                                                            {{ $item->quantita }} x € {{ number_format($item->prezzo_unitario, 2, ',', '.') }}
                                                        </div>
                                                        <div class="text-sm font-black text-black mt-0.5">
                                                            € {{ number_format($item->subtotale, 2, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Riepilogo Contabile Ordine -->
                                        <div class="bg-zinc-50 p-4 border-t border-zinc-200">
                                            <div class="max-w-xs ml-auto space-y-1.5 text-xs">
                                                <div class="flex justify-between text-zinc-600">
                                                    <span>Subtotale Articoli:</span>
                                                    <span class="font-bold text-zinc-800">€ {{ number_format($order->totale_imponibile, 2, ',', '.') }}</span>
                                                </div>
                                                <div class="flex justify-between text-zinc-600">
                                                    <span>Spedizione:</span>
                                                    <span class="font-bold text-zinc-800">
                                                        @if((float)$order->costo_spedizione == 0)
                                                            <span class="text-emerald-700 font-bold uppercase">Gratuita</span>
                                                        @else
                                                            € {{ number_format($order->costo_spedizione, 2, ',', '.') }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="flex justify-between text-sm font-black text-black pt-2 border-t border-zinc-200">
                                                    <span>Totale Complessivo:</span>
                                                    <span class="text-base text-[#e35205]">€ {{ number_format($order->totale_ordine, 2, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Indirizzo di Spedizione e Metodo Pagamento -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="bg-white p-4 border border-zinc-200">
                                            <h5 class="text-xs font-black uppercase tracking-wider text-black mb-2 flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-[#fedb00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>Destinazione di Spedizione</span>
                                            </h5>
                                            <div class="text-xs text-zinc-700 space-y-0.5">
                                                <p class="font-bold text-black">{{ $order->spedizione_nome }}</p>
                                                <p>{{ $order->spedizione_indirizzo }}</p>
                                                <p>{{ $order->spedizione_cap }} {{ $order->spedizione_citta }} ({{ $order->spedizione_provincia ?? '' }})</p>
                                                <p class="font-semibold">{{ $order->spedizione_nazione }}</p>
                                                @if(!empty($order->customer?->telefono))
                                                    <p class="text-zinc-500 mt-2">Tel: {{ $order->customer->telefono }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="bg-white p-4 border border-zinc-200">
                                            <h5 class="text-xs font-black uppercase tracking-wider text-black mb-2 flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-[#fedb00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                                <span>Pagamento & Fatturazione</span>
                                            </h5>
                                            <div class="text-xs text-zinc-700 space-y-1">
                                                <p>
                                                    Metodo: <strong class="text-black uppercase">{{ $order->metodo_pagamento }}</strong>
                                                </p>
                                                <p>
                                                    Stato Pagamento: 
                                                    <span class="font-bold {{ $order->stato_pagamento === 'pagato' ? 'text-emerald-700' : 'text-amber-700' }}">
                                                        {{ strtoupper($order->stato_pagamento ?? 'attesa') }}
                                                    </span>
                                                </p>
                                                @if(!empty($order->customer?->ragione_sociale))
                                                    <div class="mt-2 pt-2 border-t border-zinc-100 text-[11px] text-zinc-500">
                                                        <p class="font-bold text-zinc-800">Fattura intestata a:</p>
                                                        <p>{{ $order->customer->ragione_sociale }}</p>
                                                        <p>P.IVA: {{ $order->customer->partita_iva }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if(!empty($order->note_cliente))
                                        <div class="bg-amber-50 border border-amber-200 p-3 text-xs text-amber-900">
                                            <span class="font-bold">Note per la consegna:</span> {{ $order->note_cliente }}
                                        </div>
                                    @endif

                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif

            </div>

        </div>

    </div>
</div>
@endsection
