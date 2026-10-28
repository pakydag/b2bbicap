@extends('public.layouts.main')

@section('title', 'BICAP Official SHOP | Scarpe Antinfortunistiche Made in Italy')

@section('content')
<div class="bg-white text-black min-h-screen" x-data="bicapShopCatalog(@js($formattedProducts), @js($filterOptions))">
    
    <!-- Mobile Backdrop Overlay (only for < 1024px) -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 z-[99990] backdrop-blur-[1px] lg:hidden"
         @click="sidebarOpen = false"
         style="display: none;"></div>

    <div class="w-full flex items-start">
        
        <!-- ================= FILTER SIDEBAR (Left Column on Desktop, Offcanvas on Mobile) ================= -->
        <aside class="bicap-sidebar py-3 select-none bg-white shrink-0 border-r border-[#dedede]"
               x-show="sidebarOpen"
               :class="sidebarOpen ? 'is-open' : 'is-closed'"
               @keydown.window.escape="sidebarOpen = false">
                
            <!-- Header with ✖ close button (bicap.shop style) -->
            <div class="flex items-center justify-end px-3 pb-2 mb-1 border-b border-[#dedede]">
                <button type="button" @click="sidebarOpen = false" class="text-black hover:text-red-600 font-bold text-xl transition-colors cursor-pointer leading-none p-1" title="Chiudi filtri">
                    ✕
                </button>
            </div>

            <!-- 1. SETTORI Accordion -->
            <div class="border-b border-[#dedede]">
                <button @click="accordions.settori = !accordions.settori" class="bicap-filter-btn">
                    <span>SETTORI</span>
                    <span class="bicap-filter-icon" x-text="accordions.settori ? '−' : '+'"></span>
                </button>
                <div x-show="accordions.settori" x-collapse class="bicap-filter-content space-y-1 max-h-60 overflow-y-auto">
                    <template x-for="item in filtersData.settori" :key="item">
                        <label class="bicap-filter-label">
                            <input type="checkbox" :value="item" x-model="selected.settori" class="w-3.5 h-3.5 rounded-none border-gray-300 text-black focus:ring-0">
                            <span x-text="item"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- 2. TIPOLOGIA CALZATURA Accordion -->
            <div class="border-b border-[#dedede]">
                <button @click="accordions.tipologia = !accordions.tipologia" class="bicap-filter-btn">
                    <span>TIPOLOGIA CALZATURA</span>
                    <span class="bicap-filter-icon" x-text="accordions.tipologia ? '−' : '+'"></span>
                </button>
                <div x-show="accordions.tipologia" x-collapse class="bicap-filter-content space-y-1">
                    <template x-for="item in filtersData.tipologie" :key="item">
                        <label class="bicap-filter-label">
                            <input type="checkbox" :value="item" x-model="selected.tipologie" class="w-3.5 h-3.5 rounded-none border-gray-300 text-black focus:ring-0">
                            <span x-text="item"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- 3. CATEGORIA DI SICUREZZA Accordion -->
            <div class="border-b border-[#dedede]">
                <button @click="accordions.cat_sicurezza = !accordions.cat_sicurezza" class="bicap-filter-btn">
                    <span>CATEGORIA DI SICUREZZA</span>
                    <span class="bicap-filter-icon" x-text="accordions.cat_sicurezza ? '−' : '+'"></span>
                </button>
                <div x-show="accordions.cat_sicurezza" x-collapse class="bicap-filter-content space-y-1">
                    <template x-for="item in filtersData.categorie" :key="item">
                        <label class="bicap-filter-label">
                            <input type="checkbox" :value="item" x-model="selected.categorie" class="w-3.5 h-3.5 rounded-none border-gray-300 text-black focus:ring-0">
                            <span x-text="item"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- 4. MATERIALE Accordion -->
            <div class="border-b border-[#dedede]">
                <button @click="accordions.materiale = !accordions.materiale" class="bicap-filter-btn">
                    <span>MATERIALE</span>
                    <span class="bicap-filter-icon" x-text="accordions.materiale ? '−' : '+'"></span>
                </button>
                <div x-show="accordions.materiale" x-collapse class="bicap-filter-content space-y-1">
                    <template x-for="item in filtersData.materiali" :key="item">
                        <label class="bicap-filter-label">
                            <input type="checkbox" :value="item" x-model="selected.materiali" class="w-3.5 h-3.5 rounded-none border-gray-300 text-black focus:ring-0">
                            <span x-text="item"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- 5. PUNTALE Accordion -->
            <div class="border-b border-[#dedede]">
                <button @click="accordions.puntale = !accordions.puntale" class="bicap-filter-btn">
                    <span>PUNTALE</span>
                    <span class="bicap-filter-icon" x-text="accordions.puntale ? '−' : '+'"></span>
                </button>
                <div x-show="accordions.puntale" x-collapse class="bicap-filter-content space-y-1">
                    <template x-for="item in filtersData.puntali" :key="item">
                        <label class="bicap-filter-label">
                            <input type="checkbox" :value="item" x-model="selected.puntali" class="w-3.5 h-3.5 rounded-none border-gray-300 text-black focus:ring-0">
                            <span x-text="item"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- 6. SUOLA Accordion -->
            <div class="border-b border-[#dedede]">
                <button @click="accordions.suola = !accordions.suola" class="bicap-filter-btn">
                    <span>SUOLA</span>
                    <span class="bicap-filter-icon" x-text="accordions.suola ? '−' : '+'"></span>
                </button>
                <div x-show="accordions.suola" x-collapse class="bicap-filter-content space-y-1">
                    <template x-for="item in filtersData.suole" :key="item">
                        <label class="bicap-filter-label">
                            <input type="checkbox" :value="item" x-model="selected.suole" class="w-3.5 h-3.5 rounded-none border-gray-300 text-black focus:ring-0">
                            <span x-text="item"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- 7. COLLEZIONE Accordion (OPEN BY DEFAULT - Identical to bicap.shop) -->
            <div class="border-b border-[#dedede]">
                <button @click="accordions.collezione = !accordions.collezione" class="bicap-filter-btn">
                    <span>COLLEZIONE</span>
                    <span class="bicap-filter-icon" x-text="accordions.collezione ? '−' : '+'"></span>
                </button>
                <div x-show="accordions.collezione" class="bicap-filter-content space-y-1">
                    <template x-for="item in filtersData.collezioni" :key="item">
                        <label class="bicap-filter-label">
                            <input type="checkbox" :value="item" x-model="selected.collezioni" class="w-3.5 h-3.5 rounded-none border-gray-300 text-black focus:ring-0">
                            <span x-text="item"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- 8. CALZATA Accordion -->
            <div class="border-b border-[#dedede]">
                <button @click="accordions.calzata = !accordions.calzata" class="bicap-filter-btn">
                    <span>CALZATA</span>
                    <span class="bicap-filter-icon" x-text="accordions.calzata ? '−' : '+'"></span>
                </button>
                <div x-show="accordions.calzata" x-collapse class="bicap-filter-content space-y-1">
                    <template x-for="item in filtersData.calzate" :key="item">
                        <label class="bicap-filter-label">
                            <input type="checkbox" :value="item" x-model="selected.calzate" class="w-3.5 h-3.5 rounded-none border-gray-300 text-black focus:ring-0">
                            <span x-text="item"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- 9. NORMA Accordion -->
            <div class="border-b border-[#dedede]">
                <button @click="accordions.norma = !accordions.norma" class="bicap-filter-btn">
                    <span>NORMA</span>
                    <span class="bicap-filter-icon" x-text="accordions.norma ? '−' : '+'"></span>
                </button>
                <div x-show="accordions.norma" x-collapse class="bicap-filter-content space-y-1">
                    <template x-for="item in filtersData.norme" :key="item">
                        <label class="bicap-filter-label">
                            <input type="checkbox" :value="item" x-model="selected.norme" class="w-3.5 h-3.5 rounded-none border-gray-300 text-black focus:ring-0">
                            <span x-text="item"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- Reset All Filters if Active -->
            <div x-show="activeFilterCount() > 0" class="p-4">
                <button @click="resetFilters()" class="w-full py-2 border border-black text-black font-bold uppercase text-xs hover:bg-black hover:text-white transition">
                    Resetta Filtri
                </button>
            </div>
        </aside>

        <!-- ================= MAIN PRODUCTS AREA ================= -->
        <div class="flex-1 px-4 sm:px-6 lg:px-10 py-6 min-w-0">

            <!-- Top Action Bar: FILTRO + (Visible only when sidebar is closed) & Active Chips -->
            <div class="flex flex-wrap items-center gap-4 mb-6" x-show="!sidebarOpen || activeFilterCount() > 0">
                <!-- FILTRO + Yellow Button (Identical to bicap.shop) -->
                <button x-show="!sidebarOpen"
                        @click="sidebarOpen = true" 
                        style="background-color: #fedb00 !important; color: #000000 !important; font-family: 'Bebas Neue', sans-serif !important;"
                        class="inline-flex items-center justify-center px-6 py-1.5 text-[20px] font-bold tracking-wider uppercase cursor-pointer hover:opacity-90 active:scale-95 shadow-xs transition"
                        title="Apri Filtri">
                    FILTRO +
                </button>

                <!-- Active Filter Chips -->
                <div class="flex flex-wrap items-center gap-2">
                    <template x-for="(val, idx) in activeFilterList()" :key="idx">
                        <span class="inline-flex items-center gap-1.5 bg-zinc-100 hover:bg-zinc-200 text-black px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider transition">
                            <span x-text="val.label"></span>
                            <button type="button" @click="removeFilter(val.type, val.value)" class="text-zinc-400 hover:text-black">
                                &times;
                            </button>
                        </span>
                    </template>

                    <button x-show="activeFilterCount() > 0" 
                            @click="resetFilters()" 
                            class="text-xs text-red-600 font-bold uppercase tracking-wider hover:underline ml-2">
                        Cancella tutti
                    </button>
                </div>
            </div>

            <!-- ================= 3-COLUMN LARGE PRODUCTS CATALOG GRID ================= -->
            <main class="w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-16 gap-x-6 xl:gap-x-12">
                    
                    <template x-for="product in filteredProducts" :key="product.id">
                        <article class="group flex flex-col items-center text-center cursor-pointer">
                            <a :href="product.url" class="block w-full">
                                
                                <!-- Large Product Shoe Image Container (Identical proportions to bicap.shop) -->
                                <div class="w-full max-w-[440px] h-[300px] sm:h-[340px] lg:h-[360px] mx-auto flex items-center justify-center p-3 relative overflow-hidden bg-white">
                                    <img :src="product.image" 
                                         :alt="product.name" 
                                         class="max-h-full max-w-full object-contain transition-transform duration-300 ease-out group-hover:scale-105"
                                         loading="lazy">
                                </div>

                                <!-- Product Title (Bebas Neue 28px bold uppercase - Exactly bicap.shop) -->
                                <h3 class="bicap-product-title font-['Bebas_Neue',sans-serif] text-[26px] sm:text-[30px] font-bold tracking-[1px] uppercase text-black mt-4 mb-1 text-center leading-none group-hover:text-amber-500 transition-colors"
                                    x-text="product.name">
                                </h3>

                                <!-- Product Subtitle (Code + Security Standard - Montserrat 13px regular uppercase) -->
                                <p class="bicap-product-subtitle text-[13px] font-normal uppercase text-[#555555] tracking-[0.5px] text-center leading-normal"
                                   x-text="product.subtitle">
                                </p>
                            </a>
                        </article>
                    </template>

                </div>

                <!-- Empty State -->
                <div x-show="filteredProducts.length === 0" class="py-20 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-zinc-100 mb-4 text-zinc-400 text-2xl">
                        👟
                    </div>
                    <h3 class="text-base font-black uppercase tracking-tight text-zinc-800">
                        Nessun prodotto trovato
                    </h3>
                    <p class="text-xs text-zinc-500 mt-1 max-w-md mx-auto">
                        Nessuna scarpa corrisponde a tutti i filtri selezionati. Prova a rimuovere qualche filtro per visualizzare altri modelli.
                    </p>
                    <button @click="resetFilters()" style="background-color: #fedb00 !important; color: #000000 !important;" class="mt-4 inline-flex items-center gap-2 font-black uppercase text-xs px-4 py-2 rounded-lg">
                        Reimposta Filtri
                    </button>
                </div>
            </main>

        </div>
    </div>
