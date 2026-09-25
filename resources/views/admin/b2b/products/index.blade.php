<x-app-layout>
    <x-slot name="header">
        @php
            $totalCount = $products->count();
            $syncCount = $products->filter(fn($p) => !empty($p->giacenza_match))->count();
            $notSyncCount = $totalCount - $syncCount;
        @endphp
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Inventario Prodotti B2B') }}
            </h2>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-full uppercase">
                    Totale: {{ $totalCount }}
                </span>
                <span class="text-xs font-bold text-emerald-800 bg-emerald-100 border border-emerald-300 px-3 py-1 rounded-full uppercase flex items-center gap-1">
                    🟢 Sincronizzati: {{ $syncCount }}
                </span>
                @if($notSyncCount > 0)
                    <span class="text-xs font-bold text-rose-800 bg-rose-100 border border-rose-300 px-3 py-1 rounded-full uppercase flex items-center gap-1 animate-pulse">
                        ⚠️ Non Sincronizzati: {{ $notSyncCount }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-2xl shadow-sm text-sm font-bold flex items-center gap-2" role="alert">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-2xl shadow-sm text-sm font-bold flex items-center gap-2" role="alert">
                <span>⚠️</span>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-2xl shadow-sm text-sm font-bold flex items-center gap-2" role="alert">
                <span>❌</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Sezioni Sincronizzazione Dati -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <!-- Sezione Importazione Google Sheet -->
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </span>
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Prodotti da Google Sheet</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                        Importa o aggiorna l'inventario prodotti B2B dal foglio Google. Vengono letti solo i prodotti <strong>"pronta consegna"</strong> in Colonna C.
                    </p>
                </div>
                <form action="{{ route('admin.b2b.products.import') }}" method="POST" class="flex flex-col gap-3 mt-2">
                    @csrf
                    <div>
                        <label for="url" class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">URL CSV Google Sheet</label>
                        <input type="url" name="url" id="url" 
                               value="https://docs.google.com/spreadsheets/d/11HQN1nTtHUPt29p9ZFGH5jHaSk90Ltc19RGlNDis5Dw/export?format=csv&gid=1019847442" 
                               required 
                               class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-yellow-400 focus:ring-yellow-400 text-xs font-mono">
                    </div>
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow text-xs font-black uppercase tracking-wider transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.283 8H18"></path></svg>
                        Sincronizza Prodotti
                    </button>
                </form>
            </div>

            <!-- Sezione Sincronizzazione Giacenze FTPS -->
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>
                        </span>
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Giacenze FTPS (Giacenza.csv)</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                        Scarica l'ultimo file <strong>Giacenza.csv</strong> da FTPS (<code>51.75.145.169 / Output</code>) per aggiornare giacenze e date di consegna.
                    </p>
                </div>
                <form action="{{ route('admin.b2b.products.sync_giacenze') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow text-xs font-black uppercase tracking-wider transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Aggiorna File Giacenze FTPS Ora
                    </button>
                </form>
            </div>
        </div>

        <!-- Filtri e Ricerca -->
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-5">
            @if(!empty($selectedBrand))
                <div class="mb-4 bg-amber-50 border-2 border-amber-300 text-amber-900 p-3.5 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🏷️</span>
                        <span class="text-xs font-bold uppercase tracking-wider">Filtro Linea: <strong class="font-black text-amber-950 text-sm ml-1">{{ $selectedBrand->name }}</strong> ({{ $products->count() }} Prodotti)</span>
                    </div>
                    <a href="{{ route('admin.b2b.products.index') }}" class="text-xs font-black text-amber-950 hover:text-amber-800 bg-amber-200 px-3 py-1 rounded-lg uppercase tracking-wider">✕ Rimuovi Filtro</a>
                </div>
            @endif

            <form action="{{ route('admin.b2b.products.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 max-w-lg">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cerca per codice, nome o linea..." class="block w-full pl-10 pr-3 py-2.5 border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black py-2.5 px-4 rounded-xl shadow transition text-xs uppercase tracking-wider">
                        🔍 Cerca
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('admin.b2b.products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-black py-2.5 px-3 rounded-xl transition text-xs uppercase flex items-center justify-center">
                            ✖ Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Contenitore Lista Prodotti -->
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden min-w-0">
            
            <!-- Vista Mobile (< sm) a riga orizzontale compatta con tasto a DESTRA -->
            <div class="sm:hidden divide-y divide-gray-100">
                @forelse($products as $product)
                    @php
                        $gMatch = $product->giacenza_match ?? null;
                        if ($gMatch) {
                            $curStock = $gMatch['current_stock'] ?? [];
                            $futStock = $gMatch['future_stock'] ?? [];
                            $totPronta = 0;
                            foreach ($curStock as $qty) {
                                if ($qty > 0) $totPronta += $qty;
                            }
                            $totArrivo = 0;
                            foreach ($futStock as $sizes) {
                                $totArrivo += array_sum($sizes);
                            }
                        } else {
                            $totPronta = $product->variants->sum('quantity');
                            $totArrivo = 0;
                        }
                    @endphp
                    <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-yellow-50/40 transition">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <a href="{{ route('admin.b2b.products.show', $product) }}" class="shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-white p-1 border border-gray-200 flex items-center justify-center overflow-hidden shadow-sm">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain" onerror="this.onerror=null; this.src='{{ asset('storage/logo/logo-bicap.png') }}';">
                                </div>
                            </a>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                                    <span class="font-mono text-[10px] font-black text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">{{ $product->code ?? '-' }}</span>
                                    <span class="text-[9px] font-bold uppercase text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded truncate max-w-[80px]">{{ $product->brand->name ?? '' }}</span>
                                    @if($gMatch)
                                        <span class="text-[9px] font-black uppercase text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded-full">🟢 Sync</span>
                                    @else
                                        <span class="text-[9px] font-black uppercase text-rose-700 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded-full animate-pulse">⚠️ No Sync</span>
                                    @endif
                                </div>
                                <a href="{{ route('admin.b2b.products.show', $product) }}" class="block font-black text-xs text-slate-900 truncate hover:text-amber-600 transition">
                                    {{ $product->name }}
                                </a>
                                <div class="flex items-center gap-2 mt-0.5 text-[11px]">
                                    <span class="font-black text-slate-900">€ {{ number_format($product->price, 2, ',', '.') }}</span>
                                    <span class="text-gray-300">•</span>
                                    @if($product->has_stock)
                                        <span class="font-bold text-emerald-700">🟢 {{ $totPronta }} pz</span>
                                        @if($totArrivo > 0)
                                            <span class="text-[10px] font-bold text-amber-700">+{{ $totArrivo }} arr.</span>
                                        @endif
                                    @else
                                        <span class="text-amber-700 font-bold text-[10px]">No Mag.</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <a href="{{ route('admin.b2b.products.show', $product) }}" class="inline-flex items-center gap-1 px-3 py-2 bg-slate-900 hover:bg-yellow-400 hover:text-slate-950 text-white font-black rounded-xl text-[11px] uppercase tracking-wider shadow transition">
                                <span>👁️ DETTAGLIO</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm font-bold text-gray-400">
                        {{ !empty($search) ? 'Nessun prodotto corrisponde alla ricerca.' : 'Nessun prodotto in inventario.' }}
                    </div>
                @endforelse
            </div>

            <!-- Vista Desktop & Tablet (>= sm): Tabella Completa con Tasto a DESTRA -->
            <div class="hidden sm:block overflow-x-auto min-w-0">
                <table class="w-full text-left divide-y divide-gray-100 border-collapse">
                    <thead class="bg-gray-50/80 text-[11px] font-black text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-3 py-3 w-14 text-center">Foto</th>
                            <th class="px-3 py-3 whitespace-nowrap">Codice</th>
                            <th class="px-4 py-3">Prodotto & Modello</th>
                            <th class="px-3 py-3 whitespace-nowrap">Linea</th>
                            <th class="px-3 py-3 whitespace-nowrap text-right">Prezzo</th>
                            <th class="px-3 py-3 whitespace-nowrap text-center">Stato Giacenze</th>
                            <th class="px-3 py-3 whitespace-nowrap text-center">Giacenza</th>
                            <th class="px-4 py-3 whitespace-nowrap text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse($products as $product)
                            @php
                                $gMatch = $product->giacenza_match ?? null;
                                if ($gMatch) {
                                    $curStock = $gMatch['current_stock'] ?? [];
                                    $futStock = $gMatch['future_stock'] ?? [];
                                    $totPronta = 0;
                                    foreach ($curStock as $qty) {
                                        if ($qty > 0) $totPronta += $qty;
                                    }
                                    $totArrivo = 0;
                                    foreach ($futStock as $sizes) {
                                        $totArrivo += array_sum($sizes);
                                    }
                                } else {
                                    $totPronta = $product->variants->sum('quantity');
                                    $totArrivo = 0;
                                }
                            @endphp
                            <tr class="hover:bg-yellow-50/40 transition">
                                <td class="px-3 py-2.5 text-center">
                                    <a href="{{ route('admin.b2b.products.show', $product) }}" class="inline-block">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-11 h-11 object-contain rounded-lg bg-gray-50 border border-gray-200 p-1 hover:border-yellow-400 transition" onerror="this.onerror=null; this.src='{{ asset('storage/logo/logo-bicap.png') }}';">
                                    </a>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap font-mono font-bold text-gray-700">
                                    {{ $product->code ?? '-' }}
                                </td>
                                <td class="px-4 py-2.5">
                                    <a href="{{ route('admin.b2b.products.show', $product) }}" class="group block max-w-xs">
                                        <div class="font-black text-slate-900 group-hover:text-amber-600 transition truncate">{{ $product->name }}</div>
                                        @if(!empty($product->characteristics['CAT-SICUREZZA']))
                                            <span class="inline-block bg-slate-100 text-slate-700 text-[10px] px-1.5 py-0.2 rounded font-mono font-bold mt-0.5">{{ $product->characteristics['CAT-SICUREZZA'] }}</span>
                                        @endif
                                    </a>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <span class="font-bold text-gray-600">{{ $product->brand->name ?? 'N.D.' }}</span>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap text-right font-black text-slate-900 text-sm">
                                    € {{ number_format($product->price, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap text-center">
                                    @if($gMatch)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-black rounded-full uppercase tracking-wider">
                                            <span>🟢</span> Sincronizzato
                                        </span>
                                        <div class="text-[9px] font-mono text-gray-400 mt-0.5" title="Codice gestionale abbinato: {{ $gMatch['raw_code'] ?? '' }}">
                                            {{ $gMatch['raw_code'] ?? '' }}
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-black rounded-full uppercase tracking-wider animate-pulse">
                                            <span>⚠️</span> Non Sincronizzato
                                        </span>
                                        <div class="text-[9px] text-rose-500 font-semibold mt-0.5">
                                            Manca in Giacenza.csv
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap text-center">
                                    @if($product->has_stock)
                                        <div class="font-black text-emerald-700 text-xs">🟢 Pronta: {{ $totPronta }} pz</div>
                                        @if($totArrivo > 0)
                                            <div class="text-[10px] text-amber-700 font-bold mt-0.5">🚚 In arrivo: +{{ $totArrivo }}</div>
                                        @endif
                                        <div class="text-[10px] text-gray-400 font-semibold mt-0.5">{{ $product->variants->count() }} varianti</div>
                                    @else
                                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 font-bold rounded text-[10px]">Senza Magazzino</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.b2b.products.show', $product) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-yellow-400 hover:text-slate-950 text-white font-black rounded-xl text-xs uppercase tracking-wider shadow transition">
                                        <span>👁️ DETTAGLIO</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-400 font-bold">
                                    {{ !empty($search) ? 'Nessun prodotto corrisponde alla ricerca.' : 'Nessun prodotto in inventario.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
