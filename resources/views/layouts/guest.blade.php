<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $favicon = \App\Models\Setting::where('key', 'site_favicon')->value('value') ?: 'favicon.ico';
            $logo = \App\Models\Setting::where('key', 'site_logo')->value('value');
            $seoTitle = \App\Models\Setting::where('key', 'home_seo_title')->value('value') ?: 'BICAP Official: Calzature di sicurezza Made in Italy | Portale B2B';
            $seoDesc = \App\Models\Setting::where('key', 'home_seo_description')->value('value') ?: "Le calzature BICAP sono l'espressione del Made in Italy: un insieme armonico di esperienza, innovazione, passione e qualità delle materie prime utilizzate.";
            $seoImg = \App\Models\Setting::where('key', 'home_seo_image')->value('value') ?: '/storage/seo/bicap-og-image.jpg';
            $isEn = app()->getLocale() === 'en';
        @endphp

        <link rel="icon" type="image/x-icon" href="{{ asset($favicon) }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <title>{{ $seoTitle }}</title>
        <meta name="description" content="{{ $seoDesc }}">
        <meta name="keywords" content="calzature di sicurezza, calzature da lavoro, safety shoes, antinfortunistica, made in italy, workwear, bicap, calzaturificio 5bi, absolutely safe">
        <meta name="author" content="Calzaturificio 5BI s.r.l. - BICAP">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $seoTitle }}">
        <meta property="og:description" content="{{ $seoDesc }}">
        <meta property="og:image" content="{{ asset($seoImg) }}">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoTitle }}">
        <meta name="twitter:description" content="{{ $seoDesc }}">
        <meta name="twitter:image" content="{{ asset($seoImg) }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Bulletproof Dark Inputs for Auth with Full Autofill Support */
            .auth-input {
                background-color: #121217 !important;
                color: #ffffff !important;
                border: 1px solid #3f3f46 !important;
                font-size: 0.925rem !important;
                line-height: 1.5 !important;
            }
            .auth-input:focus {
                background-color: #09090b !important;
                color: #ffffff !important;
                border-color: #facc15 !important;
                box-shadow: 0 0 0 2px rgba(250, 204, 21, 0.35) !important;
                outline: none !important;
            }
            .auth-input::placeholder {
                color: #71717a !important;
            }
            /* Chrome / Safari / Edge / Firefox Autofill Override */
            .auth-input:-webkit-autofill,
            .auth-input:-webkit-autofill:hover, 
            .auth-input:-webkit-autofill:focus,
            .auth-input:-webkit-autofill:active {
                -webkit-text-fill-color: #ffffff !important;
                -webkit-box-shadow: 0 0 0px 1000px #18181b inset !important;
                box-shadow: 0 0 0px 1000px #18181b inset !important;
                transition: background-color 5000s ease-in-out 0s;
                caret-color: #ffffff !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-black text-white min-h-screen flex flex-col justify-between selection:bg-yellow-400 selection:text-black relative">
        
        <!-- Ambient background lighting for glassmorphic depth -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-32 -left-32 w-[500px] h-[500px] bg-yellow-500/10 rounded-full blur-[140px]"></div>
            <div class="absolute -bottom-32 -right-32 w-[500px] h-[500px] bg-yellow-500/10 rounded-full blur-[140px]"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-yellow-400/5 rounded-full blur-[160px]"></div>
        </div>

        <!-- Header / Language Switcher -->
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-end">
            <div class="flex items-center bg-zinc-950/80 backdrop-blur-md border border-zinc-800/80 rounded-xl p-1 shadow-sm">
                <a href="{{ route('set-locale', 'it') }}" class="px-2 py-1 text-xs font-black uppercase rounded-lg transition {{ app()->getLocale() === 'it' ? 'bg-yellow-400 text-slate-950 shadow-xs' : 'text-zinc-400 hover:text-white' }}" title="Lingua Italiana">
                    🇮🇹 IT
                </a>
                <a href="{{ route('set-locale', 'en') }}" class="px-2 py-1 text-xs font-black uppercase rounded-lg transition {{ app()->getLocale() === 'en' ? 'bg-yellow-400 text-slate-950 shadow-xs' : 'text-zinc-400 hover:text-white' }}" title="English Language">
                    🇬🇧 EN
                </a>
            </div>
        </div>

        <!-- Central Card Container -->
        <div class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-4 sm:py-8">
            <div class="w-full max-w-md">
                
                <!-- Main Glassmorphic Card -->
                <div class="bg-zinc-900/60 backdrop-blur-2xl border border-zinc-800/90 shadow-2xl shadow-black rounded-3xl p-6 sm:p-8 relative overflow-hidden">
                    
                    <!-- Top Accent Line -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-yellow-500 via-yellow-400 to-amber-500"></div>

                    <!-- Logo Section -->
                    <div class="flex flex-col items-center justify-center text-center mb-6 pt-2">
                        <div class="flex justify-center w-full">
                            <a href="{{ route('login') }}" class="inline-block transition-transform hover:scale-105 duration-200">
                                @if(!empty($logo))
                                    <img src="{{ asset($logo) }}" class="h-10 sm:h-12 w-auto mx-auto object-contain" alt="Logo BICAP">
                                @else
                                    <h1 class="text-2xl font-black tracking-tight uppercase text-yellow-400">BICAP</h1>
                                @endif
                            </a>
                        </div>

                        <div class="mt-3 flex justify-center w-full">
                            <span class="inline-flex items-center gap-1.5 bg-yellow-400/10 border border-yellow-400/20 px-3.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest text-yellow-400 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></span>
                                {{ $isEn ? 'B2B & Agent Portal' : 'Portale B2B & Agenti' }}
                            </span>
                        </div>
                    </div>

                    {{ $slot }}

                </div>

            </div>
        </div>

        <!-- Footer -->
        <div class="relative z-10 py-4 text-center text-xs font-semibold text-zinc-500">
            &copy; {{ date('Y') }} Cedma S.r.l. &bull; <span class="text-zinc-400">BICAP Absolutely Safe</span>
        </div>

    </body>
</html>