</div>

@push('scripts')
<script>
function bicapShopCatalog(allProducts, filterOptions) {
    return {
        products: allProducts,
        filtersData: filterOptions,
        sidebarOpen: (window.innerWidth >= 1024),

        // Accordion states (COLLEZIONE open by default like bicap.shop)
        accordions: {
            settori: false,
            tipologia: false,
            cat_sicurezza: false,
            materiale: false,
            puntale: false,
            suola: false,
            collezione: true,
            calzata: false,
            norma: false,
        },

        // Selected filter values
        selected: {
            settori: [],
            tipologie: [],
            categorie: [],
            materiali: [],
            puntali: [],
            suole: [],
            collezioni: [],
            calzate: [],
            norme: [],
        },

        // Filtered products list
        get filteredProducts() {
            return this.products.filter(p => {
                // Collezioni (Brand)
                if (this.selected.collezioni.length > 0 && !this.selected.collezioni.includes(p.brand)) {
                    return false;
                }
                // Tipologia
                if (this.selected.tipologie.length > 0 && !this.selected.tipologie.includes(p.tipologia)) {
                    return false;
                }
                // Categoria di sicurezza
                if (this.selected.categorie.length > 0 && !this.selected.categorie.includes(p.categoria)) {
                    return false;
                }
                // Materiale
                if (this.selected.materiali.length > 0 && !this.selected.materiali.includes(p.materiale)) {
                    return false;
                }
                // Puntale
                if (this.selected.puntali.length > 0 && !this.selected.puntali.includes(p.puntale)) {
                    return false;
                }
                // Suola
                if (this.selected.suole.length > 0 && !this.selected.suole.includes(p.suola)) {
                    return false;
                }
                // Calzata
                if (this.selected.calzate.length > 0 && !this.selected.calzate.includes(p.calzata)) {
                    return false;
                }
                // Norma
                if (this.selected.norme.length > 0 && !this.selected.norme.includes(p.norma)) {
                    return false;
                }
                // Settori (any of selected sectors)
                if (this.selected.settori.length > 0) {
                    const hasSector = this.selected.settori.some(s => p.settori.includes(s));
                    if (!hasSector) return false;
                }
                return true;
            });
        },

        activeFilterCount() {
            return Object.values(this.selected).reduce((acc, arr) => acc + arr.length, 0);
        },

        activeFilterList() {
            const list = [];
            for (const [type, values] of Object.entries(this.selected)) {
                for (const val of values) {
                    list.push({ type, value: val, label: val });
                }
            }
            return list;
        },

        removeFilter(type, value) {
            if (this.selected[type]) {
                this.selected[type] = this.selected[type].filter(v => v !== value);
            }
        },

        resetFilters() {
            this.selected = {
                settori: [],
                tipologie: [],
                categorie: [],
                materiali: [],
                puntali: [],
                suole: [],
                collezioni: [],
                calzate: [],
                norme: [],
            };
        }
    };
}
</script>
@endpush
@endsection
