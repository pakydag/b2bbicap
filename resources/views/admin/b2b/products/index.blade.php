<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inventario Prodotti B2B') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Sezioni Sincronizzazione Dati -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Sezione Importazione Google Sheet -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="p-2 bg-green-50 text-green-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            <h3 class="text-lg font-bold text-gray-800">Prodotti da Google Sheet</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">
                            Importa o aggiorna l'inventario prodotti B2B dal foglio Google. Vengono letti ed importati solo i prodotti contrassegnati come <strong>"pronta consegna"</strong> in Colonna C.
                        </p>
                    </div>
                    <form action="{{ route('admin.b2b.products.import') }}" method="POST" class="flex flex-col gap-3 mt-2">
                        @csrf
                        <div>
                            <label for="url" class="block text-xs font-bold text-gray-700 uppercase mb-1">URL Esportazione CSV Google Sheet</label>
                            <input type="url" name="url" id="url" 
                                   value="https://docs.google.com/spreadsheets/d/11HQN1nTtHUPt29p9ZFGH5jHaSk90Ltc19RGlNDis5Dw/export?format=csv&gid=1019847442" 
                                   required 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.283 8H18"></path></svg>
                            Sincronizza Prodotti
                        </button>
                    </form>
                </div>

                <!-- Sezione Sincronizzazione Giacenze FTPS -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>
                            </span>
                            <h3 class="text-lg font-bold text-gray-800">Giacenze & Taglie da Server FTPS</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">
                            Scarica immediatamente l'ultimo file <strong>Giacenza.csv</strong> dal server remoto (<code>51.75.145.169 / Output</code>) per aggiornare le giacenze e le date di consegna degli agenti.
                        </p>
                    </div>
                    <form action="{{ route('admin.b2b.products.sync_giacenze') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Aggiorna File Giacenze FTPS Ora
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(!empty($selectedBrand))
                        <div class="mb-6 bg-amber-50 border-2 border-amber-300 text-amber-900 p-4 rounded-xl flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">🏷️</span>
                                <span class="text-xs font-bold uppercase tracking-wider">Filtro Linea Attivo: <strong class="font-black text-amber-950 text-sm ml-1">{{ $selectedBrand->name }}</strong> ({{ $products->count() }} Prodotti Trovati)</span>
                            </div>
                            <a href="{{ route('admin.b2b.products.index') }}" class="text-xs font-black text-amber-900 hover:text-amber-700 bg-amber-200/80 px-3 py-1.5 rounded-lg uppercase tracking-wider">✕ Mostra Tutte le Linee</a>
                        </div>
                    @endif

                    <!-- Form di Ricerca -->
                    <div class="mb-6">
                        <form action="{{ route('admin.b2b.products.index') }}" method="GET" class="flex gap-2">
                            <div class="relative flex-1 max-w-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cerca per nome prodotto o linea..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 active:bg-slate-900 focus:outline-none focus:border-slate-900 focus:ring ring-slate-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cerca
                            </button>
                            @if(!empty($search))
                                <a href="{{ route('admin.b2b.products.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150">
                                    Annulla
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- Mobile View -->
                    <div class="md:hidden space-y-4">
                        @forelse($products as $product)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm relative overflow-hidden">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-white p-1 border border-gray-200 flex items-center justify-center shrink-0 overflow-hidden">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain" onerror="this.onerror=null; this.src='{{ asset('storage/logo/logo-bicap.png') }}';">
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-900 leading-tight">{{ $product->name }}</h3>
                                            <p class="text-xs text-indigo-600 font-semibold">{{ $product->brand->name }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-gray-100">
                                    <div class="text-xs">
                                        <p class="text-gray-400">Stagione</p>
                                        <p class="font-medium text-gray-700">{{ $product->season ?? '-' }}</p>
                                    </div>
                                    <div class="text-xs text-right">
                                        <p class="text-gray-400">Prezzo</p>
                                        <p class="font-bold text-gray-900 text-sm">€ {{ number_format($product->price, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                                    @if($product->has_stock)
                                        <div class="bg-indigo-50 px-2 py-1 rounded">
                                            <span class="text-indigo-700 font-bold">Magazzino: {{ $product->variants->sum('stock') }} pz</span>
                                        </div>
                                    @else
                                        <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-[10px] font-bold">SENZA MAGAZZINO</span>
                                    @endif
                                    <a href="{{ route('admin.b2b.products.show', $product) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-600 text-white font-bold rounded-lg text-xs hover:bg-indigo-700 transition">
                                        <span>👁️ DETTAGLIO</span>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-sm text-gray-500">{{ !empty($search) ? 'Nessun prodotto trovato.' : 'Nessun prodotto in inventario.' }}</p>
                        @endforelse
                    </div>

                    <!-- Desktop View -->
                    <div class="hidden md:flex flex-col overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codice</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodotto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Linea</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stagione</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prezzo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Varianti / Stock</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($products as $product)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="{{ route('admin.b2b.products.show', $product) }}">
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-14 h-14 object-contain rounded-lg bg-gray-50 border border-gray-200 p-1 hover:border-indigo-400 transition" onerror="this.onerror=null; this.src='{{ asset('storage/logo/logo-bicap.png') }}';">
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-600">
                                            {{ $product->code ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('admin.b2b.products.show', $product) }}" class="group">
                                                <div class="font-black text-slate-900 group-hover:text-indigo-600 transition">{{ $product->name }}</div>
                                                @if(!empty($product->characteristics['CAT-SICUREZZA']))
                                                    <span class="inline-block bg-slate-100 text-slate-700 text-[10px] px-1.5 py-0.5 rounded font-mono font-bold mt-0.5">{{ $product->characteristics['CAT-SICUREZZA'] }}</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->brand->name ?? 'N.D.' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->season ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">€ {{ number_format($product->price, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @if($product->has_stock)
                                                <div class="space-y-1">
                                                    @php $totalStock = $product->variants->sum('stock'); @endphp
                                                    <span class="font-bold text-indigo-600">Tot: {{ $totalStock }} pz</span>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $product->variants->count() }} varianti registrate
                                                    </div>
                                                </div>
                                            @else
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Senza Magazzino</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.b2b.products.show', $product) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg text-xs uppercase tracking-wider shadow transition">
                                                <span>👁️ DETTAGLIO</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ !empty($search) ? 'Nessun prodotto corrisponde alla ricerca.' : 'Nessun prodotto in inventario.' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
