<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.b2b.products.index') }}" class="text-gray-500 hover:text-gray-900 transition">
                    <span class="text-xl">←</span>
                </a>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    Dettaglio Prodotto B2B: <span class="text-indigo-600 font-black">{{ $product->name }}</span>
                </h2>
            </div>
            <div class="flex items-center gap-2">
                @if(!empty($lastGiacenzeSync))
                    <span class="text-xs font-bold text-indigo-900 bg-indigo-50 border border-indigo-200 px-3 py-1.5 rounded-full uppercase flex items-center gap-1.5 shadow-sm" title="Data e ora dell'ultimo scaricamento e sincronizzazione del file Giacenza.csv">
                        <span>🕒</span> Sync: <strong class="text-indigo-950">{{ $lastGiacenzeSync->format('d/m/Y H:i') }}</strong>
                        <span class="text-[10px] text-indigo-600 font-normal">({{ $lastGiacenzeSync->diffForHumans() }})</span>
                    </span>
                @endif
                <a href="{{ route('admin.b2b.products.index') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-slate-900 text-white font-black rounded-xl text-xs uppercase tracking-wider hover:bg-yellow-400 hover:text-slate-950 transition shadow">
                    ← Torna all'Inventario
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Banner Informato: Dati Sincronizzati da File -->
            <div class="bg-blue-50 border-2 border-blue-200 text-blue-900 p-4 rounded-2xl shadow-sm flex items-center gap-3">
                <span class="text-2xl">ℹ️</span>
                <div class="text-xs leading-relaxed font-medium">
                    <strong class="font-black uppercase tracking-wide block text-blue-950">Scheda Prodotto in Sola Lettura</strong>
                    Le informazioni di questo articolo (prezzi, taglie, descrizioni e disponibilità di magazzino) vengono sincronizzate automaticamente dai file aziendali e non sono modificabili manualmente per garantire la massima accuratezza con il gestionale.
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Colonna Sinistra: Foto e Dati Principali -->
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm text-center">
                        <div class="bg-gray-50 rounded-2xl p-6 mb-6 flex items-center justify-center border border-gray-100 min-h-[260px]">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-56 max-w-full object-contain filter drop-shadow">
                        </div>

                        <span class="inline-block px-3 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-black uppercase tracking-widest rounded-full mb-3">
                            {{ $product->brand->name ?? 'Linea Non Specificata' }}
                        </span>

                        <h3 class="text-2xl font-black text-slate-900 tracking-tight uppercase mb-1">{{ $product->name }}</h3>
                        <p class="text-xs font-mono font-bold text-gray-500 mb-3">COD. {{ $product->code }}</p>

                        @if(!empty($giacenzaMatch))
                            <div class="mb-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-black rounded-full uppercase tracking-wider">
                                    <span>🟢</span> Giacenza Sincronizzata
                                </span>
                                @if(!empty($giacenzaMatch['raw_code']))
                                    <p class="text-[10px] font-mono text-gray-400 mt-1">Codice Gestionale: <strong class="text-gray-600">{{ $giacenzaMatch['raw_code'] }}</strong></p>
                                @endif
                            </div>
                        @else
                            <div class="mb-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 text-xs font-black rounded-full uppercase tracking-wider animate-pulse">
                                    <span>⚠️</span> Non Sincronizzato (Manca in Giacenza.csv)
                                </span>
                            </div>
                        @endif

@php
    $currentStock = $giacenzaMatch['current_stock'] ?? [];
    $futureStockGrouped = $giacenzaMatch['future_stock'] ?? [];
    
    $totProntaDisponibile = 0;
    $totProntaImpegnata = 0;
    foreach ($currentStock as $s => $q) {
        if ($q > 0) {
            $totProntaDisponibile += $q;
        } else {
            $totProntaImpegnata += abs($q);
        }
    }
    
    $totProntaNetto = array_sum($currentStock);
    $totArrivo = 0;
    foreach ($futureStockGrouped as $d => $sizesArr) {
        $totArrivo += array_sum($sizesArr);
    }
    
    if (empty($giacenzaMatch)) {
        $totProntaDisponibile = $product->variants->sum('stock');
        $totProntaNetto = $totProntaDisponibile;
        foreach ($product->variants as $v) {
            $currentStock[$v->size] = $v->stock;
        }
    }

    $totGiacenzaGlobale = $totProntaNetto + $totArrivo;

    $allSizes = $product->variants->pluck('size')->toArray();
    $allSizes = array_unique(array_merge($allSizes, array_keys($currentStock)));
    foreach ($futureStockGrouped as $d => $sArr) {
        $allSizes = array_merge($allSizes, array_keys($sArr));
    }
    $allSizes = array_filter(array_unique($allSizes));
    sort($allSizes, SORT_NUMERIC);
