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

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        
        <style>
            /* Override Tailwind Indigo colors to BICAP Black & Yellow corporate identity */
            .bg-indigo-600 {
                background-color: #000000 !important;
                border-color: #09090b !important;
            }
            .hover\:bg-indigo-700:hover {
                background-color: #facc15 !important;
                color: #020617 !important;
            }
            .text-indigo-600 {
                color: #000000 !important;
                font-weight: 800 !important;
            }
            .text-indigo-600:hover {
                color: #eab308 !important;
                text-decoration: underline !important;
            }
            .hover\:text-indigo-900:hover {
                color: #eab308 !important;
                text-decoration: underline !important;
            }
            .text-indigo-700 {
                color: #020617 !important;
            }
            .text-indigo-400 {
                color: #eab308 !important;
            }
            .bg-indigo-50 {
                background-color: #f4f4f5 !important;
            }
            .bg-indigo-100 {
                background-color: #e4e4e7 !important;
            }
            .text-indigo-900 {
                color: #09090b !important;
            }
            .border-indigo-500 {
                border-color: #eab308 !important;
            }
            .border-indigo-600 {
                border-color: #09090b !important;
            }
            .border-indigo-100 {
                border-color: #e4e4e7 !important;
            }
            
            /* Focus rings and inputs */
            .focus\:ring-indigo-500:focus, .focus\:ring-yellow-500:focus {
                --tw-ring-color: #eab308 !important;
                border-color: #eab308 !important;
            }
            .focus\:border-indigo-500:focus, .focus\:border-yellow-500:focus {
                border-color: #eab308 !important;
            }
            
            /* Selection checkboxes custom style */
            input[type="checkbox"]:checked {
                background-color: #000000 !important;
                border-color: #000000 !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased overflow-hidden" x-data="{ showWipeModal: false }">
        <div class="flex flex-col h-screen bg-gray-50">
            
            <!-- Top Header Bar Nero Completo -->
            <header class="bg-black text-white border-b border-zinc-900 h-16 flex items-center justify-between px-6 shrink-0 z-30 shadow-md">
                <a href="{{ route('admin.b2b.dashboard') }}" class="flex items-center gap-3 hover:opacity-90 transition">
                    @php
                        $logo = \App\Models\Setting::where('key', 'site_logo')->value('value') ?? '';
                    @endphp
                    @if(!empty($logo))
                        <img src="{{ asset($logo) }}" class="h-8 w-auto object-contain" alt="Logo">
                    @else
                        <x-application-logo class="block h-8 w-auto fill-current text-yellow-400" />
                    @endif
                    <span class="text-xs font-black uppercase tracking-widest text-zinc-400 border-l border-zinc-800 pl-3">
                        BICAP ADMIN PORTAL
                    </span>
                </a>

                <div class="flex items-center gap-4">
                    <span class="bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs px-3 py-1.5 rounded-xl font-bold uppercase flex items-center gap-2">
                        👤 {{ Auth::user()->name }}
                    </span>
                    
                    <a href="{{ Auth::user()->role === 'admin' ? route('admin.b2b.dashboard') : route('agent.dashboard') }}" class="text-xs font-black text-yellow-400 hover:text-slate-950 bg-zinc-900 hover:bg-yellow-400 border border-zinc-800 hover:border-yellow-400 px-3.5 py-1.5 rounded-xl transition duration-300 uppercase shadow-sm">
                        🌐 Portale Agente / B2B
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
                <!-- Sidebar Navigation -->
                @include('layouts.navigation')

                <!-- Main Content Area -->
                <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                    <!-- Page Heading (Title bar per le singole viste) -->
                    @isset($header)
                        <div class="bg-white shadow-sm border-b border-gray-100">
                            <div class="max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
                                {{ $header }}
                            </div>
                        </div>
                    @endisset

                    <!-- Scrollable Page Content -->
                    <main class="flex-1 overflow-y-auto overflow-x-auto min-w-0 bg-gray-50 p-3 sm:p-4 md:p-6 lg:p-8">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>

        @stack('scripts')
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
    </body>
</html>
