@extends('public.layouts.main')

@section('title', $prodotto->nome . ' - ' . config('app.name'))

@section('content')
<div class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $prodotto->nome }}</h1>
            @if($prodotto->marca)
                <p class="text-sm text-gray-500 mt-1 uppercase tracking-wider">{{ $prodotto->marca }}</p>
            @endif
        </div>
        @if($collezione)
            <a href="{{ route('public.shop.collezione', $collezione->slug) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">&larr; Torna a {{ $collezione->nome }}</a>
        @else
            <a href="{{ route('public.shop.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">&larr; Torna allo Shop</a>
        @endif
    </div>
</div>

<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
        
        <!-- Galleria Immagini -->
        <div x-data="{ currentImage: 0, 
                      images: [
                        @if(is_array($prodotto->foto_aggiuntive))
                            @foreach($prodotto->foto_aggiuntive as $foto)
                                '{{ $foto }}',
                            @endforeach
                        @endif
                      ]
                    }" 
             class="flex flex-col">
             
            <template x-if="images.length > 0">
                <div class="w-full bg-white rounded-lg border overflow-hidden">
                    <img :src="images[currentImage]" alt="{{ $prodotto->nome }}" class="w-full max-h-96 object-contain">
                </div>
            </template>
            <template x-if="images.length === 0">
                 <div class="w-full h-96 max-h-96 bg-gray-100 rounded-lg flex items-center justify-center border">
                    <span class="text-gray-400">Nessuna immagine</span>
                </div>
            </template>

            <!-- Thumbnails -->
            <template x-if="images.length > 1">
                <div class="mt-4 grid grid-cols-4 gap-2">
                    <template x-for="(img, index) in images" :key="index">
                        <button @click="currentImage = index" 
                                class="border-2 rounded-lg overflow-hidden focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                :class="{'border-indigo-500': currentImage === index, 'border-transparent': currentImage !== index}">
                            <img :src="img" class="h-20 w-full object-cover">
                        </button>
                    </template>
                </div>
            </template>
        </div>

        <!-- Dettagli e Carrello -->
        <div class="mt-10 px-4 sm:px-0 lg:mt-0" 
             x-data='productB2C(@json($prodotto->variants), @json(($settings["shop_stock_infinite"] ?? "0") == "1"))'>
            <h2 class="text-3xl tracking-tight text-gray-900 font-extrabold">{{ $prodotto->nome }}</h2>
            @if($prodotto->sku_padre)
                <p class="text-sm text-gray-500 mt-2">SKU: {{ $prodotto->sku_padre }}</p>
            @endif

            <div class="mt-3">
                <p class="text-3xl text-gray-900 font-bold" x-html="formattedPrice"></p>
            </div>

            <div class="mt-6 border-t border-gray-200 pt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Descrizione</h3>
                <div class="prose prose-sm text-gray-700">
                    {!! $prodotto->descrizione !!}
                </div>
            </div>

            <!-- Selezione B2C -->
            <div class="mt-8">
                <!-- MESSAGGI DI ERRORE O SUCCESSO -->
                @if(session('success'))
                    <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded text-sm font-bold border border-green-200 shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded text-sm font-bold border border-red-200 shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('public.shop.cart.add') }}" method="POST" data-loading-type="cart" class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    @csrf
                    <input type="hidden" name="variant_id" :value="selectedVariantId">
                    
                    @if($prodotto->variants->count() > 0)
                        
                        <!-- Selezione Colore -->
                        <template x-if="availableColors().length > 0">
                            <div class="mb-6">
                                <h3 class="text-sm text-gray-900 font-medium whitespace-nowrap mb-3" id="color-choice-heading">Colore: <span x-text="selectedColor"></span></h3>
                                <div class="flex items-center space-x-3">
                                    <template x-for="color in availableColors()" :key="color">
                                        <button type="button" 
                                                @click="selectColor(color)"
                                                class="px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                                                :class="selectedColor === color ? 'bg-indigo-600 text-white border-transparent shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'">
                                            <span x-text="color"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Selezione Taglia -->
                        <template x-if="availableSizes().length > 0">
                            <div class="mb-6">
                                <h3 class="text-sm text-gray-900 font-medium whitespace-nowrap mb-3" id="size-choice-heading">Taglia: <span x-text="selectedSize"></span></h3>
                                <div class="grid grid-cols-4 gap-3 sm:grid-cols-6 lg:grid-cols-4">
                                    <template x-for="size in availableSizes()" :key="size">
                                        <button type="button" 
                                                @click="selectSize(size)"
                                                class="px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                                                :class="selectedSize === size ? 'bg-indigo-600 text-white border-transparent shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'">
                                            <span x-text="size"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Messaggio Mancanza Varianti -->
                        <div x-show="selectedVariantId === null && (selectedColor !== null || selectedSize !== null)" class="mt-2 text-sm text-red-600 mb-4 h-5">
                            La combinazione selezionata non è disponibile.
                        </div>

                        <!-- Quantità -->
                        <div class="mb-6 flex items-center mt-6">
                            <h3 class="text-sm text-gray-900 font-medium mr-4">Quantità:</h3>
                            <input type="number" name="quantita" min="1" :max="currentVariant ? currentVariant.quantita : 1" x-model="quantity" class="w-20 text-center border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            
                            <!-- Check Disponibilità stock -->
                            <template x-if="currentVariant">
                                <span class="ml-4 text-sm" :class="(infiniteStock || (currentVariant && currentVariant.quantita > 0)) ? 'text-green-600' : 'text-red-600'">
                                    <template x-if="infiniteStock">
                                        <span>Disponibile</span>
                                    </template>
                                    <template x-if="!infiniteStock">
                                        <span>
                                            <span x-show="currentVariant && currentVariant.quantita > 0">
                                                Disponibile (<span x-text="currentVariant.quantita"></span>)
                                            </span>
                                            <span x-show="!currentVariant || currentVariant.quantita <= 0">Esaurito</span>
                                        </span>
                                    </template>
                                </span>
                            </template>
                        </div>

                    @else
                        <div class="text-yellow-600 bg-yellow-50 p-4 rounded border border-yellow-200">
                            Prodotto non disponibile al momento.
                        </div>
                    @endif

                    <div class="mt-6 flex">
                        <button type="submit" 
                                :disabled="selectedVariantId === null || (!infiniteStock && currentVariant && currentVariant.quantita <= 0)"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center justify-center transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span x-text="(infiniteStock || (currentVariant && currentVariant.quantita > 0)) ? 'Aggiungi al Carrello' : 'Prodotto Esaurito'"></span>
                        </button>
                    </div>
                </form>
            </div>
            
            @if($prodotto->tags->count() > 0)
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-medium text-gray-900">Tag Profilazione</h3>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($prodotto->tags as $tag)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $tag->nome }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productB2C', (variantsRaw, infiniteStock = false) => ({
            variants: variantsRaw,
            infiniteStock: infiniteStock,
            selectedColor: null,
            selectedSize: null,
            selectedVariantId: null,
            quantity: 1,

            init() {
                // Auto-seleziona se ci sono varianti disponibili
                if (this.availableColors().length > 0) {
                    this.selectedColor = this.availableColors()[0];
                }
                if (this.availableSizes().length > 0) {
                    this.selectedSize = this.availableSizes()[0];
                }
                this.doUpdateVariant();
                
                // Watch per agganciare l'immagine della variante se l'utente clicca una thumb
                this.$watch('selectedVariantId', (val) => {
                    const variant = this.currentVariant;
                    if (variant && variant.foto) {
                        // Se la variante ha una foto specifica e c'è una galleria attiva
                        const galleryApp = document.querySelector('[x-data="{ currentImage: 0"]').__x.$data;
                        if(galleryApp) {
                            // Cerca se la foto della variante è già nelle immagini passate, altrimenti l'aggiunge/la mostra
                            let idx = galleryApp.images.findIndex(img => img.includes(variant.foto) || variant.foto.includes(img));
                            if (idx !== -1) {
                                galleryApp.currentImage = idx;
                            } else {
                                galleryApp.images.push(variant.foto);
                                galleryApp.currentImage = galleryApp.images.length - 1;
                            }
                        }
                    }
                });
            },

            updateVariant() {
                // Ensure array data types for proper method usages by updating available colors / sizes first (fixed AlpineJS bug from the old getter issue)
            },
            
            availableColors() {
                const colors = new Set();
                this.variants.forEach(v => {
                    if(v.colore) colors.add(v.colore);
                });
                return Array.from(colors);
            },

            availableSizes() {
                const sizes = new Set();
                this.variants.forEach(v => {
                    if (this.selectedColor) {
                        if (v.colore === this.selectedColor && v.taglia) sizes.add(v.taglia);
                    } else {
                        if (v.taglia) sizes.add(v.taglia);
                    }
                });
                return Array.from(sizes);
            },

            selectColor(col) {
                this.selectedColor = col;
                if (this.selectedSize && !this.availableSizes().includes(this.selectedSize)) {
                    this.selectedSize = this.availableSizes().length > 0 ? this.availableSizes()[0] : null;
                }
                this.doUpdateVariant();
            },

            selectSize(sz) {
                this.selectedSize = sz;
                this.doUpdateVariant();
            },

            doUpdateVariant() {
                let match = this.variants.find(v => {
                    let cMatch = !this.selectedColor || v.colore === this.selectedColor;
                    let sMatch = !this.selectedSize || v.taglia === this.selectedSize;
                    return cMatch && sMatch;
                });
                
                // Fallback: se nessuna selezione ma ci sono varianti generiche
                if (!match && !this.selectedColor && !this.selectedSize && this.variants.length > 0) {
                    match = this.variants[0];
                }

                if (match) {
                    this.selectedVariantId = match.id;
                } else {
                    this.selectedVariantId = null;
                }
            },

            get currentVariant() {
                if (this.selectedVariantId) {
                    return this.variants.find(v => v.id === this.selectedVariantId);
                }
                return this.variants.length > 0 ? this.variants[0] : null;
            },

            get formattedPrice() {
                const v = this.currentVariant;
                if (!v) return '€ 0,00';
                
                let baseHtml = '';
                if (parseFloat(v.prezzo_scontato) > 0) {
                    baseHtml = `<span class="text-red-600 block">€ ${parseFloat(v.prezzo_scontato).toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                <span class="text-lg line-through text-gray-400 block font-medium mt-1">€ ${parseFloat(v.prezzo).toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
                } else {
                    baseHtml = `<span>€ ${parseFloat(v.prezzo).toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
                }
                return baseHtml;
            }
        }));
    });
</script>
@endpush
@endsection