@endphp

                        <div class="grid grid-cols-3 gap-2 border-t border-gray-100 pt-4 mt-2">
                            <div class="bg-gray-50 p-2.5 rounded-2xl border border-gray-100 text-center">
                                <span class="block text-[9px] font-bold text-gray-400 uppercase">Prezzo B2B</span>
                                <span class="text-sm font-black text-slate-900">€ {{ number_format($product->price, 2, ',', '.') }}</span>
                            </div>
                            <div class="bg-emerald-50/70 p-2.5 rounded-2xl border border-emerald-100 text-center">
                                <span class="block text-[9px] font-bold text-emerald-800 uppercase">Pronta Consegna</span>
                                <span class="text-sm font-black text-emerald-700">🟢 {{ $totProntaDisponibile }}</span>
                                @if($totProntaImpegnata > 0)
                                    <span class="block text-[9px] font-bold text-rose-600 mt-0.5" title="Quantità impegnata su ordini pregressi">-{{ $totProntaImpegnata }} imp.</span>
                                @endif
                            </div>
                            <div class="bg-amber-50/70 p-2.5 rounded-2xl border border-amber-100 text-center">
                                <span class="block text-[9px] font-bold text-amber-800 uppercase">In Arrivo</span>
                                <span class="text-sm font-black text-amber-700">🚚 {{ $totArrivo }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Scheda Descrizione -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <h4 class="font-black text-xs uppercase tracking-widest text-gray-400 mb-3 border-b border-gray-100 pb-2">Descrizione Prodotto</h4>
                        <div class="text-xs text-gray-700 leading-relaxed space-y-2">
                            @if($product->description)
                                {!! nl2br(e($product->description)) !!}
                            @else
                                <p class="text-gray-400 italic">Nessuna descrizione testuale associata a questo articolo.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Colonna Destra: Varianti Taglie & Caratteristiche Tecniche -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Varianti Taglie & Giacenze -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <div class="flex flex-wrap justify-between items-center mb-4 border-b border-gray-100 pb-3 gap-2">
                            <div>
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-900">Disponibilità Stock Per Taglia</h4>
                                <p class="text-xs text-gray-500">Pronta Consegna e Merce in Arrivo per ciascuna taglia</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="bg-emerald-100 text-emerald-900 border border-emerald-300 text-xs font-black px-3 py-1 rounded-full uppercase flex items-center gap-1">
                                    <span>🟢 Pronta Consegna:</span> <strong>{{ $totProntaDisponibile }}</strong>
                                </span>
                                @if($totProntaImpegnata > 0)
                                    <span class="bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold px-2 py-1 rounded-full uppercase" title="Totale impegnato su ordini">
                                        ⚠️ Impegnati: -{{ $totProntaImpegnata }}
                                    </span>
                                @endif
                                @if($totArrivo > 0)
                                    <span class="bg-amber-100 text-amber-900 border border-amber-300 text-xs font-black px-3 py-1 rounded-full uppercase flex items-center gap-1">
                                        <span>🚚 In Arrivo:</span> <strong>{{ $totArrivo }}</strong>
                                    </span>
                                @endif
                                <span class="bg-slate-900 text-white text-xs font-black px-3 py-1 rounded-full uppercase" title="Disponibilità netta globale (Pronta consegna + Arrivi - Impegnati)">
                                    Netto: {{ $totGiacenzaGlobale }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-7 gap-2">
                            @forelse($allSizes as $size)
                                @php
                                    $pQty = $currentStock[$size] ?? 0;
                                    $futureEntries = [];
                                    foreach ($futureStockGrouped as $date => $sizesArr) {
                                        if (!empty($sizesArr[$size]) && $sizesArr[$size] > 0) {
                                            $futureEntries[] = ['date' => $date, 'qty' => $sizesArr[$size]];
                                        }
                                    }
                                    $hasFuture = !empty($futureEntries);
                                @endphp
                                <div class="py-2 px-1 rounded-xl border text-center transition flex flex-col justify-between {{ $pQty > 0 ? 'bg-emerald-50/60 border-emerald-200' : ($hasFuture ? 'bg-amber-50/60 border-amber-200' : 'bg-gray-50 border-gray-200 opacity-50') }}">
                                    <span class="block text-xs font-black text-slate-900 uppercase">Tg. {{ $size }}</span>
                                    
                                    <div class="my-1 space-y-0.5">
                                        <div class="text-xs font-black {{ $pQty > 0 ? 'text-emerald-700' : ($pQty < 0 ? 'text-rose-600' : 'text-gray-400') }}">
                                            @if($pQty > 0)
                                                🟢 {{ $pQty }}
                                            @elseif($pQty < 0)
                                                <span class="text-rose-600 font-bold" title="Disponibili: 0 | Impegnati su ordini: {{ abs($pQty) }}">
                                                    0 <span class="text-[10px] text-rose-500 font-semibold">({{ $pQty }})</span>
                                                </span>
                                            @else
                                                <span class="text-gray-300">0</span>
                                            @endif
                                        </div>

                                        @foreach($futureEntries as $fEntry)
                                            <div class="text-[10px] font-bold text-amber-800 bg-amber-100/90 rounded py-0.5 px-1 leading-tight shadow-sm" title="In arrivo il {{ $fEntry['date'] }}">
                                                🚚 +{{ $fEntry['qty'] }}
                                                <span class="block text-[8px] font-semibold text-amber-900 leading-none mt-0.5">{{ $fEntry['date'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-4 text-xs text-gray-400 font-bold uppercase tracking-wider">
                                    Nessuna variante taglia registrata per questo prodotto.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Caratteristiche Tecniche B2B -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <h4 class="font-black text-sm uppercase tracking-wider text-slate-900 mb-4 border-b border-gray-100 pb-3">Caratteristiche Tecniche & Certificazioni</h4>
                        
                        @php
                            $chars = is_array($product->characteristics) ? $product->characteristics : json_decode($product->characteristics ?? '[]', true);
                        @endphp

                        @if(!empty($chars))
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($chars as $key => $value)
                                    @php
                                        $valStr = trim((string)$value);
                                        $keyStr = trim((string)$key);
                                        $keyLower = strtolower($keyStr);
                                        $valLower = strtolower($valStr);

                                        // Se il valore è vuoto o uguale alla chiave (es. FOTO-AGGIUNTIVA-1 => FOTO-AGGIUNTIVA-1), ignora
                                        if (empty($valStr) || strcasecmp($valStr, $keyStr) === 0) {
                                            continue;
                                        }

                                        // Se la chiave o il valore riguardano foto/immagini ma il valore NON è un URL reale, ignora
                                        $isPhotoField = str_contains($keyLower, 'foto') || str_contains($keyLower, 'image') || str_contains($valLower, 'foto');
                                        $isValidUrl = \Illuminate\Support\Str::startsWith($valLower, ['http://', 'https://', '/storage/']);

                                        if ($isPhotoField && !$isValidUrl) {
                                            continue;
                                        }

                                        $isHtml = (str_contains($valStr, '<') && str_contains($valStr, '>'));
                                        $isImage = $isPhotoField && $isValidUrl;
                                        $fullWidth = $isHtml || strlen($valStr) > 120;
                                    @endphp

                                    @if($isImage)
                                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex flex-col items-start gap-2">
                                            <span class="block text-[10px] font-black uppercase text-indigo-600 tracking-wider">{{ str_replace(['_', '-'], ' ', $key) }}</span>
                                            <div class="flex items-center gap-3 w-full">
                                                <img src="{{ $valStr }}" alt="{{ $key }}" class="h-16 w-auto object-contain rounded-lg border border-gray-200 bg-white p-1 shadow-sm" onerror="this.closest('.bg-gray-50').style.display='none';">
                                                <a href="{{ $valStr }}" target="_blank" class="text-[11px] text-indigo-600 underline font-semibold hover:text-indigo-800">Apri Immagine Originale ↗</a>
                                            </div>
                                        </div>
                                    @elseif($isHtml)
                                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 md:col-span-2">
                                            <span class="block text-[10px] font-black uppercase text-indigo-600 tracking-wider mb-1">{{ str_replace(['_', '-'], ' ', $key) }}</span>
                                            <div class="text-xs text-slate-800 leading-relaxed mt-1 border-t border-gray-200/60 pt-2 font-sans space-y-2 [&_h3]:font-black [&_h3]:text-slate-900 [&_h3]:text-sm [&_h3]:mt-2 [&_p]:my-1 [&_strong]:font-bold [&_strong]:text-slate-900">
                                                {!! $valStr !!}
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 {{ $fullWidth ? 'md:col-span-2' : '' }}">
                                            <span class="block text-[10px] font-black uppercase text-indigo-600 tracking-wider mb-1">{{ str_replace(['_', '-'], ' ', $key) }}</span>
                                            <span class="text-xs font-bold text-slate-800 leading-snug block mt-0.5">{{ $valStr }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 text-center text-xs text-gray-400 font-bold uppercase tracking-wider">
                                Nessuna caratteristica tecnica aggiuntiva memorizzata.
                            </div>
                        @endif
                    </div>
                </div>
        </div>
    </div>
</x-app-layout>
