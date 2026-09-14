<x-agent-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
                {{ __('Catalogo Prodotti authorized') }}
            </h2>

        </div>
    </x-slot>

    <div x-data="{ showFilters: {{ (request()->has('brands') || request()->has('settori') || request()->has('tipologie') || request()->has('categorie') || request()->has('materiali') || request()->has('puntali') || request()->has('suole') || request()->has('calzate') || request()->has('norme')) ? 'true' : 'false' }} }" class="space-y-6">
        
        <!-- Barra di Controllo con Toggle Filtri e Ricerca Veloce -->
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center bg-white p-4 rounded-3xl border border-gray-100 shadow-sm gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <button type="button" @click="showFilters = !showFilters" class="flex items-center gap-2 bg-zinc-50 border border-zinc-200 text-zinc-700 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-zinc-100 hover:text-black hover:border-yellow-400 transition shadow-sm shrink-0">
                    <span>🎛️</span>
                    <span>Filtri</span>
                    <span class="bg-black text-white px-2 py-0.5 rounded text-[10px]" x-text="showFilters ? 'Nascondi' : 'Mostra'"></span>
                </button>

                <!-- Ricerca Veloce per Nome / Codice -->
                <div class="relative flex-1 max-w-xs">
                    <input type="text" name="search" form="filter-form" value="{{ request('search') }}" oninput="clearTimeout(window.searchTimeout); window.searchTimeout = setTimeout(() => this.form.submit(), 500)" placeholder="Cerca per nome o codice..." class="text-xs border-gray-200 rounded-xl pl-8 pr-4 py-2 focus:ring-black focus:border-black w-full">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-xs">🔍</span>
                </div>
            </div>
            
            <div class="text-xs text-gray-400 font-black uppercase tracking-widest text-right shrink-0">
                {{ count($products) }} {{ count($products) == 1 ? 'Prodotto Trovato' : 'Prodotti Trovati' }}
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filtri -->
            <aside x-show="showFilters" x-transition class="w-full lg:w-64 shrink-0">
                <form id="filter-form" action="{{ route('agent.catalog') }}" method="GET" class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-6">
                    <!-- Header Filtri con Reset -->
                    <div class="flex justify-between items-center border-b pb-4">
                        <span class="text-sm font-black uppercase tracking-wider text-slate-900">Filtri</span>
                        <a href="{{ route('agent.catalog') }}" class="text-xs font-black text-red-500 hover:text-red-700 uppercase tracking-widest transition">Resetta</a>
                    </div>

                    <!-- 1. Collezioni / Brand -->
                    @if($authorizedBrands->isNotEmpty())
                    <div x-data="{ open: true }" class="border-b border-gray-50 pb-4">
                        <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-xs font-black uppercase tracking-wider text-gray-700 hover:text-yellow-600 transition">
                            <span>Collezioni</span>
                            <span x-text="open ? '−' : '+'" class="text-yellow-500 text-sm font-bold"></span>
                        </button>
                        <div x-show="open" x-transition class="mt-3 space-y-2 max-h-40 overflow-y-auto pr-1">
                            @foreach($authorizedBrands as $brand)
                                <label class="flex items-center text-[10px] text-gray-600 font-black uppercase cursor-pointer hover:text-yellow-600 transition">
                                    <input type="checkbox" name="brands[]" value="{{ $brand->id }}" onchange="this.form.submit()" {{ in_array($brand->id, request('brands', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500 mr-2.5">
                                    {{ $brand->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- 2. Settori di Utilizzo -->
                    @if(!empty($filterOptions['settori']))
                    <div x-data="{ open: false }" class="border-b border-gray-50 pb-4">
                        <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-xs font-black uppercase tracking-wider text-gray-700 hover:text-yellow-600 transition">
                            <span>Settori</span>
                            <span x-text="open ? '−' : '+'" class="text-yellow-500 text-sm font-bold"></span>
                        </button>
                        <div x-show="open" x-transition class="mt-3 space-y-2 max-h-40 overflow-y-auto pr-1">
                            @foreach($filterOptions['settori'] as $settore)
                                <label class="flex items-center text-[10px] text-gray-600 font-black uppercase cursor-pointer hover:text-yellow-600 transition">
                                    <input type="checkbox" name="settori[]" value="{{ $settore }}" onchange="this.form.submit()" {{ in_array($settore, request('settori', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500 mr-2.5">
                                    {{ $settore }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- 3. Tipologia Calzatura -->
                    @if(!empty($filterOptions['tipologie']))
                    <div x-data="{ open: false }" class="border-b border-gray-50 pb-4">
                        <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-xs font-black uppercase tracking-wider text-gray-700 hover:text-yellow-600 transition">
                            <span>Tipologia</span>
                            <span x-text="open ? '−' : '+'" class="text-yellow-500 text-sm font-bold"></span>
                        </button>
                        <div x-show="open" x-transition class="mt-3 space-y-2 max-h-40 overflow-y-auto pr-1">
                            @foreach($filterOptions['tipologie'] as $tipologia)
                                <label class="flex items-center text-[10px] text-gray-600 font-black uppercase cursor-pointer hover:text-yellow-600 transition">
                                    <input type="checkbox" name="tipologie[]" value="{{ $tipologia }}" onchange="this.form.submit()" {{ in_array($tipologia, request('tipologie', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500 mr-2.5">
                                    {{ $tipologia }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- 4. Categoria di Sicurezza -->
                    @if(!empty($filterOptions['categorie']))
                    <div x-data="{ open: false }" class="border-b border-gray-50 pb-4">
                        <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-xs font-black uppercase tracking-wider text-gray-700 hover:text-yellow-600 transition">
                            <span>Categoria Sicurezza</span>
                            <span x-text="open ? '−' : '+'" class="text-yellow-500 text-sm font-bold"></span>
                        </button>
                        <div x-show="open" x-transition class="mt-3 space-y-2 max-h-40 overflow-y-auto pr-1">
                            @foreach($filterOptions['categorie'] as $categoria)
                                <label class="flex items-center text-[10px] text-gray-600 font-black uppercase cursor-pointer hover:text-yellow-600 transition">
                                    <input type="checkbox" name="categorie[]" value="{{ $categoria }}" onchange="this.form.submit()" {{ in_array($categoria, request('categorie', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500 mr-2.5">
                                    {{ $categoria }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- 5. Materiale -->
                    @if(!empty($filterOptions['materiali']))
                    <div x-data="{ open: false }" class="border-b border-gray-50 pb-4">
                        <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-xs font-black uppercase tracking-wider text-gray-700 hover:text-yellow-600 transition">
                            <span>Materiale</span>
                            <span x-text="open ? '−' : '+'" class="text-yellow-500 text-sm font-bold"></span>
                        </button>
                        <div x-show="open" x-transition class="mt-3 space-y-2 max-h-40 overflow-y-auto pr-1">
                            @foreach($filterOptions['materiali'] as $materiale)
                                <label class="flex items-center text-[10px] text-gray-600 font-black uppercase cursor-pointer hover:text-yellow-600 transition">
                                    <input type="checkbox" name="materiali[]" value="{{ $materiale }}" onchange="this.form.submit()" {{ in_array($materiale, request('materiali', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500 mr-2.5">
                                    {{ $materiale }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- 6. Puntale -->
                    @if(!empty($filterOptions['puntali']))
                    <div x-data="{ open: false }" class="border-b border-gray-50 pb-4">
                        <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-xs font-black uppercase tracking-wider text-gray-700 hover:text-yellow-600 transition">
                            <span>Puntale</span>
                            <span x-text="open ? '−' : '+'" class="text-yellow-500 text-sm font-bold"></span>
                        </button>
                        <div x-show="open" x-transition class="mt-3 space-y-2 max-h-40 overflow-y-auto pr-1">
                            @foreach($filterOptions['puntali'] as $puntale)
                                <label class="flex items-center text-[10px] text-gray-600 font-black uppercase cursor-pointer hover:text-yellow-600 transition">
                                    <input type="checkbox" name="puntali[]" value="{{ $puntale }}" onchange="this.form.submit()" {{ in_array($puntale, request('puntali', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500 mr-2.5">
                                    {{ $puntale }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- 7. Suola -->
                    @if(!empty($filterOptions['suole']))
                    <div x-data="{ open: false }" class="border-b border-gray-50 pb-4">
                        <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-xs font-black uppercase tracking-wider text-gray-700 hover:text-yellow-600 transition">
                            <span>Suola</span>
                            <span x-text="open ? '−' : '+'" class="text-yellow-500 text-sm font-bold"></span>
                        </button>
                        <div x-show="open" x-transition class="mt-3 space-y-2 max-h-40 overflow-y-auto pr-1">
                            @foreach($filterOptions['suole'] as $suola)
                                <label class="flex items-center text-[10px] text-gray-600 font-black uppercase cursor-pointer hover:text-yellow-600 transition">
                                    <input type="checkbox" name="suole[]" value="{{ $suola }}" onchange="this.form.submit()" {{ in_array($suola, request('suole', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500 mr-2.5">
                                    {{ $suola }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- 8. Calzata -->
                    @if(!empty($filterOptions['calzate']))
                    <div x-data="{ open: false }" class="border-b border-gray-50 pb-4">
                        <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-xs font-black uppercase tracking-wider text-gray-700 hover:text-yellow-600 transition">
                            <span>Calzata</span>
                            <span x-text="open ? '−' : '+'" class="text-yellow-500 text-sm font-bold"></span>
                        </button>
                        <div x-show="open" x-transition class="mt-3 space-y-2 max-h-40 overflow-y-auto pr-1">
                            @foreach($filterOptions['calzate'] as $calzata)
                                <label class="flex items-center text-[10px] text-gray-600 font-black uppercase cursor-pointer hover:text-yellow-600 transition">
                                    <input type="checkbox" name="calzate[]" value="{{ $calzata }}" onchange="this.form.submit()" {{ in_array($calzata, request('calzate', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500 mr-2.5">
                                    {{ $calzata }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- 9. Normativa -->
                    @if(!empty($filterOptions['norme']))
                    <div x-data="{ open: false }" class="pb-2">
                        <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-xs font-black uppercase tracking-wider text-gray-700 hover:text-yellow-600 transition">
                            <span>Normativa</span>
                            <span x-text="open ? '−' : '+'" class="text-yellow-500 text-sm font-bold"></span>
                        </button>
                        <div x-show="open" x-transition class="mt-3 space-y-2 max-h-40 overflow-y-auto pr-1">
                            @foreach($filterOptions['norme'] as $norma)
                                <label class="flex items-center text-[10px] text-gray-600 font-black uppercase cursor-pointer hover:text-yellow-600 transition">
                                    <input type="checkbox" name="norme[]" value="{{ $norma }}" onchange="this.form.submit()" {{ in_array($norma, request('norme', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500 mr-2.5">
                                    {{ $norma }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </form>
            </aside>

            <!-- Griglia Prodotti -->
            <div class="flex-1">
                <div :class="showFilters ? 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8' : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8'">
                    @forelse($products as $product)
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-xl hover:shadow-zinc-100 transition duration-500 group">
                            <div class="aspect-square bg-gray-50 flex items-center justify-center p-8 relative">
                                <!-- Badge Marchio -->
                                <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm border border-gray-100 px-3 py-1 rounded-full text-xs font-black uppercase text-gray-600 tracking-wider shadow-sm">
                                    {{ $product->brand->name }}
                                </span>

                                <!-- Badge Sincronizzazione -->
                                @if(isset($product->is_synchronized) && !$product->is_synchronized)
                                    <span class="absolute top-4 right-4 bg-rose-600 text-white px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider shadow-md z-10 animate-pulse">
                                        NON SINCRONIZZATO
                                    </span>
                                @endif

                                @if($product->image)
                                    <img src="{{ \Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="text-6xl opacity-20">👕</div>
                                @endif
                            </div>

                            <div class="p-6 flex-1 flex flex-col titoli">
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">{{ $product->season ?? 'Qualsiasi Stagione' }}</p>
                                <h3 class="text-lg font-black text-gray-900 leading-tight mb-1 uppercase">{{ $product->name }}</h3>

                                <!-- Codice e Categoria di Sicurezza -->
                                <div class="flex flex-wrap items-center gap-1.5 mb-3 text-[10px] font-black uppercase tracking-wider">
                                    @if($product->code)
                                        <span class="bg-zinc-50 text-zinc-700 border border-zinc-200 px-2 py-0.5 rounded-md">{{ $product->code }}</span>
                                    @endif
                                    @if(isset($product->is_synchronized) && !$product->is_synchronized)
                                        <span class="bg-rose-50 text-rose-700 px-2 py-0.5 rounded-md border border-rose-100">NON SINCRONIZZATO</span>
                                    @endif
                                    @if(!empty($product->characteristics['CAT-SICUREZZA']))
                                        <span class="bg-slate-100 text-slate-800 px-2 py-0.5 rounded-md border border-slate-200">{{ $product->characteristics['CAT-SICUREZZA'] }}</span>
                                    @endif
                                </div>

                                <div class="flex-1 text-sm text-gray-500 italic mb-3 line-clamp-2">
                                    {{ $product->description ?? 'Nessuna descrizione disponibile.' }}
                                </div>

                                @if(!empty($product->characteristics['RANGE-TAGLIE-VALORE']))
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2">
                                        Range Taglie: <span class="text-gray-700 font-extrabold">{{ $product->characteristics['RANGE-TAGLIE-VALORE'] }}</span>
                                    </p>
                                @endif

                                <div class="pt-3 border-t border-gray-50 flex justify-between items-center">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($product->variants->pluck('size')->unique() as $size)
                                            <span class="bg-gray-50 text-gray-600 border border-gray-100 px-2 py-0.5 rounded text-[10px] font-bold">{{ $size }}</span>
                                        @endforeach
                                        @if($product->variants->isEmpty())
                                            <span class="text-xs text-rose-400 font-bold uppercase italic">Esaurito</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 pb-6 mt-auto">
                                <div class="flex items-end justify-between">
                                    <div>
                                        @if(isset($product->calculated_price) && $product->calculated_price < $product->price)
                                            <span class="text-xs text-gray-400 line-through font-bold block">
                                                € {{ number_format($product->price, 2, ',', '.') }}
                                            </span>
                                            <p class="text-xl font-black text-indigo-700 leading-none">
                                                € {{ number_format($product->calculated_price, 2, ',', '.') }}
                                            </p>
                                        @elseif(isset($product->calculated_price) && !empty($product->price_details['tier']))
                                            <p class="text-xl font-black text-indigo-700 leading-none">
                                                € {{ number_format($product->calculated_price, 2, ',', '.') }}
                                            </p>
                                        @else
                                            <p class="text-xl font-black text-slate-900 leading-none">
                                                € {{ number_format($product->price, 2, ',', '.') }}
                                            </p>
                                        @endif
                                    </div>
                                    <a href="{{ route('agent.product', $product) }}" class="bg-black border border-zinc-950 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:border-yellow-400 hover:text-yellow-400 shadow-lg shadow-black/10 transition duration-300">
                                        Vedi Dettaglio
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <span class="text-6xl block mb-4">🔍</span>
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Nessun prodotto trovato per i filtri selezionati.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-agent-layout>
