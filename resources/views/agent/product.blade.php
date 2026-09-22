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
        @media (min-width: 1024px) and (max-width: 1279px) {
            .product-left-col {
                width: 270px !important;
                flex: 0 0 270px !important;
            }
        }
        @media (min-width: 1280px) {
            .product-left-col {
                width: 340px !important;
                flex: 0 0 340px !important;
            }
        }
    </style>

    <div class="flex flex-col lg:flex-row gap-6 xl:gap-10 min-w-0" x-data="{ showDetails: true }">
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
                 
                 <!-- Bottone Chiudi -->
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
                    
                    $tomaiaKey = $isIt ? 'tomaia-descrizione-it' : 'tomaia-descrizione-en';
                    $tomaiaVal = !empty($c[$tomaiaKey]) ? $c[$tomaiaKey] : ($c['PUNTALE'] ?? '');
                    if (!empty($tomaiaVal)) $specs[$isIt ? 'Tomaia' : 'Upper'] = $tomaiaVal;
                    
                    $foderaKey = $isIt ? 'fodera-descrizione-it' : 'fodera-descrizione-en';
                    if (!empty($c[$foderaKey])) $specs[$isIt ? 'Fodera' : 'Lining'] = $c[$foderaKey];
                    
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
        <div class="flex-1 min-w-0 bg-white rounded-[40px] shadow-sm border border-gray-100 p-5 sm:p-8 xl:p-12 overflow-hidden transition-all duration-300">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h3 class="font-black text-xl text-gray-900 uppercase tracking-tight">{{ $product->name }}</h3>
                    @if(isset($priceDetails) && ($priceDetails['unit_price'] < $product->price || !empty($priceDetails['tier'])))
                        <div class="flex items-center gap-2 mt-1">
                            @if($priceDetails['unit_price'] < $product->price)
                                <span class="text-xs text-gray-400 line-through font-bold">
                                    € {{ number_format($product->price, 2, ',', '.') }}
                                </span>
                            @endif
                            <span class="text-2xl font-black text-indigo-700">
                                € {{ number_format($priceDetails['unit_price'], 2, ',', '.') }}
                            </span>
                            @if(!empty($priceDetails['is_product_exception']))
                                @if(($priceDetails['discount_type'] ?? '') === 'fixed_price')
                                    @php
                                        $extraDiscounts = [];
                                        if (!empty($priceDetails['discount_2']) && $priceDetails['discount_2'] > 0) {
                                            $extraDiscounts[] = '-' . floatval($priceDetails['discount_2']) . '%';
                                        }
                                        if (!empty($priceDetails['discount_3']) && $priceDetails['discount_3'] > 0) {
                                            $extraDiscounts[] = '-' . floatval($priceDetails['discount_3']) . '%';
                                        }
                                    @endphp
                                    <span class="text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300 px-2.5 py-1 rounded-full shadow-2xs">
                                        ⭐ LISTINO PERSONALIZZATO: PREZZO NETTO € {{ number_format($priceDetails['discount_value'], 2, ',', '.') }}{{ !empty($extraDiscounts) ? ' (' . implode(' ', $extraDiscounts) . ')' : '' }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300 px-2.5 py-1 rounded-full shadow-2xs">
                                        ⭐ LISTINO PERSONALIZZATO ({{ $priceDetails['rule_summary'] ?? 'Prezzo Dedicato' }})
                                    </span>
                                @endif
                            @elseif(($priceDetails['discount_type'] ?? '') === 'fixed_price')
                                @php
                                    $calcDiscountPct = (1 - ($priceDetails['unit_price'] / max(0.01, (float)$product->price))) * 100;
                                    $extraDiscounts = [];
                                    if (!empty($priceDetails['discount_2']) && $priceDetails['discount_2'] > 0) {
                                        $extraDiscounts[] = '-' . floatval($priceDetails['discount_2']) . '%';
                                    }
                                    if (!empty($priceDetails['discount_3']) && $priceDetails['discount_3'] > 0) {
                                        $extraDiscounts[] = '-' . floatval($priceDetails['discount_3']) . '%';
                                    }
                                @endphp
                                @if(!empty($extraDiscounts))
                                    <span class="text-[10px] font-black bg-amber-50 text-amber-800 border border-amber-200 px-2 py-0.5 rounded-full">
                                        PREZZO NETTO RISERVATO (€ {{ number_format($priceDetails['discount_value'], 2, ',', '.') }} {{ implode(' ', $extraDiscounts) }})
                                    </span>
                                @elseif($calcDiscountPct > 0)
                                    <span class="text-[10px] font-black bg-amber-50 text-amber-800 border border-amber-200 px-2 py-0.5 rounded-full">
                                        PREZZO NETTO RISERVATO (-{{ number_format($calcDiscountPct, 1) }}%)
                                    </span>
                                @else
                                    <span class="text-[10px] font-black bg-amber-50 text-amber-800 border border-amber-200 px-2 py-0.5 rounded-full">
                                        PREZZO NETTO RISERVATO
                                    </span>
                                @endif
                            @else
                                @php
                                    $pctParts = ['-' . floatval($priceDetails['discount_value']) . '%'];
                                    if (!empty($priceDetails['discount_2']) && $priceDetails['discount_2'] > 0) {
                                        $pctParts[] = '+' . floatval($priceDetails['discount_2']) . '%';
                                    }
                                    if (!empty($priceDetails['discount_3']) && $priceDetails['discount_3'] > 0) {
                                        $pctParts[] = '+' . floatval($priceDetails['discount_3']) . '%';
                                    }
                                @endphp
                                <span class="text-[10px] font-black bg-indigo-50 text-indigo-800 border border-indigo-200 px-2 py-0.5 rounded-full">
                                    📉 SCONTO QUANTITÀ AZIENDA ({{ implode(' ', $pctParts) }})
                                </span>
                            @endif
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
                    <button type="button" @click="showDetails = !showDetails" class="bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100 hover:text-black hover:border-yellow-400 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-2 transition shadow-sm">
                        <span x-text="showDetails ? '⬅️ Espandi Griglia (Schermo Intero)' : '➡️ Mostra Foto Prodotto'"></span>
                    </button>
                </div>
            </div>

            @php
                $currentCustomer = $customer ?? (auth()->user()->role === 'customer' ? auth()->user()->b2bCustomer : null);
                if (!isset($assignedPriceList) && $currentCustomer && $currentCustomer->b2b_price_list_id) {
                    $assignedPriceList = \App\Models\B2bPriceList::find($currentCustomer->b2b_price_list_id);
                }
                if ((!isset($specificTiers) || $specificTiers->isEmpty()) && $assignedPriceList) {
                    $specificTiers = \App\Models\B2bPriceListItem::where('b2b_price_list_id', $assignedPriceList->id)
                        ->where('b2b_product_id', $product->id)
                        ->orderBy('min_quantity', 'asc')
                        ->get();
                }
                if ((!isset($generalTiers) || $generalTiers->isEmpty()) && $assignedPriceList) {
                    $generalTiers = \App\Models\B2bPriceListItem::where('b2b_price_list_id', $assignedPriceList->id)
                        ->whereNull('b2b_product_id')
                        ->orderBy('min_quantity', 'asc')
                        ->get();
                }
            @endphp

            <!-- Card Dettaglio Listino Prezzi & Fasce per questo Articolo -->
            @if($assignedPriceList || ($specificTiers && $specificTiers->count() > 0) || ($generalTiers && $generalTiers->count() > 0))
                <div class="mb-6 bg-gradient-to-br from-indigo-50/80 via-white to-amber-50/50 border border-indigo-100 rounded-3xl p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-indigo-100/70 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="text-2xl">📋</span>
                            <div>
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest block leading-none">Condizioni Listino Riservate</span>
                                <h4 class="font-black text-indigo-950 uppercase tracking-tight text-sm mt-0.5">
                                    {{ $assignedPriceList ? $assignedPriceList->name : 'Listino Prezzi Standard' }}
                                    @if($currentCustomer)
                                        <span class="text-xs font-bold text-gray-400 lowercase">per</span> <span class="text-xs font-black text-slate-900 uppercase">{{ $currentCustomer->business_name }}</span>
                                    @endif
                                </h4>
                            </div>
                        </div>
                        @if($specificTiers && $specificTiers->count() > 0)
                            <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-2xs self-start sm:self-auto">
                                ⭐ Listino Personalizzato Articolo
                            </span>
                        @elseif($generalTiers && $generalTiers->count() > 0)
                            <span class="inline-flex items-center gap-1.5 bg-indigo-100 text-indigo-900 border border-indigo-200 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-2xs self-start sm:self-auto">
                                📉 Sconto Quantità da Listino
                            </span>
                        @endif
                    </div>

                    @if($specificTiers && $specificTiers->count() > 0)
                        <!-- Eccezione specifica per questo prodotto -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-black text-amber-950 uppercase tracking-tight flex items-center gap-1.5">
                                    <span>🎯</span> Regola Personalizzata per {{ $product->name }}
                                </p>
                                <span class="text-[10px] text-gray-400 font-bold uppercase">Prezzo Base di Listino: € {{ number_format($product->price, 2, ',', '.') }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach($specificTiers as $sTier)
                                    @php
                                        $tierPrice = $sTier->calculateUnitPrice($product->price);
                                    @endphp
                                    <div class="bg-white rounded-2xl border border-amber-200 p-3 shadow-2xs flex flex-col justify-between">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-[10px] font-black text-gray-500 uppercase">
                                                @if($sTier->min_quantity > 1 || !empty($sTier->max_quantity))
                                                    Da {{ $sTier->min_quantity }} {{ $sTier->max_quantity ? 'a ' . $sTier->max_quantity : 'in poi' }} pz
                                                @else
                                                    Tutte le Quantità (1+ pz)
                                                @endif
                                            </span>
                                            <span class="text-[9px] font-black text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">
                                                {{ $sTier->rule_summary }}
                                            </span>
                                        </div>
                                        <div class="flex items-baseline justify-between mt-1 pt-1 border-t border-gray-50">
                                            <span class="text-xs text-gray-500 font-bold">Prezzo Riservato:</span>
                                            <span class="text-base font-black text-indigo-700">€ {{ number_format($tierPrice, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[10px] text-amber-800 font-semibold italic mt-1">
                                Questo articolo gode di un prezzo/sconto esclusivo configurato specificamente nel listino aziendale.
                            </p>
                        </div>
                    @elseif($generalTiers && $generalTiers->count() > 0)
                        <!-- Fasce generali applicabili a questo prodotto -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-black text-indigo-950 uppercase tracking-tight flex items-center gap-1.5">
                                    <span>📊</span> Fasce di Sconto Quantità Applicabili a questo Articolo
                                </p>
                                <span class="text-[10px] text-gray-400 font-bold uppercase">Prezzo Base di Listino: € {{ number_format($product->price, 2, ',', '.') }}</span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach($generalTiers as $gTier)
                                    @php
                                        $gTierPrice = $gTier->calculateUnitPrice($product->price);
                                    @endphp
                                    <div class="bg-white rounded-2xl border border-indigo-100 p-3 shadow-2xs flex flex-col justify-between">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-[10px] font-black text-gray-500 uppercase">
                                                {{ $gTier->min_quantity }}{{ $gTier->max_quantity ? '-' . $gTier->max_quantity : '+' }} pz
                                            </span>
                                            <span class="text-[9px] font-black text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100">
                                                @if($gTier->discount_type === 'percentage')
                                                    -{{ floatval($gTier->discount_value) }}%{{ $gTier->discount_2 > 0 ? ' +' . floatval($gTier->discount_2) . '%' : '' }}
                                                @elseif($gTier->discount_type === 'fixed_price')
                                                    € {{ number_format($gTier->discount_value, 2, ',', '.') }}
                                                @else
                                                    -€ {{ number_format($gTier->discount_value, 2, ',', '.') }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex items-baseline justify-between mt-1 pt-1 border-t border-gray-50">
                                            <span class="text-[10px] text-gray-400 font-bold">Prezzo:</span>
                                            <span class="text-sm font-black text-indigo-700">€ {{ number_format($gTierPrice, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[10px] text-indigo-500 font-semibold italic mt-1">
                                Lo sconto per quantità viene calcolato sul totale complessivo dei pezzi ordinati all'interno dello stesso gruppo di consegna nel carrello.
                            </p>
                        </div>
                    @endif
                </div>
            @endif
            
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

                <form action="{{ route('agent.cart.add') }}" method="POST" class="variant-form-container">
                    @csrf

                    @php
                        // Aggregazione giacenze future per la griglia immediata
                        $summedFutureStock = [];
                        if(!empty($giacenzaMatch['future_stock'])) {
                            foreach ($giacenzaMatch['future_stock'] as $date => $stocks) {
                                foreach ($stocks as $s => $q) {
                                    if (!isset($summedFutureStock[$s])) {
                                        $summedFutureStock[$s] = 0;
                                    }
                                    $summedFutureStock[$s] += $q;
                                }
                            }
                        }
                    @endphp
                    
                    <!-- 1. Griglia Disponibilità Immediata -->
                    <div class="mb-12">
                        <h4 class="font-black text-sm uppercase tracking-widest text-indigo-600 mb-6 flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping shrink-0"></span>
                            Disponibilità Immediata (Pronta Consegna)
                        </h4>
                        
                        <div class="overflow-x-auto pb-4">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-base font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                        <th class="py-6 px-4 whitespace-nowrap">Variante / Colore</th>
                                        @foreach($sizes as $size)
                                            <th class="py-6 px-3 text-center whitespace-nowrap">{{ $size }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($groupedVariants as $color => $variants)
                                        <tr class="group hover:bg-indigo-50/30 transition">
                                            <td class="py-8 px-4">
                                                <div class="flex items-center">
                                                    <div class="w-3 h-3 rounded-full mr-4 bg-indigo-500"></div>
                                                    <span class="text-base font-black text-gray-900 uppercase">{{ $color }}</span>
                                                </div>
                                            </td>
                                            @foreach($sizes as $size)
                                                <td class="py-6 px-2 text-center">
                                                    @php 
                                                        $variant = $variants->where('size', $size)->first(); 
                                                        $current = $giacenzaMatch['current_stock'][$size] ?? 0;
                                                        $qtyAvailable = $current > 0 ? $current : 0;
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
                                                                   class="qty-input w-20 text-center text-base font-black rounded-xl focus:ring-2 focus:ring-indigo-500 transition p-3 {{ $qtyAvailable <= 0 ? 'bg-red-50 border-red-200 text-red-400' : 'bg-gray-50 border-gray-100 hover:bg-white text-gray-900' }} disabled:cursor-not-allowed">
                                                            @if($qtyAvailable > 5)
                                                                <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-[11px] font-black text-emerald-600 whitespace-nowrap bg-white px-2 shadow-sm rounded-full border border-emerald-50 mb-1">
                                                                    {{ $qtyAvailable }}
                                                                </span>
                                                            @elseif($qtyAvailable <= 5 && $qtyAvailable > 0)
                                                                <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-[11px] font-black text-orange-500 whitespace-nowrap bg-white px-2 shadow-sm rounded-full border border-orange-50 mb-1">
                                                                    SOLO {{ $qtyAvailable }}
                                                                </span>
                                                            @elseif($qtyAvailable <= 0)
                                                                <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-[11px] font-black text-rose-400 whitespace-nowrap bg-white px-2 shadow-sm rounded-full border border-rose-50 mb-1">
                                                                    ESAU.
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @php $inputIndex++; @endphp
                                                    @else
                                                        <div class="w-20 mx-auto h-12 bg-gray-50 rounded-xl flex items-center justify-center opacity-20">
                                                            <div class="w-1.5 h-6 bg-gray-300 rotate-45"></div>
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
                            $futureDates = array_keys($giacenzaMatch['future_stock']);
                            $datesListText = implode(', ', $futureDates);
                            $defaultFutureDate = $futureDates[0] ?? '';
                        @endphp
                        
                        <div class="mt-16 border-t border-gray-100 pt-12">
                            <div class="bg-indigo-50/40 rounded-3xl p-8 border border-indigo-100">
                                <h4 class="font-black text-sm uppercase tracking-widest text-indigo-800 mb-2 flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full bg-indigo-600 animate-pulse"></span>
                                    Prenotazione Disponibilità Future
                                </h4>
                                <p class="text-xs font-bold text-indigo-600/80 uppercase tracking-widest mb-6">
                                    Arrivi previsti: {{ $datesListText }}
                                </p>
                                
                                <div class="overflow-x-auto pb-4">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="text-base font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                                <th class="py-6 px-4 whitespace-nowrap">Variante / Colore</th>
                                                @foreach($sizes as $size)
                                                    <th class="py-6 px-3 text-center whitespace-nowrap">{{ $size }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-indigo-100/50">
                                            @foreach($groupedVariants as $color => $variants)
                                                <tr class="group hover:bg-indigo-50/50 transition">
                                                    <td class="py-8 px-4">
                                                        <div class="flex items-center">
                                                            <div class="w-3 h-3 rounded-full mr-4 bg-indigo-400"></div>
                                                            <span class="text-base font-black text-gray-900 uppercase">{{ $color }}</span>
                                                        </div>
                                                    </td>
                                                    @foreach($sizes as $size)
                                                        <td class="py-6 px-2 text-center">
                                                            @php 
                                                                $variant = $variants->where('size', $size)->first(); 
                                                                $current = $giacenzaMatch['current_stock'][$size] ?? 0;
                                                                $qtyFuture = ($summedFutureStock[$size] ?? 0) + $current;
                                                            @endphp
                                                            @if($variant)
                                                                <div class="relative inline-block">
                                                                    <input type="hidden" name="items[{{ $inputIndex }}][variant_id]" value="{{ $variant->id }}">
                                                                    <input type="hidden" name="items[{{ $inputIndex }}][delivery_date]" value="{{ $defaultFutureDate }}">
                                                                    <input type="number" 
                                                                           name="items[{{ $inputIndex }}][quantity]" 
                                                                           min="0" 
                                                                           max="{{ $qtyFuture > 0 ? $qtyFuture : 0 }}" 
                                                                           value="0" 
                                                                           placeholder="0"
                                                                           {{ $qtyFuture <= 0 ? 'disabled' : '' }}
                                                                           class="qty-input w-20 text-center text-base font-black rounded-xl focus:ring-2 focus:ring-indigo-500 transition p-3 {{ $qtyFuture <= 0 ? 'bg-red-50 border-red-200 text-red-400' : 'bg-white border-indigo-200 text-gray-900' }} disabled:cursor-not-allowed">
                                                                    @if($qtyFuture > 0)
                                                                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-[11px] font-black text-indigo-600 whitespace-nowrap bg-white px-2 shadow-sm rounded-full border border-indigo-100">
                                                                            +{{ $qtyFuture }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                @php $inputIndex++; @endphp
                                                            @else
                                                                <div class="w-20 mx-auto h-12 bg-gray-50 rounded-xl flex items-center justify-center opacity-20">
                                                                    <div class="w-1.5 h-6 bg-gray-300 rotate-45"></div>
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

                    <!-- Sezione Riepilogo e Invio al Carrello -->
                    <div class="mt-12 bg-gray-50 rounded-[32px] p-8 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Riepilogo Selezione</span>
                            <div class="flex items-baseline gap-2">
                                <span id="total-qty-display" class="text-4xl font-black text-slate-900 leading-none">0</span>
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Pezzi Totali</span>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full md:w-auto px-12 bg-black border border-zinc-950 text-white py-5 rounded-2xl text-xs font-black uppercase tracking-widest hover:border-yellow-400 hover:text-yellow-400 shadow-xl shadow-black/10 transition duration-300 flex items-center justify-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Aggiungi al Carrello
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 text-center mt-4 font-black uppercase tracking-widest opacity-60">L'ordine verrà salvato come bozza nel carrello B2B.</p>
                </form>
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
