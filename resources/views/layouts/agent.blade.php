<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $favicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
    @endphp

    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset($favicon) }}">
    @endif

    <title>Portale Agente - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        @media (min-width: 768px) {
            .b2b-sidebar-open {
                width: 16rem !important;
            }
            .b2b-sidebar-closed {
                width: 5rem !important;
            }
        }
    </style>
</head>
    <body class="font-sans antialiased overflow-hidden bg-gray-50">
        <div class="flex flex-col h-screen" x-data="{ sidebarOpen: localStorage.getItem('b2b_sidebar_open') === 'true' }">
            
            <!-- Top Header Bar Nero Completo -->
            <header class="bg-black text-white border-b border-zinc-900 h-16 flex items-center justify-between px-6 shrink-0 z-30 shadow-md">
                <a href="{{ route('agent.dashboard') }}" class="flex items-center gap-3 hover:opacity-90 transition shrink-0">
                    @php
                        $logo = \App\Models\Setting::where('key', 'site_logo')->value('value') ?? '';
                    @endphp
                    @if(!empty($logo))
                        <img src="{{ asset($logo) }}" class="h-8 w-auto object-contain" alt="Logo">
                    @else
                        <h1 class="text-xl font-bold tracking-tight uppercase text-yellow-400">B2B Portal</h1>
                    @endif
                    <span class="text-xs font-black uppercase tracking-widest text-zinc-400 border-l border-zinc-800 pl-3 hidden sm:inline">
                        {{ Auth::user()->role === 'customer' ? 'Area Clienti' : 'Area Agenti' }}
                    </span>
                </a>

                <!-- Selettore Cliente Attivo per Agenti / Admin -->
                @if(Auth::user()->role === 'agent' || Auth::user()->role === 'admin')
                    @php
                        $selectedCustId = session('b2b_selected_customer_id');
                        $currentCustomer = $selectedCustId ? \App\Models\B2bCustomer::with('priceList')->find($selectedCustId) : null;
                        $agentCustomers = Auth::user()->role === 'customer'
                            ? collect()
                            : Auth::user()->b2bCustomers()->with('priceList')->orderBy('business_name')->get();
                    @endphp

                    <div x-data="{ 
                            openCustModal: false, 
                            search: '',
                            customers: [
                                @foreach($agentCustomers as $ac)
                                {
                                    id: '{{ $ac->id }}',
                                    code: '{{ addslashes($ac->code ?? '') }}',
                                    name: '{{ addslashes($ac->business_name) }}',
                                    vat: '{{ addslashes($ac->vat_number ?? '') }}',
                                    priceList: '{{ addslashes($ac->priceList?->name ?? 'Listino Base') }}'
                                },
                                @endforeach
                            ],
                            get filtered() {
                                if (!this.search) return this.customers;
                                const s = this.search.toLowerCase();
                                return this.customers.filter(c => 
                                    c.name.toLowerCase().includes(s) || 
                                    c.code.toLowerCase().includes(s) || 
                                    c.vat.toLowerCase().includes(s) ||
                                    c.priceList.toLowerCase().includes(s)
                                );
                            }
                        }" class="flex items-center">

                        @if($currentCustomer)
                            <!-- Badge Cliente Attivo -->
                            <div class="flex items-center gap-2 bg-zinc-900 border border-yellow-400/70 rounded-2xl px-3 py-1.5 shadow-sm">
                                <span class="text-sm">👤</span>
                                <div class="text-left leading-tight">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[9px] text-zinc-400 font-bold uppercase hidden md:inline">Ordinando per:</span>
                                        <span class="text-xs font-black text-yellow-400 uppercase tracking-tight truncate max-w-[120px] sm:max-w-[180px] lg:max-w-[240px]" title="{{ $currentCustomer->business_name }}">
                                            {{ $currentCustomer->business_name }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        @if($currentCustomer->priceList)
                                            <span class="text-[9px] font-black uppercase tracking-wider bg-yellow-400/20 text-yellow-300 border border-yellow-400/40 px-1.5 py-0.2 rounded">
                                                🏷️ {{ $currentCustomer->priceList->name }}
                                            </span>
                                        @else
                                            <span class="text-[9px] font-bold uppercase tracking-wider text-zinc-400">
                                                🏷️ Listino Base
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <button type="button" @click="openCustModal = true" class="ml-1 text-[10px] font-black uppercase text-slate-950 bg-yellow-400 hover:bg-yellow-300 px-2.5 py-1 rounded-xl transition shadow cursor-pointer">
                                    Cambia
                                </button>

                                <form action="{{ route('agent.select_customer') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="b2b_customer_id" value="">
                                    <button type="submit" title="Rimuovi cliente attivo (torna a listino base)" class="text-zinc-500 hover:text-rose-400 p-1 text-xs leading-none transition cursor-pointer">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        @else
                            <!-- Nessun Cliente Selezionato -->
                            <button type="button" @click="openCustModal = true" class="flex items-center gap-2 bg-zinc-900 hover:bg-zinc-800 border border-zinc-700 hover:border-yellow-400 rounded-2xl px-3 sm:px-3.5 py-1.5 sm:py-2 text-xs font-bold text-zinc-300 hover:text-yellow-400 transition cursor-pointer shadow-sm group">
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                <span class="font-black uppercase tracking-wider text-[10px] sm:text-[11px]">👤 Seleziona Cliente</span>
                                <span class="text-[10px] text-zinc-500 group-hover:text-yellow-400">▼</span>
                            </button>
                        @endif

                        <!-- Modal / Dropdown Ricerca e Selezione Cliente -->
                        <div x-show="openCustModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm" @click.self="openCustModal = false" @keydown.escape.window="openCustModal = false">
                            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-lg overflow-hidden text-slate-900">
                                <div class="bg-black text-white px-6 py-5 flex items-center justify-between">
                                    <div>
                                        <h3 class="font-black uppercase text-sm tracking-wide text-yellow-400">Seleziona Cliente per l'Ordine</h3>
                                        <p class="text-xs text-zinc-400 mt-0.5">I prezzi, sconti e scaglioni del catalogo e carrello si adatteranno al suo listino.</p>
                                    </div>
                                    <button type="button" @click="openCustModal = false" class="text-zinc-400 hover:text-white text-2xl font-bold cursor-pointer leading-none">&times;</button>
                                </div>

                                <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                                    <div class="relative">
                                        <input type="text" x-model="search" x-ref="searchInput" x-init="$watch('openCustModal', value => { if(value) setTimeout(() => $refs.searchInput.focus(), 100) })" placeholder="Cerca azienda per nome, codice o P.IVA..." class="w-full bg-white border border-gray-300 rounded-2xl px-4 py-3 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 pl-10 shadow-sm">
                                        <span class="absolute left-3.5 top-3.5 text-xs text-gray-400">🔍</span>
                                    </div>
                                </div>

                                <div class="max-h-80 overflow-y-auto divide-y divide-gray-50 p-2">
                                    <template x-for="c in filtered" :key="c.id">
                                        <form action="{{ route('agent.select_customer') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="b2b_customer_id" :value="c.id">
                                            <button type="submit" class="w-full text-left p-3.5 rounded-2xl hover:bg-amber-50/70 border border-transparent hover:border-amber-200 transition flex items-center justify-between group cursor-pointer">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <template x-if="c.code">
                                                            <span class="text-[9px] font-mono font-bold bg-zinc-100 text-zinc-800 px-1.5 py-0.5 rounded border border-zinc-200" x-text="c.code"></span>
                                                        </template>
                                                        <p class="font-black text-slate-900 text-xs uppercase group-hover:text-amber-900" x-text="c.name"></p>
                                                    </div>
                                                    <div class="flex items-center gap-3 mt-1 text-[10px] text-gray-500 font-semibold">
                                                        <span x-text="'P.IVA: ' + (c.vat || 'N/D')"></span>
                                                        <span class="text-amber-700 font-bold" x-text="'• Listino: ' + c.priceList"></span>
                                                    </div>
                                                </div>
                                                <span class="bg-black group-hover:bg-yellow-400 text-white group-hover:text-slate-950 text-[10px] font-black uppercase px-3 py-1.5 rounded-xl transition shadow-sm">
                                                    Attiva →
                                                </span>
                                            </button>
                                        </form>
                                    </template>
                                    <div x-show="filtered.length === 0" class="p-8 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Nessun cliente trovato.
                                    </div>
                                </div>

                                @if($currentCustomer)
                                <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                                    <span class="text-xs text-gray-500 font-medium">Vuoi azzerare il cliente attivo?</span>
                                    <form action="{{ route('agent.select_customer') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="b2b_customer_id" value="">
                                        <button type="submit" class="text-xs font-black uppercase text-rose-600 hover:text-rose-800 cursor-pointer">
                                            Rimuovi Selezione (Listino Base)
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex items-center gap-4">
                    <span class="bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs px-3 py-1.5 rounded-xl font-bold uppercase hidden lg:flex items-center gap-2">
                        👤 {{ Auth::user()->name }}
                    </span>
                    
                    <a href="{{ route('agent.profile') }}" class="text-xs font-black text-yellow-400 hover:text-slate-950 bg-zinc-900 hover:bg-yellow-400 border border-zinc-800 hover:border-yellow-400 px-3.5 py-1.5 rounded-xl transition duration-300 uppercase shadow-sm hidden md:block">
                        Profilo
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-black text-rose-400 hover:text-white bg-rose-950/40 hover:bg-rose-900 border border-rose-900/50 px-3 py-1.5 rounded-xl transition duration-200 uppercase">
                            🚪 Esci
                        </button>
                    </form>
                </div>
            </header>

            <div class="flex flex-1 overflow-hidden min-w-0">
                
                <!-- Sidebar Chiara -->
                <aside class="flex-shrink-0 bg-white text-gray-800 hidden md:flex flex-col h-full border-r border-gray-100 shadow-sm transition-all duration-300" :class="sidebarOpen ? 'w-64' : 'w-20'">
                    
                    <div class="flex items-center justify-end p-4 h-14 shrink-0 border-b border-gray-50">
                        <button type="button" @click="sidebarOpen = !sidebarOpen; localStorage.setItem('b2b_sidebar_open', sidebarOpen)" class="text-gray-400 hover:text-gray-900 p-1.5 hover:bg-gray-100 rounded-lg transition duration-200" title="Espandi/Comprimi Menu">
                            <span x-text="sidebarOpen ? '◀' : '▶'"></span>
                        </button>
                    </div>
                    
                    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2" aria-label="Sidebar">
                        @php 
                            $activeClass = 'bg-yellow-400 text-slate-950 font-black shadow-lg shadow-yellow-400/20 rounded-xl';
                            $inactiveClass = 'text-gray-600 hover:bg-gray-100/80 hover:text-slate-950 font-bold rounded-xl';
                        @endphp
                        
                        <a href="{{ route('agent.dashboard') }}" class="flex items-center px-3 py-2.5 transition-all duration-200 {{ request()->routeIs('agent.dashboard') ? $activeClass : $inactiveClass }}" :class="sidebarOpen ? '' : 'justify-center'" title="Dashboard">
                            <span class="text-base" :class="sidebarOpen ? 'mr-3' : ''">📊</span> 
                            <span x-show="sidebarOpen" x-transition class="text-sm">Dashboard</span>
                        </a>
                        <a href="{{ route('agent.catalog') }}" class="flex items-center px-3 py-2.5 transition-all duration-200 {{ request()->routeIs('agent.catalog') || request()->routeIs('agent.product') ? $activeClass : $inactiveClass }}" :class="sidebarOpen ? '' : 'justify-center'" title="Catalogo Prodotti">
                            <span class="text-base" :class="sidebarOpen ? 'mr-3' : ''">📦</span> 
                            <span x-show="sidebarOpen" x-transition class="text-sm">Catalogo Prodotti</span>
                        </a>
                        <a href="{{ route('agent.cart') }}" class="flex items-center px-3 py-2.5 transition-all duration-200 {{ request()->routeIs('agent.cart') ? $activeClass : $inactiveClass }}" :class="sidebarOpen ? '' : 'justify-center relative'" title="Carrello">
                            <span class="text-base" :class="sidebarOpen ? 'mr-3' : ''">🛒</span> 
                            <span x-show="sidebarOpen" x-transition class="text-sm">Carrello</span>
                            @if(count(session('b2b_cart', [])) > 0)
                                <span :class="sidebarOpen ? 'ml-auto bg-rose-600 text-white text-[10px] px-2 py-0.5 rounded-full font-black shadow-sm' : 'absolute -top-1 -right-1 bg-rose-600 text-white text-[8px] px-1.5 py-0.5 rounded-full font-black shadow-sm'">{{ count(session('b2b_cart')) }}</span>
                            @endif
                        </a>
                        <a href="{{ route('agent.orders') }}" class="flex items-center px-3 py-2.5 transition-all duration-200 {{ request()->routeIs('agent.orders') || request()->routeIs('agent.order_detail') ? $activeClass : $inactiveClass }}" :class="sidebarOpen ? '' : 'justify-center'" title="Ordini Inviati">
                            <span class="text-base" :class="sidebarOpen ? 'mr-3' : ''">📝</span> 
                            <span x-show="sidebarOpen" x-transition class="text-sm">Ordini Inviati</span>
                        </a>
                        @if(Auth::user()->role === 'agent' || Auth::user()->role === 'admin')
                        <a href="{{ route('agent.price-lists.index') }}" class="flex items-center px-3 py-2.5 transition-all duration-200 {{ request()->routeIs('agent.price-lists.*') ? $activeClass : $inactiveClass }}" :class="sidebarOpen ? '' : 'justify-center'" title="Listini Prezzi">
                            <span class="text-base" :class="sidebarOpen ? 'mr-3' : ''">🏷️</span> 
                            <span x-show="sidebarOpen" x-transition class="text-sm">Listini Prezzi</span>
                        </a>
                        @endif
                    </nav>
                </aside>

                <!-- Mobile Header -->
                <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                    <header class="md:hidden bg-black text-white px-4 py-3 flex justify-between items-center shadow-lg shrink-0 relative z-40" x-data="{ mobileNav: false }">
                        <div class="flex items-center gap-2">
                            <span class="font-black text-yellow-400 text-sm tracking-wider">B2B PORTAL</span>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase bg-zinc-900 px-2 py-0.5 rounded border border-zinc-800">
                                {{ Auth::user()->role === 'customer' ? 'Cliente' : (Auth::user()->role === 'admin' ? 'Admin' : 'Agente') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('agent.cart') }}" class="relative p-1.5 text-yellow-400 font-black text-xs flex items-center gap-1">
                                <span>🛒</span>
                                @if(count(session('b2b_cart', [])) > 0)
                                    <span class="bg-rose-600 text-white text-[9px] px-1.5 py-0.2 rounded-full font-black">{{ count(session('b2b_cart')) }}</span>
                                @endif
                            </a>
                            <button @click="mobileNav = !mobileNav" type="button" class="px-2.5 py-1.5 border border-yellow-400 rounded-lg text-yellow-400 font-black text-xs uppercase flex items-center gap-1">
                                <span>☰</span>
                                <span>Menu</span>
                            </button>
                        </div>
                        
                        <!-- Mobile Dropdown -->
                        <div x-show="mobileNav" @click.away="mobileNav = false" x-transition class="absolute top-full left-0 right-0 bg-black border-b border-zinc-800 p-4 space-y-2 shadow-2xl z-50" style="display: none;">
                            <a href="{{ route('agent.dashboard') }}" class="block px-3 py-2 rounded-lg text-xs font-black uppercase tracking-wider {{ request()->routeIs('agent.dashboard') ? 'bg-yellow-400 text-slate-950' : 'text-zinc-300 hover:text-white bg-zinc-900' }}">📊 Dashboard</a>
                            <a href="{{ route('agent.catalog') }}" class="block px-3 py-2 rounded-lg text-xs font-black uppercase tracking-wider {{ request()->routeIs('agent.catalog') || request()->routeIs('agent.product') ? 'bg-yellow-400 text-slate-950' : 'text-zinc-300 hover:text-white bg-zinc-900' }}">📦 Catalogo Prodotti</a>
                            <a href="{{ route('agent.cart') }}" class="block px-3 py-2 rounded-lg text-xs font-black uppercase tracking-wider {{ request()->routeIs('agent.cart') ? 'bg-yellow-400 text-slate-950' : 'text-zinc-300 hover:text-white bg-zinc-900' }}">🛒 Carrello ({{ count(session('b2b_cart', [])) }})</a>
                            <a href="{{ route('agent.orders') }}" class="block px-3 py-2 rounded-lg text-xs font-black uppercase tracking-wider {{ request()->routeIs('agent.orders') || request()->routeIs('agent.order_detail') ? 'bg-yellow-400 text-slate-950' : 'text-zinc-300 hover:text-white bg-zinc-900' }}">📝 Ordini Inviati</a>
                            @if(Auth::user()->role === 'agent' || Auth::user()->role === 'admin')
                                <a href="{{ route('agent.price-lists.index') }}" class="block px-3 py-2 rounded-lg text-xs font-black uppercase tracking-wider {{ request()->routeIs('agent.price-lists.*') ? 'bg-yellow-400 text-slate-950' : 'text-zinc-300 hover:text-white bg-zinc-900' }}">🏷️ Listini Prezzi</a>
                            @endif
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.b2b.orders.index') }}" class="block px-3 py-2 rounded-lg text-xs font-black uppercase tracking-wider text-amber-300 bg-zinc-900 border border-amber-500/30">⚙️ Pannello Admin B2B</a>
                            @endif
                            <a href="{{ route('agent.profile') }}" class="block px-3 py-2 rounded-lg text-xs font-black uppercase tracking-wider text-zinc-400 hover:text-white">👤 Profilo</a>
                        </div>
                    </header>

                    <!-- Desktop Sub-header -->
                    @isset($header)
                        <div class="bg-white shadow-sm border-b border-gray-100 shrink-0">
                            <div class="max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
                                {{ $header }}
                            </div>
                        </div>
                    @endisset

                    <!-- Main Content Area -->
                    <main class="flex-1 overflow-y-auto overflow-x-auto min-w-0 bg-gray-50 p-3 sm:p-4 md:p-6 lg:p-8">
                        @if(session('success'))
                            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm font-bold text-sm">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl shadow-sm font-bold text-sm">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{ $slot }}
                    </main>
                    
                    <footer class="bg-white border-t border-gray-200 p-4 text-center text-xs font-bold text-gray-400 shrink-0">
                        &copy; {{ date('Y') }} {{ config('app.name') }} B2B Portal. Tutti i diritti riservati.
                    </footer>
                </div>
            </div>
    <script>
        // Sincronizzazione automatica in background delle giacenze ogni 2 minuti
        setInterval(() => {
            fetch("{{ route('agent.ping_sync') }}", {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).catch(() => {});
        }, 120000);
    </script>
    @stack('scripts')
</body>
</html>

