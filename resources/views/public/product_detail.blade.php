@extends('public.layouts.main')

@section('title', $product->name . ' | BICAP Official Shop')

@php
    $isEn = app()->getLocale() === 'en';
    $c = $product->characteristics ?? [];
    $cat = $c['CAT-SICUREZZA'] ?? ($c['CAT-SICUREZZA-FILTRO'] ?? '');
    $price = $product->price ? (float)$product->price : 89.00;
    
    // All Photos
    $photos = [];
    if (!empty($product->image_url)) $photos[] = $product->image_url;
    if (!empty($c['FOTO-PRINCIPALE-PRODOTTO-WEB'])) $photos[] = $c['FOTO-PRINCIPALE-PRODOTTO-WEB'];
    for ($i = 1; $i <= 10; $i++) {
        if (!empty($c["FOTO-AGGIUNTIVA-{$i}"])) {
            $photos[] = $c["FOTO-AGGIUNTIVA-{$i}"];
        }
    }
    if (!empty($c['FOTO SUOLA'])) $photos[] = $c['FOTO SUOLA'];
    $photos = array_values(array_unique(array_filter($photos)));

    $foto360 = $c['PRIMA-FOTO-360-LARGE'] ?? null;
    $frames360 = [];
    if (!empty($foto360)) {
        for ($i = 1; $i <= 18; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $frames360[] = preg_replace('/-(?:01|1)\.([a-zA-Z0-9]+)$/i', "-{$num}.$1", $foto360);
        }
    }

    // Plus Icons
    $plusIcons = [];
    for ($i = 1; $i <= 10; $i++) {
        if (!empty($c["ICONA-PLUS-{$i}"])) {
            $plusIcons[] = $c["ICONA-PLUS-{$i}"];
        }
    }

    // Component Images
    $componenti = [];
    for ($i = 1; $i <= 10; $i++) {
        if (!empty($c["COMPONENTE-{$i}"])) {
            $componenti[] = $c["COMPONENTE-{$i}"];
        }
    }

    // Safety Icons
    $safetyIcons = [];
    for ($i = 1; $i <= 20; $i++) {
        if (!empty($c["ICONA-SICUREZZA-{$i}"])) {
            $safetyIcons[] = $c["ICONA-SICUREZZA-{$i}"];
        }
    }

    // Downloads (EN / IT priority)
    $docConformita = $isEn
        ? ($c['URL-DICHIARAZIONE-CONFORMITA-EN'] ?? ($c['URL-DICHIARAZIONE-CONFORMITA-IT'] ?? null))
        : ($c['URL-DICHIARAZIONE-CONFORMITA-IT'] ?? ($c['URL-DICHIARAZIONE-CONFORMITA-EN'] ?? null));
    $schedaTecnica = $isEn
        ? ($c['URL-TDS-EN'] ?? ($c['URL-TDS IT'] ?? null))
        : ($c['URL-TDS IT'] ?? ($c['URL-TDS-EN'] ?? null));
    $catalogo = $isEn
        ? ($c['URL-CATALOGO-EN'] ?? ($c['URL-CATALOGO-IT'] ?? null))
        : ($c['URL-CATALOGO-IT'] ?? ($c['URL-CATALOGO-EN'] ?? null));

    // Specs (EN / IT priority)
    $tomaia = $isEn
        ? ($c['tomaia-descrizione-en'] ?? ($c['MATERIALE-FILTRO-EN'] ?? ($c['tomaia-descrizione-it'] ?? ($c['MATERIALE-FILTRO-IT'] ?? null))))
        : ($c['tomaia-descrizione-it'] ?? ($c['MATERIALE-FILTRO-IT'] ?? ($c['tomaia-descrizione-en'] ?? null)));
    $fodera = $isEn
        ? ($c['fodera-descrizione-en'] ?? ($c['fodera-descrizione-it'] ?? null))
        : ($c['fodera-descrizione-it'] ?? ($c['fodera-descrizione-en'] ?? null));
    $puntale = $isEn
        ? ($c['puntale-descrizione-en'] ?? ($c['PUNTALE-FILTRO-EN'] ?? ($c['puntale-descrizione-it'] ?? ($c['PUNTALE-FILTRO-IT'] ?? null))))
        : ($c['puntale-descrizione-it'] ?? ($c['PUNTALE-FILTRO-IT'] ?? ($c['puntale-descrizione-en'] ?? null)));
    $lamina = $isEn
        ? ($c['lamina-descrizione-en'] ?? ($c['lamina-descrizione-it'] ?? null))
        : ($c['lamina-descrizione-it'] ?? ($c['lamina-descrizione-en'] ?? null));
    $suola = $isEn
        ? ($c['suola-descrizione-en'] ?? ($c['SUOLA-FILTRO-EN'] ?? ($c['suola-descrizione-it'] ?? ($c['SUOLA-FILTRO-IT'] ?? null))))
        : ($c['suola-descrizione-it'] ?? ($c['SUOLA-FILTRO-IT'] ?? ($c['suola-descrizione-en'] ?? null)));
    $taglie = $c['RANGE-TAGLIE-VALORE'] ?? '36/48';
    $calzata = $c['CALZATA'] ?? 'Mondopoint 11';
    $norma = $c['NORMA'] ?? '20345:2022';
    $settori = $isEn
        ? ($c['SETTORE-DI-UTILIZZO-EN'] ?? ($c['SETTORE-DI-UTILIZZO-IT'] ?? null))
        : ($c['SETTORE-DI-UTILIZZO-IT'] ?? ($c['SETTORE-DI-UTILIZZO-EN'] ?? null));

    // Barcodes
    $barcodes = [];
    for ($sz = 35; $sz <= 49; $sz++) {
        if (!empty($c["GTIN TAGLIA {$sz}"])) {
            $barcodes[$sz] = $c["GTIN TAGLIA {$sz}"];
        }
    }

    // Description (Localizzato con fallback)
    $approfondimento = $isEn
        ? ($c['descrizione-articolo-en'] ?? ($c['descrizione-articolo-EN'] ?? ($product->localized_description ?: ($c['descrizione-articolo-it'] ?? null))))
        : ($c['descrizione-articolo-it'] ?? ($product->localized_description ?: ($c['descrizione-articolo-en'] ?? null)));
    $descrizioneEstesa = $isEn
        ? ($c['DESCRIZIONE ESTESA HTML EN'] ?? ($product->localized_extended_html ?: ($c['DESCRIZIONE ESTESA HTML IT'] ?? null)))
        : ($c['DESCRIZIONE ESTESA HTML IT'] ?? ($product->localized_extended_html ?: ($c['DESCRIZIONE ESTESA HTML EN'] ?? null)));
