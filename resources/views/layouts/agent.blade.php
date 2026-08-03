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
                <div class="flex items-center gap-3">
                    @php
                        $logo = \App\Models\Setting::where('key', 'site_logo')->value('value') ?? '';
                    @endphp
                    @if(!empty($logo))
                        <img src="{{ asset($logo) }}" class="h-8 w-auto object-contain" alt="Logo">
                    @else
                        <h1 class="text-xl font-bold tracking-tight uppercase text-yellow-400">B2B Portal</h1>
                    @endif
                    <span class="text-xs font-black uppercase tracking-widest text-zinc-400 border-l border-zinc-800 pl-3">
                        {{ Auth::user()->role === 'customer' ? 'Area Clienti' : 'Area Agenti' }}
                    </span>
                </div>

                <div class="flex items-center gap-4">
                    <span class="bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs px-3 py-1.5 rounded-xl font-bold uppercase flex items-center gap-2">
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

            <div class="flex flex-1 overflow-hidden">
                
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
                    <header class="md:hidden bg-black text-white p-4 flex justify-between items-center shadow-lg shrink-0">
                        <span class="font-black text-yellow-400">B2B PORTAL</span>
                        <a href="{{ route('agent.dashboard') }}" class="p-2 border border-yellow-400 rounded text-yellow-400 font-black text-xs uppercase">Menu</a>
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
                    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 md:p-6 lg:p-8">
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
        </div>

    @stack('scripts')
</body>
</html>
