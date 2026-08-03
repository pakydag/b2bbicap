<x-agent-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('agent.catalog') }}" class="bg-white border border-gray-200 text-gray-500 p-2 rounded-xl hover:bg-gray-50 transition">
                <span class="text-xl leading-none">←</span>
            </a>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
                {{ $product->name }}
            </h2>
        </div>
    </x-slot>

    @php
        $c = $product->characteristics ?? [];
        $gallery = [];
        
        // Foto principale del DB
        if (!empty($product->image)) {
            $gallery[] = \Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://']) 
                ? $product->image 
                : asset('storage/' . $product->image);
        }
        
        // Foto principale Web
        if (!empty($c['FOTO-PRINCIPALE-PRODOTTO-WEB'])) {
            $webMain = $c['FOTO-PRINCIPALE-PRODOTTO-WEB'];
            if (!in_array($webMain, $gallery)) {
                $gallery[] = $webMain;
            }
        }
        
        // Foto suola
        if (!empty($c['FOTO SUOLA'])) {
            $suolaPhoto = $c['FOTO SUOLA'];
            if (!in_array($suolaPhoto, $gallery)) {
                $gallery[] = $suolaPhoto;
            }
        }
        
        // Foto aggiuntive 1-10
        for ($i = 1; $i <= 10; $i++) {
            $key = "FOTO-AGGIUNTIVA-{$i}";
            if (!empty($c[$key])) {
                $additional = $c[$key];
                if (!in_array($additional, $gallery)) {
                    $gallery[] = $additional;
                }
            }
        }
    @endphp

    <style>
        @media (min-width: 1024px) {
            .product-left-col {
                width: 350px !important;
                flex: 0 0 350px !important;
            }
        }
    </style>

    <div class="flex flex-col lg:flex-row gap-12" x-data="{ showDetails: true }">
        <!-- Image Gallery & Info -->
        <div x-show="showDetails" x-transition.duration.300ms class="w-full product-left-col space-y-6" x-data="{ currentImg: 0, showLightbox: false, images: {{ json_encode($gallery) }} }">
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-12 aspect-square flex items-center justify-center relative overflow-hidden group">
                <span class="absolute top-8 left-8 bg-indigo-900 text-white px-4 py-1 rounded-full text-xs font-black uppercase tracking-widest shadow-xl z-10">
                    {{ $product->brand->name }}
                </span>
                
                <template x-if="images.length > 0">
                    <img :src="images[currentImg]" 
                         @click="showLightbox = true" 
                         x-on:error="images = images.filter(i => i !== images[currentImg]); if (currentImg >= images.length) currentImg = 0;"
                         alt="{{ $product->name }}" 
                         class="max-w-full max-h-full object-contain group-hover:scale-105 transition duration-700 cursor-zoom-in">
                </template>
                <template x-if="images.length === 0">
                    <div class="text-9xl opacity-10">👕</div>
                </template>

                <!-- Navigation arrows -->
                <template x-if="images.length > 1">
                    <button type="button" @click="currentImg = (currentImg === 0) ? images.length - 1 : currentImg - 1" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 border border-gray-100 w-10 h-10 rounded-full shadow-md flex items-center justify-center transition duration-200 z-10 text-xl font-bold hover:scale-110">
                        ‹
                    </button>
                </template>
                <template x-if="images.length > 1">
                    <button type="button" @click="currentImg = (currentImg === images.length - 1) ? 0 : currentImg + 1" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 border border-gray-100 w-10 h-10 rounded-full shadow-md flex items-center justify-center transition duration-200 z-10 text-xl font-bold hover:scale-110">
                        ›
                    </button>
                </template>
            </div>

            <!-- Thumbnails -->
            <template x-if="images.length > 1">
                <div class="flex flex-wrap gap-2 justify-center">
                    <template x-for="(img, idx) in images" :key="idx">
                        <button type="button" @click="currentImg = idx" :class="currentImg === idx ? 'border-2 border-indigo-600 ring-2 ring-indigo-100 opacity-100 scale-105 shadow-sm' : 'border border-gray-200 opacity-70 hover:opacity-100'" class="w-14 h-14 bg-white rounded-xl overflow-hidden p-1 transition duration-200 flex items-center justify-center">
                            <img :src="img" x-on:error="images = images.filter(i => i !== img); if (currentImg >= images.length) currentImg = 0;" class="w-full h-full object-contain">
                        </button>
                    </template>
                </div>
            </template>

            <!-- Lightbox Modal -->
            <div x-show="showLightbox" 
                 x-transition 
                 class="fixed inset-0 flex flex-col items-center justify-center p-4"
                 @keydown.escape.window="showLightbox = false"
                 style="display: none; background-color: rgba(0, 0, 0, 0.98); z-index: 99999; backdrop-filter: blur(8px);">
                 
                 <!-- Bottone Chiudi (X grande, sfondo bianco, testo scuro ad alta leggibilità) -->
                 <button type="button" @click="showLightbox = false" class="absolute top-6 right-6 bg-gray-900 text-white hover:bg-yellow-500 hover:text-black hover:scale-110 w-12 h-12 rounded-full flex items-center justify-center shadow-2xl transition duration-200 text-xl font-bold z-[100000]">
                     ✕
                 </button>

                 <!-- Immagine Ingrandita -->
                 <div class="relative max-w-4xl max-h-[80vh] w-full flex items-center justify-center p-4">
                     <img :src="images[currentImg]" class="max-w-full max-h-[75vh] object-contain rounded-2xl shadow-2xl">
                     
                     <!-- Freccia Sinistra (Lightbox) -->
                     <template x-if="images.length > 1">
                         <button type="button" @click.stop="currentImg = (currentImg === 0) ? images.length - 1 : currentImg - 1" class="absolute left-2 bg-white text-gray-900 hover:bg-yellow-500 hover:text-black hover:scale-110 w-12 h-12 rounded-full flex items-center justify-center transition duration-200 text-2xl font-black shadow-2xl z-[100000]">
                             ‹
                         </button>
                     </template>
                     <!-- Freccia Destra (Lightbox) -->
                     <template x-if="images.length > 1">
                         <button type="button" @click.stop="currentImg = (currentImg === images.length - 1) ? 0 : currentImg + 1" class="absolute right-2 bg-white text-gray-900 hover:bg-yellow-500 hover:text-black hover:scale-110 w-12 h-12 rounded-full flex items-center justify-center transition duration-200 text-2xl font-black shadow-2xl z-[100000]">
                             ›
                         </button>
                     </template>
                 </div>

                 <!-- Titolo e Contatore in Basso -->
                 <div class="text-gray-900 text-xs font-black uppercase tracking-widest mt-4 bg-gray-100 px-6 py-2.5 rounded-full border border-gray-200 shadow-lg">
                     {{ $product->name }} - <span x-text="(currentImg + 1) + ' / ' + images.length"></span>
                 </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <h3 class="font-black text-sm uppercase tracking-widest text-gray-500 mb-4 border-b pb-2">Dettagli Articolo</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-xs mb-1">Codice</p>
                        <p class="font-black text-slate-900">{{ $product->code ?? 'N/D' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-xs mb-1">Linea</p>
                        <p class="font-black text-slate-900">{{ $product->brand->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-xs mb-1">Stagione</p>
                        <p class="font-black text-slate-900">{{ $product->season ?? 'N/D' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-xs mb-1">Stato Giacenze</p>
                        @if($giacenzaMatch)
                            <p class="font-black text-emerald-600 uppercase text-xs">Sincronizzato</p>
                        @else
                            <p class="font-black text-rose-500 uppercase text-xs">Non Sincronizzato</p>
                        @endif
                    </div>
                </div>
                <div class="mt-6">
                    <p class="text-gray-500 font-bold uppercase text-xs mb-1">Descrizione</p>
                    <p class="text-sm text-gray-700 font-medium leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100">{{ $product->description ?? 'Nessuna descrizione disponibile per questo articolo.' }}</p>
                </div>
            </div>

            @php
                $locale = app()->getLocale();
                $isIt = ($locale === 'it');
                $c = $product->characteristics ?? [];
                
                $specs = [];
                if (!empty($c)) {
                    if (!empty($c['CAT-SICUREZZA'])) $specs[$isIt ? 'Categoria di Sicurezza' : 'Safety Category'] = $c['CAT-SICUREZZA'];
                    if (!empty($c['RANGE-TAGLIE-VALORE'])) $specs[$isIt ? 'Range Taglie' : 'Size Range'] = $c['RANGE-TAGLIE-VALORE'];
                    if (!empty($c['NORMA'])) $specs[$isIt ? 'Normativa' : 'Standard'] = $c['NORMA'];
                    if (!empty($c['CALZATA'])) $specs[$isIt ? 'Calzata' : 'Fitting'] = $c['CALZATA'];
                    if (!empty($c['SOLETTO'])) $specs[$isIt ? 'Soletto' : 'Insole'] = $c['SOLETTO'];
                    if (!empty($c['MODELLO'])) $specs[$isIt ? 'Modello' : 'Model'] = $c['MODELLO'];
                    
                    $settoreKey = $isIt ? 'SETTORE-DI-UTILIZZO-IT' : 'SETTORE-DI-UTILIZZO-EN';
                    if (!empty($c[$settoreKey])) $specs[$isIt ? 'Settori di Utilizzo' : 'Work Environments'] = $c[$settoreKey];
                    
                    // Tomaia: usa tomaia-descrizione-it/en con fallback su PUNTALE (visto che nel foglio contiene tomaia, es. PELLE SCAMOSCIATA)
                    $tomaiaKey = $isIt ? 'tomaia-descrizione-it' : 'tomaia-descrizione-en';
                    $tomaiaVal = !empty($c[$tomaiaKey]) ? $c[$tomaiaKey] : ($c['PUNTALE'] ?? '');
                    if (!empty($tomaiaVal)) $specs[$isIt ? 'Tomaia' : 'Upper'] = $tomaiaVal;
                    
                    $foderaKey = $isIt ? 'fodera-descrizione-it' : 'fodera-descrizione-en';
                    if (!empty($c[$foderaKey])) $specs[$isIt ? 'Fodera' : 'Lining'] = $c[$foderaKey];
                    
                    // Puntale: mappato su puntale-descrizione-it/en per avere la vera descrizione metallo/composito
                    $puntaleDescKey = $isIt ? 'puntale-descrizione-it' : 'puntale-descrizione-en';
                    if (!empty($c[$puntaleDescKey])) {
                        $specs[$isIt ? 'Puntale' : 'Toe Cap'] = $c[$puntaleDescKey];
                    }
                    
                    $laminaKey = $isIt ? 'lamina-descrizione-it' : 'lamina-descrizione-en';
                    if (!empty($c[$laminaKey])) $specs[$isIt ? 'Lamina Antiperforazione' : 'Anti-Perforation Insert'] = $c[$laminaKey];
                    
                    $suolaKey = $isIt ? 'suola-descrizione-it' : 'suola-descrizione-en';
                    if (!empty($c[$suolaKey])) $specs[$isIt ? 'Suola' : 'Outsole'] = $c[$suolaKey];
                }
            @endphp

            @if(!empty($specs))
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mt-6">
                <h3 class="font-black text-sm uppercase tracking-widest text-gray-500 mb-4 border-b pb-2">
                    {{ $isIt ? 'Specifiche Tecniche' : 'Technical Specifications' }}
                </h3>
                <div class="space-y-4">
                    @foreach($specs as $label => $value)
                        <div class="border-b border-gray-50 pb-2">
                            <span class="text-xs font-bold text-gray-400 uppercase block mb-0.5">{{ $label }}</span>
                            <span class="text-sm font-semibold text-indigo-950 leading-relaxed">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(!empty($c[$isIt ? 'URL-TDS IT' : 'URL-TDS-EN']) || !empty($c[$isIt ? 'URL-CATALOGO-IT' : 'URL-CATALOGO-EN']) || !empty($c[$isIt ? 'URL-DICHIARAZIONE-CONFORMITA-IT' : 'URL-DICHIARAZIONE-CONFORMITA-EN']) || !empty($c['URL-NOTA-INFORMATIVA']))
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mt-6">
                <h3 class="font-black text-sm uppercase tracking-widest text-gray-500 mb-4 border-b pb-2">
                    {{ $isIt ? 'Documentazione Tecnica' : 'Technical Documents' }}
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if(!empty($c[$isIt ? 'URL-TDS IT' : 'URL-TDS-EN']))
                        <a href="{{ $c[$isIt ? 'URL-TDS IT' : 'URL-TDS-EN'] }}" target="_blank" class="inline-flex items-center text-xs font-black uppercase text-indigo-600 hover:text-indigo-800 transition">
                            📄 Scheda Tecnica (TDS)
                        </a>
                    @endif
                    @if(!empty($c[$isIt ? 'URL-CATALOGO-IT' : 'URL-CATALOGO-EN']))
                        <a href="{{ $c[$isIt ? 'URL-CATALOGO-IT' : 'URL-CATALOGO-EN'] }}" target="_blank" class="inline-flex items-center text-xs font-black uppercase text-indigo-600 hover:text-indigo-800 transition">
                            📖 Catalogo Brand
                        </a>
                    @endif
                    @if(!empty($c[$isIt ? 'URL-DICHIARAZIONE-CONFORMITA-IT' : 'URL-DICHIARAZIONE-CONFORMITA-EN']))
                        <a href="{{ $c[$isIt ? 'URL-DICHIARAZIONE-CONFORMITA-IT' : 'URL-DICHIARAZIONE-CONFORMITA-EN'] }}" target="_blank" class="inline-flex items-center text-xs font-black uppercase text-indigo-600 hover:text-indigo-800 transition">
                            🛡️ Dichiarazione Conformità UE
                        </a>
                    @endif
                    @if(!empty($c['URL-NOTA-INFORMATIVA']))
                        <a href="{{ $c['URL-NOTA-INFORMATIVA'] }}" target="_blank" class="inline-flex items-center text-xs font-black uppercase text-indigo-600 hover:text-indigo-800 transition">
                            ℹ️ Nota Informativa
                        </a>
                    @endif
                </div>
            </div>
            @endif

            @if(!empty($c[$isIt ? 'DESCRIZIONE ESTESA HTML IT' : 'DESCRIZIONE ESTESA HTML EN']))
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mt-6 prose prose-indigo max-w-none">
                <h3 class="font-black text-sm uppercase tracking-widest text-gray-500 mb-4 border-b pb-2">
                    {{ $isIt ? 'Presentazione Prodotto' : 'Product Features' }}
                </h3>
                <div class="text-sm text-gray-700 leading-relaxed">
                    {!! $c[$isIt ? 'DESCRIZIONE ESTESA HTML IT' : 'DESCRIZIONE ESTESA HTML EN'] !!}
                </div>
            </div>
            @endif
        </div>

        <!-- Variant Matrix & Form -->
        <div class="flex-1 min-w-0 bg-white rounded-[40px] shadow-sm border border-gray-100 p-8 md:p-12 overflow-hidden transition-all duration-300">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div>
                    <h3 class="font-black text-xl text-gray-900 uppercase tracking-tight">{{ $product->name }}</h3>
                    @if(isset($priceDetails) && $priceDetails['unit_price'] < $product->price)
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-400 line-through font-bold">
                                € {{ number_format($product->price, 2, ',', '.') }}
                            </span>
                            <span class="text-2xl font-black text-indigo-700">
                                € {{ number_format($priceDetails['unit_price'], 2, ',', '.') }}
                            </span>
                            <span class="text-[10px] font-black bg-amber-50 text-amber-800 border border-amber-200 px-2 py-0.5 rounded-full">
                                RISERVATO AZIENDA (-{{ number_format($priceDetails['discount_value'], 1) }}%)
                            </span>
                        </div>
                    @else
                        <div class="mt-1">
                            <span class="text-2xl font-black text-slate-900">
                                € {{ number_format($product->price, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
                
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('agent.product', $product->id) }}" class="bg-gray-900 border border-black text-white hover:bg-black hover:text-yellow-400 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-2 transition shadow-sm">
                        <span>Torna al Prodotto Reale</span>
                        <span class="text-sm">🔙</span>
                    </a>
                    <button type="button" @click="showDetails = !showDetails" class="bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100 hover:text-black hover:border-yellow-400 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-2 transition shadow-sm">
                        <span x-text="showDetails ? '⬅️ Espandi Griglia (Schermo Intero)' : '➡️ Mostra Foto Prodotto'"></span>
                    </button>
                </div>
            </div>
            
            @if(!$giacenzaMatch)
                <!-- Prodotto Non Sincronizzato -->
                <div class="bg-rose-50 border border-rose-100 rounded-3xl p-8 text-center space-y-4">
                    <span class="text-5xl block">⚠️</span>
                    <h4 class="text-rose-800 font-black uppercase text-lg tracking-wider">NON SINCRONIZZATO</h4>
                    <p class="text-sm text-rose-700 leading-relaxed max-w-md mx-auto">
                        Questo articolo non è attualmente allineato con il sistema di gestione del magazzino. La prenotazione e l'ordine delle taglie non sono disponibili per questo prodotto.
                    </p>
                </div>
            @else
                @php
                    $currentSizes = array_keys($giacenzaMatch['current_stock'] ?? []);
                    $futureSizes = [];
                    foreach ($giacenzaMatch['future_stock'] ?? [] as $date => $stocks) {
                        $futureSizes = array_merge($futureSizes, array_keys($stocks));
                    }
                    $allCsvSizes = array_unique(array_merge($currentSizes, $futureSizes));
                    $sizes = collect($allCsvSizes)
                        ->filter(function($s) {
                            return is_numeric($s) && intval($s) >= 30 && intval($s) <= 50;
                        })
                        ->sort()
                        ->values()
                        ->all();
                    
                    $groupedVariants = $product->variants->groupBy(function($v) { return $v->color ?? 'UNICO'; });
                    $inputIndex = 0;
                @endphp

                <div class="variant-form-container">
                    
                    <!-- 1. Griglia Disponibilità Immediata -->
                    <div class="mb-10">
                        <h4 class="font-black text-xs uppercase tracking-widest text-indigo-600 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping shrink-0"></span>
                            Disponibilità Immediata (Pronta Consegna)
                        </h4>
                        
                        <div class="overflow-x-auto pb-4">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                        <th class="py-4 px-2 whitespace-nowrap">Variante / Colore</th>
                                        @foreach($sizes as $size)
                                            <th class="py-4 px-2 text-center whitespace-nowrap">{{ $size }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($groupedVariants as $color => $variants)
                                        <tr class="group hover:bg-indigo-50/30 transition">
                                            <td class="py-6 px-2">
                                                <div class="flex items-center">
                                                    <div class="w-2 h-2 rounded-full mr-3 bg-indigo-400"></div>
                                                    <span class="text-xs font-black text-gray-900 uppercase">{{ $color }}</span>
                                                </div>
                                            </td>
                                            @foreach($sizes as $size)
                                                <td class="py-6 px-1 text-center">
                                                    @php 
                                                        $variant = $variants->where('size', $size)->first(); 
                                                        $qtyAvailable = $giacenzaMatch['current_stock'][$size] ?? 0;
                                                    @endphp
                                                    @if($variant)
                                                        <div class="relative inline-block">
                                                            <input type="hidden" name="items[{{ $inputIndex }}][variant_id]" value="{{ $variant->id }}">
                                                            <input type="hidden" name="items[{{ $inputIndex }}][delivery_date]" value="">
                                                            <input type="number" 
                                                                   name="items[{{ $inputIndex }}][quantity]" 
                                                                   min="0" 
                                                                   max="{{ $qtyAvailable > 0 ? $qtyAvailable : 0 }}" 
                                                                   value="0" 
                                                                   placeholder="0"
                                                                   {{ $qtyAvailable <= 0 ? 'disabled' : '' }}
                                                                   class="qty-input w-16 text-center text-xs font-black rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition p-2 {{ $qtyAvailable <= 0 ? 'bg-red-50 border-red-200 text-red-400' : 'bg-gray-50 border-gray-100 hover:bg-white text-gray-900' }} disabled:cursor-not-allowed">
                                                            @if($qtyAvailable > 5)
                                                                <span class="absolute -top-3 left-1/2 -translate-x-1/2 text-[8px] font-black text-emerald-600 whitespace-nowrap bg-white px-1 shadow-sm rounded border border-emerald-50 mb-1">
                                                                    {{ $qtyAvailable }}
                                                                </span>
                                                            @elseif($qtyAvailable <= 5 && $qtyAvailable > 0)
                                                                <span class="absolute -top-3 left-1/2 -translate-x-1/2 text-[8px] font-black text-orange-500 whitespace-nowrap bg-white px-1 shadow-sm rounded border border-orange-50 mb-1">
                                                                    SOLO {{ $qtyAvailable }}
                                                                </span>
                                                            @elseif($qtyAvailable <= 0)
                                                                <span class="absolute -top-3 left-1/2 -translate-x-1/2 text-[8px] font-black text-rose-400 whitespace-nowrap bg-white px-1 shadow-sm rounded border border-rose-50 mb-1">
                                                                    ESAU.
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @php $inputIndex++; @endphp
                                                    @else
                                                        <div class="w-16 mx-auto h-8 bg-gray-50/50 rounded-lg flex items-center justify-center opacity-30">
                                                            <div class="w-1 h-3 bg-gray-200 rotate-45 transform"></div>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 2. Griglia Disponibilità Future Aggregate (Prenotazione) -->
                    @if(!empty($giacenzaMatch['future_stock']))
                        @php
                            // Calcolo date disponibili
                            $futureDates = array_keys($giacenzaMatch['future_stock']);
                            $datesListText = implode(', ', $futureDates);
                            
                            // Aggregazione giacenze future
                            $summedFutureStock = [];
                            foreach ($giacenzaMatch['future_stock'] as $date => $stocks) {
                                foreach ($stocks as $s => $q) {
                                    if (!isset($summedFutureStock[$s])) {
                                        $summedFutureStock[$s] = 0;
                                    }
                                    $summedFutureStock[$s] += $q;
                                }
                            }
                        @endphp
                        
                        <div class="mt-12 space-y-12">
                            <div class="bg-indigo-50/30 rounded-3xl p-6 border border-indigo-100/50">
                                <h4 class="font-black text-xs uppercase tracking-widest text-indigo-800 mb-2 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse shrink-0"></span>
                                    Prenotazione Disponibilità Future Associate
                                </h4>
                                <p class="text-[10px] font-bold text-indigo-600/70 uppercase tracking-widest mb-4 ml-4">
                                    Date previste di arrivo: {{ $datesListText }}
                                </p>
                                
                                <div class="overflow-x-auto pb-4">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                                <th class="py-4 px-2 whitespace-nowrap">Variante / Colore</th>
                                                @foreach($sizes as $size)
                                                    <th class="py-4 px-2 text-center whitespace-nowrap">{{ $size }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @foreach($groupedVariants as $color => $variants)
                                                <tr class="group hover:bg-indigo-50/30 transition">
                                                    <td class="py-6 px-2">
                                                        <div class="flex items-center">
                                                            <div class="w-2 h-2 rounded-full mr-3 bg-indigo-500"></div>
                                                            <span class="text-xs font-black text-gray-900 uppercase">{{ $color }}</span>
                                                        </div>
                                                    </td>
                                                    @foreach($sizes as $size)
                                                        <td class="py-6 px-1 text-center">
                                                            @php 
                                                                $variant = $variants->where('size', $size)->first(); 
                                                                $qtyFuture = $summedFutureStock[$size] ?? 0;
                                                            @endphp
                                                            @if($variant)
                                                                <div class="relative inline-block">
                                                                    <input type="hidden" name="items[{{ $inputIndex }}][variant_id]" value="{{ $variant->id }}">
                                                                    <input type="hidden" name="items[{{ $inputIndex }}][delivery_date]" value="Aggregato">
                                                                    <input type="number" 
                                                                           name="items[{{ $inputIndex }}][quantity]" 
                                                                           min="0" 
                                                                           max="{{ $qtyFuture > 0 ? $qtyFuture : 0 }}" 
                                                                           value="0" 
                                                                           placeholder="0"
                                                                           {{ $qtyFuture <= 0 ? 'disabled' : '' }}
                                                                           class="qty-input w-16 text-center text-xs font-black rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition p-2 {{ $qtyFuture <= 0 ? 'bg-red-50 border-red-200 text-red-400' : 'bg-white border-indigo-100 text-gray-900' }} disabled:cursor-not-allowed">
                                                                    
                                                                    @if($qtyFuture > 0)
                                                                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 text-[8px] font-black text-indigo-600 whitespace-nowrap bg-white px-1 shadow-sm rounded border border-indigo-50 mb-1">
                                                                            +{{ $qtyFuture }}
                                                                        </span>
                                                                    @else
                                                                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 text-[8px] font-black text-gray-400 whitespace-nowrap bg-white px-1 shadow-sm rounded border border-gray-100 mb-1">
                                                                            ESAU.
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                @php $inputIndex++; @endphp
                                                            @else
                                                                <div class="w-16 mx-auto h-8 bg-gray-50/50 rounded-lg flex items-center justify-center opacity-30">
                                                                    <div class="w-1 h-3 bg-gray-200 rotate-45 transform"></div>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Sezione Riepilogo -->
                    <div class="mt-12 bg-gray-50 rounded-[32px] p-8 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Riepilogo Selezione</span>
                            <div class="flex items-baseline gap-2">
                                <span id="total-qty-display" class="text-4xl font-black text-slate-900 leading-none">0</span>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1 text-center md:text-right w-full block">La variante è in modalità sola lettura (non aggiunge al carrello)</p>
                        </div>
                        
                        <a href="{{ route('agent.product', $product->id) }}" class="w-full md:w-auto px-12 bg-black border border-zinc-950 text-white py-5 rounded-2xl text-xs font-black uppercase tracking-widest hover:border-yellow-400 hover:text-yellow-400 shadow-xl shadow-black/10 transition duration-300 flex items-center justify-center gap-3">
                            <span class="text-xl">🔙</span>
                            Torna al Prodotto Standard
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.qty-input');
            const display = document.getElementById('total-qty-display');
            
            const updateTotal = () => {
                let total = 0;
                inputs.forEach(input => {
                    total += parseInt(input.value) || 0;
                });
                display.innerText = total;
            };

            inputs.forEach(input => {
                input.addEventListener('input', updateTotal);
                input.addEventListener('change', updateTotal);
            });
        });
    </script>
    @endpush
</x-agent-layout>