@endphp

@section('content')
<div class="bg-white text-black min-h-screen py-6 sm:py-10" 
     x-data="{ 
         activeImage: '{{ $photos[0] ?? '' }}', 
         is360: false, 
         selectedSize: '42', 
         quantity: 1, 
         accordionOpen: null, 
         showFullDesc: false 
     }">
    <div class="max-w-[1340px] mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-none flex items-center justify-between text-sm font-bold shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <a href="{{ route('public.shop.cart.index') }}" class="underline hover:text-black font-extrabold uppercase text-xs tracking-wider">{{ $isEn ? 'Go to cart →' : 'Vai al carrello →' }}</a>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-none flex items-center gap-2 text-sm font-bold shadow-xs">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Top Breadcrumbs & Back Button (Exact Bicap Shop Style) -->
        <div class="flex items-center justify-between pb-5 mb-6 border-b border-gray-100">
            <a href="{{ route('public.home') }}" class="btn-prodotti">
                {{ $isEn ? 'PRODUCTS +' : 'PRODOTTI +' }}
            </a>

            <nav class="text-xs uppercase font-semibold tracking-wider text-zinc-500">
                <a href="{{ route('public.home') }}" class="hover:text-black">Bicap</a>
                <span class="mx-1 text-zinc-300">/</span>
                <a href="{{ route('public.home') }}" class="hover:text-black">Shop</a>
                <span class="mx-1 text-zinc-300">/</span>
                <span class="text-zinc-900 font-bold">{{ $product->name }}</span>
            </nav>
        </div>

        <!-- Main Product Section (2 Columns: Left Gallery, Right Details) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            <!-- LEFT COLUMN: Vertical Thumbnails + Big Image Display (Bicap.shop Style) -->
            <div class="lg:col-span-7 flex flex-col-reverse sm:flex-row gap-4 sm:gap-6 items-start">
                
                <!-- Thumbnails Column -->
                <div class="flex sm:flex-col gap-2.5 overflow-x-auto sm:overflow-visible shrink-0 pb-2 sm:pb-0 max-h-[580px] sm:overflow-y-auto pr-1">
                    @foreach($photos as $index => $photo)
                        <button type="button"
                                @click="activeImage = '{{ $photo }}'; is360 = false;" 
                                class="w-16 h-16 sm:w-18 sm:h-18 p-1 border rounded-none flex items-center justify-center transition-all bg-white cursor-pointer"
                                :class="(!is360 && activeImage === '{{ $photo }}') ? 'border-black ring-1 ring-black' : 'border-gray-200 hover:border-gray-400 opacity-80 hover:opacity-100'">
                            <img src="{{ $photo }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain pointer-events-none" onerror="this.closest('button').remove()">
                        </button>
                    @endforeach

                    @if($foto360 && count($frames360) > 0)
                        <!-- 360 Thumbnail -->
                        <button type="button"
                                @click="if (is360) { window.dispatchEvent(new CustomEvent('bicap-360-spin')); } else { is360 = true; }" 
                                class="w-16 h-16 sm:w-18 sm:h-18 p-1 border rounded-none flex flex-col items-center justify-center transition-all bg-zinc-50 cursor-pointer relative"
                                :class="is360 ? 'border-black ring-1 ring-black' : 'border-gray-200 hover:border-gray-400 opacity-80 hover:opacity-100'"
                                title="{{ $isEn ? 'View and spin 360°' : 'Visualizza e ruota a 360°' }}">
                            <img src="https://bicap.it/images/360.png" alt="360 view" class="w-7 h-7 object-contain mb-0.5 pointer-events-none">
                            <span class="font-bebas text-[11px] leading-none text-black">360°</span>
                        </button>
                    @endif
                </div>

                <!-- Big Main Photo Display -->
                <div class="flex-1 w-full bg-white flex items-center justify-center p-2 sm:p-6 min-h-[360px] sm:min-h-[500px]">
                    <template x-if="!is360">
                        <img :src="activeImage" alt="{{ $product->name }}" class="max-h-[480px] max-w-full object-contain transition-all duration-200">
                    </template>
                    <template x-if="is360">
                        <div x-data="bicap360Viewer(@js($frames360))" 
                             class="flex flex-col items-center justify-center w-full select-none"
                             @keydown.window.escape="is360 = false">
                             
                            <!-- Interactive 360 Drag & Spin Canvas/Image Area -->
                            <div class="relative w-full flex items-center justify-center cursor-grab active:cursor-grabbing touch-none py-2"
                                 @mousedown="startDrag($event)"
                                 @mousemove="onDrag($event)"
                                 @mouseup="stopDrag()"
                                 @mouseleave="stopDrag()"
                                 @touchstart="startDrag($event)"
                                 @touchmove="onDrag($event)"
                                 @touchend="stopDrag()"
                                 @wheel.prevent="onWheel($event)"
                                 title="{{ $isEn ? 'Drag to rotate' : 'Trascina per ruotare' }}">

                                <!-- Loading Indicator -->
                                <div x-show="!isLoaded && loadedCount === 0" class="absolute inset-0 flex items-center justify-center bg-white/80 z-20">
                                    <div class="flex items-center gap-2 text-xs font-semibold text-zinc-500 uppercase">
                                        <i class="fa fa-spinner fa-spin"></i>
                                        <span>{{ $isEn ? 'Loading 360°...' : 'Caricamento 360°...' }}</span>
                                    </div>
                                </div>

                                <!-- Active 360 Frame Image -->
                                <img :src="frames[currentFrame]" 
                                     alt="{{ $product->name }} {{ $isEn ? '360° Spin' : 'Rotazione 360°' }}" 
                                     class="max-h-[460px] max-w-full object-contain pointer-events-none transition-none"
                                     draggable="false">

                                <!-- 360 Badge Floating Top Right -->
                                <div class="absolute top-2 right-2 flex items-center gap-1.5 px-2.5 py-1 bg-white/90 border border-gray-200 shadow-2xs rounded text-[11px] font-bold text-zinc-800 uppercase pointer-events-none">
                                    <img src="https://bicap.it/images/360.png" class="w-4 h-4 object-contain">
                                    <span x-text="`${currentFrame + 1} / ${totalFrames}`"></span>
                                </div>
                            </div>

                            <!-- Interactive Controls & Hint Bar -->
                            <div class="mt-3 flex flex-wrap items-center justify-center gap-2.5 sm:gap-3">
                                
                                <!-- Rotate Left Button -->
                                <button type="button" 
                                        @click="prevFrame()" 
                                        class="w-8 h-8 rounded-full border border-gray-300 bg-white hover:bg-gray-100 flex items-center justify-center text-xs text-black cursor-pointer shadow-2xs transition-colors"
                                        title="{{ $isEn ? 'Rotate left' : 'Ruota a sinistra' }}">
                                    <i class="fa fa-chevron-left"></i>
                                </button>

                                <!-- Play/Pause Autospin Button -->
                                <button type="button" 
                                        @click="toggleAutoSpin()" 
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-gray-300 bg-white hover:bg-gray-100 text-xs font-bold text-black uppercase cursor-pointer shadow-2xs transition-colors">
                                    <i class="fa text-[11px]" :class="isAutoSpinning ? 'fa-pause' : 'fa-play'"></i>
                                    <span x-text="isAutoSpinning ? '{{ $isEn ? 'Pause' : 'Pausa' }}' : '{{ $isEn ? 'Auto Spin' : 'Rotazione Automatica' }}'"></span>
                                </button>

                                <!-- Rotate Right Button -->
                                <button type="button" 
                                        @click="nextFrame()" 
                                        class="w-8 h-8 rounded-full border border-gray-300 bg-white hover:bg-gray-100 flex items-center justify-center text-xs text-black cursor-pointer shadow-2xs transition-colors"
                                        title="{{ $isEn ? 'Rotate right' : 'Ruota a destra' }}">
                                    <i class="fa fa-chevron-right"></i>
                                </button>

                                <!-- Hint Text -->
                                <div class="w-full text-center text-[12px] font-medium text-zinc-500 mt-1">
                                    <i class="fa fa-arrows-h mr-1 text-[#e35205]"></i> {{ $isEn ? 'Drag with mouse or touch to rotate 360°' : 'Trascina con il mouse o tocca per ruotare a 360°' }}
                                </div>
                            </div>

                        </div>
                    </template>
                </div>

            </div>

            <!-- RIGHT COLUMN: Product Info, Price, Add to Cart, Specs, Accordions -->
            <div class="lg:col-span-5 space-y-5">
                
                <!-- Title & Certification (Bebas Neue) -->
                <div>
                    <h1 class="font-bebas text-4xl sm:text-5xl text-black uppercase tracking-tight leading-none">
                        {{ $product->name }}
                        @if($cat)
                            <span class="block text-2xl sm:text-3xl text-zinc-900 font-bebas mt-1">{{ $cat }}</span>
                        @endif
                    </h1>
                    <h2 class="font-bebas text-lg sm:text-xl text-zinc-500 uppercase tracking-wide mt-1">
                        {{ $product->code }}
                    </h2>
                </div>

                <!-- Price (Bebas Neue Large) -->
                <div class="pt-1 pb-2">
                    <div class="font-bebas text-4xl sm:text-5xl text-black flex items-baseline gap-2">
                        € {{ number_format($price, 2, ',', '.') }}
                        <span class="text-xs font-sans font-bold uppercase text-zinc-500 tracking-wider">{{ $isEn ? 'VAT INCL.' : 'IVA INC.' }}</span>
                    </div>
                </div>

                <!-- Size & Quantity Selectors and Add to Cart Form -->
                <form action="{{ route('public.shop.cart.add') }}" method="POST" class="pt-1 space-y-4">
                    @csrf
                    <input type="hidden" name="b2b_product_id" value="{{ $product->id }}">

                    @php
                        // Filtra esclusivamente le varianti disponibili (giacenza > 0)
                        $availableVariants = $product->variants->filter(function($v) {
                            return (int)$v->quantity > 0;
                        })->sortBy(function($v) {
                            return (int)$v->size;
                        })->values();
                        $hasAvailableSizes = $availableVariants->isNotEmpty();
                    @endphp

                    <div class="flex items-center gap-4">
                        <div>
                            <label for="size-select" class="block font-bebas text-lg uppercase text-black mb-1">
                                {{ $isEn ? 'SIZE' : 'TAGLIA' }}
                            </label>
                            @if($hasAvailableSizes)
                                <select id="size-select" name="taglia" class="w-24 h-10 bg-white border border-gray-300 rounded-none px-2 text-sm font-bold text-zinc-900 focus:ring-1 focus:ring-black focus:border-black outline-none cursor-pointer">
                                    @foreach($availableVariants as $v)
                                        <option value="{{ $v->size }}">{{ $v->size }}</option>
                                    @endforeach
                                </select>
                            @else
                                <div class="h-10 flex items-center px-3 text-xs font-bold text-red-600 bg-red-50 border border-red-200 uppercase">
                                    {{ $isEn ? 'OUT OF STOCK' : 'ESAURITO' }}
                                </div>
                            @endif
                        </div>

                        <div>
                            <label for="quantity-input" class="block font-bebas text-lg uppercase text-black mb-1">
                                {{ $isEn ? 'QUANTITY' : 'QUANTITÀ' }}
                            </label>
                            <input id="quantity-input" name="quantita" type="number" min="1" max="99" value="1" {{ !$hasAvailableSizes ? 'disabled' : '' }} class="w-16 h-10 bg-white border border-gray-300 rounded-none px-2 text-sm font-bold text-center text-zinc-900 focus:ring-1 focus:ring-black focus:border-black outline-none">
                        </div>
                    </div>

                    <!-- Add to Cart (Vibrant Bicap Orange Button) -->
                    <div>
                        @if($hasAvailableSizes)
                            <button type="submit" class="btn-bicap-orange w-full flex items-center justify-center cursor-pointer border-0">
                                {{ $isEn ? 'ADD TO CART' : 'AGGIUNGI AL CARRELLO' }}
                            </button>
                        @else
                            <button type="button" disabled class="w-full py-3.5 bg-zinc-200 text-zinc-500 font-bebas text-xl uppercase tracking-wider cursor-not-allowed border-0 text-center">
                                {{ $isEn ? 'PRODUCT UNAVAILABLE' : 'PRODOTTO NON DISPONIBILE' }}
                            </button>
                        @endif
                    </div>

                    @if($hasAvailableSizes)
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const variantStocks = {
                                    @foreach($availableVariants as $v)
                                        "{{ $v->size }}": {{ (int)$v->quantity }},
                                    @endforeach
                                };
                                const sizeSelect = document.getElementById('size-select');
                                const qtyInput = document.getElementById('quantity-input');
                                if (sizeSelect && qtyInput) {
                                    function updateMax() {
                                        const maxQty = variantStocks[sizeSelect.value] || 1;
                                        qtyInput.max = maxQty;
                                        if (parseInt(qtyInput.value, 10) > maxQty) {
                                            qtyInput.value = maxQty;
                                        }
                                    }
                                    sizeSelect.addEventListener('change', updateMax);
                                    updateMax();
                                }
                            });
                        </script>
                    @endif
                </form>

                <!-- Free Shipping Banner (Green Truck) -->
                @php
                    $detailFreeThreshold = (float)(\App\Models\Setting::where('key', 'shop_free_shipping_threshold')->value('value') ?? 89);
                    $detailFreeScope = \App\Models\Setting::where('key', 'shop_free_shipping_scope')->value('value') ?? 'italy';
                @endphp
                @if($detailFreeThreshold > 0)
                    <div class="flex items-center gap-2.5 text-xs font-bold uppercase text-[#008751] pt-1">
                        <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24">
                            <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                        </svg>
                        <span>
                            @if($isEn)
                                FREE SHIPPING {{ $detailFreeScope === 'all' ? '' : 'IN ITALY ' }}FROM € {{ (int)$detailFreeThreshold }}
                            @else
                                SPEDIZIONE GRATUITA {{ $detailFreeScope === 'all' ? '' : 'ITALIA ' }}A PARTIRE DA € {{ (int)$detailFreeThreshold }}
                            @endif
                        </span>
                    </div>
                @endif

                <!-- Payment Methods Icons -->
                <div class="flex items-center gap-3 pt-1 pb-3">
                    <img src="https://bicap.shop/images/mastercard.png" alt="MasterCard" class="h-6 w-auto object-contain">
                    <img src="https://bicap.shop/images/paypal.png" alt="PayPal" class="h-5 w-auto object-contain">
                    <img src="https://bicap.shop/images/visa.png" alt="Visa" class="h-5 w-auto object-contain">
                    <img src="https://bicap.shop/images/amex.png" alt="AMEX" class="h-6 w-auto object-contain">
                </div>

                <!-- Caratteristiche Singole (Technical Specs List) -->
                <div class="bicap-specs-list pt-3 border-t border-gray-200 space-y-1.5">
                    @if(!empty($tomaia))
                        <div><strong>{{ $isEn ? 'Upper:' : 'Tomaia:' }}</strong> <span>{{ $tomaia }}</span></div>
                    @endif
                    @if(!empty($fodera))
                        <div><strong>{{ $isEn ? 'Lining:' : 'Fodera:' }}</strong> <span>{{ $fodera }}</span></div>
                    @endif
                    @if(!empty($puntale))
                        <div><strong>{{ $isEn ? 'Toe Cap:' : 'Puntale:' }}</strong> <span>{{ $puntale }}</span></div>
                    @endif
                    @if(!empty($lamina))
                        <div><strong>{{ $isEn ? 'Midsole:' : 'Lamina:' }}</strong> <span>{{ $lamina }}</span></div>
                    @endif
                    @if(!empty($suola))
                        <div><strong>{{ $isEn ? 'Sole:' : 'Suola:' }}</strong> <span>{{ $suola }}</span></div>
                    @endif
                    @if(!empty($taglie))
                        <div><strong>{{ $isEn ? 'Sizes:' : 'Taglie:' }}</strong> <span>{{ $taglie }}</span></div>
                    @endif
                </div>

                <!-- 1. Box grigio sotto la descrizione (Approfondimento - Bicap.shop Style) -->
                @if(!empty($approfondimento))
                    <div class="approfondimento">
                        <p>{{ $approfondimento }}</p>
                    </div>
                @endif

                <!-- 2. Scritta Mondopoint in grassetto e Norma -->
                <div class="font-bold text-[13px] sm:text-[14px] text-black uppercase leading-tight my-2.5">
                    @if($calzata) <div>{{ $calzata }}</div> @endif
                    @if($norma) <div>{{ $norma }}</div> @endif
                </div>

                <!-- 3. Icone di condivisione con separatore | (Identiche a bicap.shop) -->
                <div class="flex items-center gap-2 text-[15px] text-black pt-1 pb-1">
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" class="text-black hover:text-[#e35205] transition-colors" title="LinkedIn">
                        <i class="fa fa-linkedin"></i>
                    </a>
                    <span class="text-zinc-300 font-light select-none">|</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="text-black hover:text-[#e35205] transition-colors" title="Facebook">
                        <i class="fa fa-facebook"></i>
                    </a>
                    <span class="text-zinc-300 font-light select-none">|</span>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($product->name . ' ' . url()->current()) }}" target="_blank" class="text-black hover:text-[#e35205] transition-colors" title="WhatsApp">
                        <i class="fa fa-whatsapp"></i>
                    </a>
                    <span class="text-zinc-300 font-light select-none">|</span>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($product->name) }}" target="_blank" class="text-black hover:text-[#e35205] transition-colors" title="Twitter">
                        <i class="fa fa-twitter"></i>
                    </a>
                    <span class="text-zinc-300 font-light select-none">|</span>
                    <a href="mailto:?subject={{ rawurlencode($product->name) }}&body={{ rawurlencode(url()->current()) }}" class="text-black hover:text-[#e35205] transition-colors" title="Email">
                        <i class="fa fa-envelope"></i>
                    </a>
                </div>

                <!-- Linea divisoria nera prima di DOWNLOAD -->
                <hr class="border-t border-black my-5">

                <!-- DOWNLOAD Section (Bebas Neue Title + White/Black Outline Buttons) -->
                <div>
                    <h3 class="font-bebas text-3xl text-black uppercase mb-3">
                        DOWNLOAD
                    </h3>
                    <div class="flex flex-wrap items-center gap-2.5" x-data="{ showBarcodes: false }">
                        @if($docConformita)
                            <a href="{{ $docConformita }}" target="_blank" class="btn-bicap-download">
                                {{ $isEn ? 'DECLARATION OF CONFORMITY' : 'DICHIARAZIONE DI CONFORMITÀ' }}
                            </a>
                        @endif
                        @if($schedaTecnica)
                            <a href="{{ $schedaTecnica }}" target="_blank" class="btn-bicap-download">
                                {{ $isEn ? 'TECHNICAL DATA SHEET' : 'SCHEDA TECNICA' }}
                            </a>
                        @endif
                        @if($catalogo)
                            <a href="{{ $catalogo }}" target="_blank" class="btn-bicap-download">
                                {{ $isEn ? 'CATALOGUE' : 'CATALOGO' }}
                            </a>
                        @endif
                        @if(!empty($barcodes))
                            <div class="relative">
                                <button type="button" @click="showBarcodes = !showBarcodes" class="btn-bicap-download flex items-center gap-1.5 cursor-pointer">
                                    <span>BARCODE</span>
                                    <span class="text-[9px]">▼</span>
                                </button>
                                <div x-show="showBarcodes" @click.away="showBarcodes = false" class="absolute left-0 top-full mt-1 bg-white border border-black shadow-lg p-3 z-50 min-w-[200px] max-h-60 overflow-y-auto text-xs" style="display: none;">
                                    <div class="font-bold text-black uppercase mb-1.5 border-b pb-1">{{ $isEn ? 'EAN / GTIN Codes' : 'Codici EAN / GTIN' }}</div>
                                    @foreach($barcodes as $sz => $code)
                                        <div class="py-1 flex justify-between gap-2 border-b border-gray-100 last:border-0 font-mono">
                                            <span class="font-bold text-black">{{ $isEn ? 'Size' : 'Tg' }} {{ $sz }}:</span>
                                            <span class="text-zinc-700">{{ $code }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Info Accordions (Dinamicamente dal Gestionale: Sezione Condizioni di Vendita) -->
                <div class="pt-4 border-t border-gray-200">
                    @if(isset($accordionArticles) && $accordionArticles->count() > 0)
                        @foreach($accordionArticles as $article)
                            @php
                                $accKey = 'acc_' . $article->id;
                                $titolo = $article->titolo;
                                $descrizione = $article->descrizione;
                            @endphp
                            <div>
                                <button type="button" 
                                        @click="accordionOpen = (accordionOpen === '{{ $accKey }}' ? null : '{{ $accKey }}')" 
                                        class="bicap-accordion-btn">
                                    <span>{{ $titolo }}</span>
                                    <span class="font-bold text-lg" x-text="accordionOpen === '{{ $accKey }}' ? '−' : '+'"></span>
                                </button>
                                <div x-show="accordionOpen === '{{ $accKey }}'" x-collapse class="bicap-accordion-content prose max-w-none text-zinc-800 text-[13px] leading-relaxed">
                                    {!! $descrizione !!}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback se non ci sono articoli nella sezione -->
                        <div>
                            <button type="button" @click="accordionOpen = (accordionOpen === 'condizioni' ? null : 'condizioni')" class="bicap-accordion-btn">
                                <span>{{ $isEn ? 'TERMS OF SALE' : 'CONDIZIONI DI VENDITA' }}</span>
                                <span class="font-bold text-lg" x-text="accordionOpen === 'condizioni' ? '−' : '+'"></span>
                            </button>
                            <div x-show="accordionOpen === 'condizioni'" x-collapse class="bicap-accordion-content">
                                @if($isEn)
                                    <p class="mb-2"><strong>Online purchase of products:</strong> To purchase items online, the Customer must access the product catalogue from the "SHOP" menu.</p>
                                    <p class="mb-2"><strong>Sale price:</strong> For each item the unit price is indicated in EURO (€), VAT included and excluding shipping costs.</p>
                                    <p><strong>Payment methods:</strong> Credit card (VISA, MASTERCARD, STRIPE), Bank transfer, PAYPAL.</p>
                                @else
                                    <p class="mb-2"><strong>Acquisto on-line dei prodotti:</strong> Per poter acquistare on-line gli articoli del negozio, il Cliente deve accedere al catalogo prodotti dal menù "SHOP".</p>
                                    <p class="mb-2"><strong>Prezzo di vendita:</strong> Per ogni articolo è indicato il prezzo unitario espresso in EURO (€), I.V.A. compresa e al netto dei costi di spedizione.</p>
                                    <p><strong>Modalità di pagamento:</strong> Carta di credito (VISA, MASTERCARD, STRIPE), Bonifico bancario, Circuito PAYPAL.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- ================= FULL-WIDTH LOWER SECTION: ICONS, COMPONENTI, SICUREZZA, SETTORI, DESCRIZIONE ================= -->
        <div class="simboli-section mt-14" x-data="{ descExpanded: false }">
            
            <!-- 1. PLUS ICONS -->
            @if(count($plusIcons) > 0)
                <hr class="simboli-hr">
                <div class="simboli-row">
                    <div class="simboli-label">
                        <strong>PLUS</strong>
                    </div>
                    <div class="simboli-content">
                        @foreach($plusIcons as $icon)
                            <img src="{{ $icon }}" alt="Plus Icon" onerror="this.remove()">
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 2. COMPONENTI (High-res Product Components) -->
            @if(count($componenti) > 0)
                <hr class="simboli-hr">
                <div class="simboli-row">
                    <div class="simboli-label">
                        <strong>{{ $isEn ? 'COMPONENTS' : 'COMPONENTI' }}</strong>
                    </div>
                    <div class="simboli-content simboli-content-componenti">
                        @foreach($componenti as $comp)
                            <img src="{{ $comp }}" alt="{{ $isEn ? 'Component' : 'Componente' }}" onerror="this.remove()">
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 3. SICUREZZA (Safety Standard Icons) -->
            @if(count($safetyIcons) > 0)
                <hr class="simboli-hr">
                <div class="simboli-row">
                    <div class="simboli-label">
                        <strong>{{ $isEn ? 'SAFETY' : 'SICUREZZA' }}</strong>
                    </div>
                    <div class="simboli-content simboli-content-sicurezza">
                        @foreach($safetyIcons as $icon)
                            <img src="{{ $icon }}" alt="{{ $isEn ? 'Safety' : 'Sicurezza' }}" onerror="this.remove()">
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 4. SETTORI DI UTILIZZO -->
            @if(!empty($settori))
                <hr class="simboli-hr">
                <div class="simboli-row">
                    <div class="simboli-label">
                        <strong>{{ $isEn ? 'SECTORS OF USE' : 'SETTORI DI UTILIZZO' }}</strong>
                    </div>
                    <div class="simboli-content">
                        <span class="simboli-settori-text">
                            {{ rtrim(str_replace([' / ', '/'], ' - ', $settori), ' -') }} -
                        </span>
                    </div>
                </div>
            @endif

            <!-- 5. DESCRIZIONE ESTESA (2-line clamp + Leggi di piu' toggle) -->
            @if(!empty($descrizioneEstesa))
                <hr class="simboli-hr">
                <div class="simboli-row-start">
                    <div class="simboli-label pt-0.5">
                        <strong>{{ $isEn ? 'DESCRIPTION' : 'DESCRIZIONE' }}</strong>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <div class="simboli-descrizione" :class="descExpanded ? 'expanded' : ''">
                            {!! $descrizioneEstesa !!}
                        </div>
                        <button type="button" 
                                class="simboli-toggle-descrizione" 
                                @click="descExpanded = !descExpanded">
                            <span x-text="descExpanded ? '{{ $isEn ? 'Read less' : 'Leggi meno' }}' : '{{ $isEn ? 'Read more' : 'Leggi di più' }}'"></span>
                            <i class="fa ml-1.5 text-xs" :class="descExpanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                    </div>
                </div>
            @endif

            <hr class="simboli-hr">
        </div>

    </div>

    <!-- ================= POTREBBE INTERESSARTI (Full-width Gray Title Banner #f3f3f3 + 4 Items Grid) ================= -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="bicap-correlati-banner">
            <div class="max-w-[1340px] mx-auto px-4 sm:px-6 lg:px-8">
                <h3 class="bicap-correlati-titolo">
                    {{ $isEn ? 'YOU MAY ALSO LIKE' : 'POTREBBE INTERESSARTI' }}
                </h3>
            </div>
        </div>

        <div class="max-w-[1340px] mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                @foreach($relatedProducts as $rel)
                    <a href="{{ route('public.product.detail', $rel->id) }}" class="bicap-correlati-item group">
                        <div class="w-full h-32 sm:h-40 md:h-44 flex items-center justify-center p-1 mb-1 bg-white">
                            <img src="{{ $rel->image_url }}" alt="{{ $rel->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <h4>
                            {{ $rel->name }}
                        </h4>
                        <div class="bicap-correlati-code">
                            {{ $rel->code }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    window.__bicap360Frames = @js($frames360 ?? []);
    
    // Background preloader for 360 frames to ensure zero lag
    if (window.__bicap360Frames && window.__bicap360Frames.length > 0) {
        if ('requestIdleCallback' in window) {
            requestIdleCallback(() => {
                window.__bicap360Frames.forEach(src => {
                    const img = new Image();
                    img.src = src;
                });
            });
        } else {
            setTimeout(() => {
                window.__bicap360Frames.forEach(src => {
                    const img = new Image();
                    img.src = src;
                });
            }, 300);
        }
    }

    function bicap360Viewer(frames) {
        return {
            frames: frames || [],
            totalFrames: (frames || []).length,
            currentFrame: 0,
            isLoaded: false,
            loadedCount: 0,
            isAutoSpinning: false,
            autoSpinTimer: null,
            isDragging: false,
            startX: 0,
            startFrame: 0,
            sensitivity: 14, // Drag sensitivity (pixels per frame)

            init() {
                this.preloadImages();
                
                // Spin once automatically when 360 viewer is loaded
                setTimeout(() => {
                    this.startAutoSpin(true);
                }, 200);

                window.addEventListener('bicap-360-spin', () => {
                    this.startAutoSpin(true);
                });
            },

            preloadImages() {
                if (!this.frames || this.frames.length === 0) return;
                this.frames.forEach(url => {
                    const img = new Image();
                    img.onload = () => {
                        this.loadedCount++;
                        if (this.loadedCount >= this.totalFrames) {
                            this.isLoaded = true;
                        }
                    };
                    img.onerror = () => {
                        this.loadedCount++;
                    };
                    img.src = url;
                });
                setTimeout(() => { this.isLoaded = true; }, 1000);
            },

            startAutoSpin(once = false) {
                this.stopAutoSpin();
                this.isAutoSpinning = true;
                let steps = 0;
                const maxSteps = once ? this.totalFrames : Infinity;
                this.autoSpinTimer = setInterval(() => {
                    this.nextFrame();
                    steps++;
                    if (steps >= maxSteps) {
                        this.stopAutoSpin();
                    }
                }, 85);
            },

            stopAutoSpin() {
                this.isAutoSpinning = false;
                if (this.autoSpinTimer) {
                    clearInterval(this.autoSpinTimer);
                    this.autoSpinTimer = null;
                }
            },

            toggleAutoSpin() {
                if (this.isAutoSpinning) {
                    this.stopAutoSpin();
                } else {
                    this.startAutoSpin(false);
                }
            },

            nextFrame() {
                if (this.totalFrames > 0) {
                    this.currentFrame = (this.currentFrame + 1) % this.totalFrames;
                }
            },

            prevFrame() {
                if (this.totalFrames > 0) {
                    this.currentFrame = (this.currentFrame - 1 + this.totalFrames) % this.totalFrames;
                }
            },

            startDrag(e) {
                this.stopAutoSpin();
                this.isDragging = true;
                this.startX = e.clientX || (e.touches && e.touches[0].clientX) || 0;
                this.startFrame = this.currentFrame;
            },

            onDrag(e) {
                if (!this.isDragging || this.totalFrames === 0) return;
                const currentX = e.clientX || (e.touches && e.touches[0].clientX) || 0;
                const deltaX = currentX - this.startX;
                const frameDelta = Math.floor(deltaX / this.sensitivity);
                let newFrame = (this.startFrame - frameDelta) % this.totalFrames;
                if (newFrame < 0) newFrame += this.totalFrames;
                this.currentFrame = newFrame;
            },

            stopDrag() {
                this.isDragging = false;
            },

            onWheel(e) {
                this.stopAutoSpin();
                if (e.deltaY > 0 || e.deltaX > 0) {
                    this.nextFrame();
                } else {
                    this.prevFrame();
                }
            }
        };
    }
</script>
@endpush
