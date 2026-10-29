@php
    $siteLogo = \App\Models\Setting::where('key', 'site_logo')->value('value') ?: 'storage/logo/logo-bicap.png';
    $currentLocale = app()->getLocale();
    $freeThreshold = (float)(\App\Models\Setting::where('key', 'shop_free_shipping_threshold')->value('value') ?? 89);
    $freeScope = \App\Models\Setting::where('key', 'shop_free_shipping_scope')->value('value') ?? 'italy';
@endphp

<!-- Top Announcement Bar (Yellow Banner - Scrolls away naturally when page scrolls) -->
<div class="bicap-top-banner" style="background-color: #fedb00 !important; color: #000000 !important; width: 100% !important; text-align: center !important; padding: 7px 16px !important; font-family: 'Montserrat', sans-serif !important; font-size: 12px !important; font-weight: 700 !important; letter-spacing: 0.5px !important; text-transform: uppercase !important; border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important; user-select: none !important; position: relative !important; z-index: 40 !important; box-sizing: border-box !important;">
    @if($freeThreshold > 0)
        {{ $currentLocale === 'en' ? ('FREE SHIPPING ' . ($freeScope === 'all' ? '' : 'IN ITALY ') . 'FROM € ' . (int)$freeThreshold) : ('SPEDIZIONE GRATUITA ' . ($freeScope === 'all' ? '' : 'ITALIA ') . 'A PARTIRE DA € ' . (int)$freeThreshold) }}
    @else
        {{ $currentLocale === 'en' ? 'FAST SHIPPING THROUGHOUT ITALY AND EUROPE' : 'SPEDIZIONI VELOCI IN TUTTA ITALIA ED EUROPA' }}
    @endif
</div>

<!-- Main Navigation Bar (Black Header - Sticks to top on scroll, edge-to-edge full width) -->
<header class="bicap-header-sticky" style="position: sticky !important; top: 0 !important; z-index: 9990 !important; background-color: #000000 !important; width: 100% !important; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5) !important;" x-data="{ mobileMenuOpen: false, langOpen: false }">
    <div class="bicap-header-container" style="width: 100% !important; display: flex !important; align-items: center !important; justify-content: space-between !important; height: 80px !important; box-sizing: border-box !important;">
        
        <!-- Far Left: Logo BICAP ABSOLUTELY SAFE -->
        <div class="flex items-center shrink-0">
            <a href="{{ route('public.home') }}" class="flex items-center transition-opacity hover:opacity-90">
                @if(file_exists(public_path($siteLogo)))
                    <img src="{{ asset($siteLogo) }}" alt="BICAP Absolutely Safe" class="h-10 sm:h-12 w-auto object-contain">
                @else
                    <div class="flex items-center gap-2">
                        <span style="background-color: #fedb00 !important; color: #000000 !important;" class="inline-block w-8 h-8 rounded-sm transform -skew-x-12 flex items-center justify-center font-black text-lg">B</span>
                        <div class="flex flex-col">
                            <span class="text-xl sm:text-2xl font-black tracking-tighter uppercase text-white leading-none">BICAP</span>
                            <span class="text-[8px] font-bold tracking-widest uppercase text-zinc-400 leading-none mt-0.5">ABSOLUTELY SAFE</span>
                        </div>
                    </div>
                @endif
            </a>
        </div>

        <!-- Far Right: Desktop Nav Menu + Yellow Icons + Language Flag -->
        <div class="flex items-center space-x-6 xl:space-x-8 shrink-0">
            <!-- Desktop Nav Menu Links (Exact bicap.shop: Montserrat 13px 700 uppercase, padding 0 10px, normal tracking) -->
            <nav class="hidden lg:flex items-center space-x-0.5 xl:space-x-1">
                <a href="{{ url('/mission-vision') }}" class="bicap-nav-link">
                    MISSION & VISION
                </a>
                <a href="{{ route('public.home') }}" class="bicap-nav-link">
                    SHOP
                </a>
                <a href="{{ url('/condizioni-di-vendita') }}" class="bicap-nav-link">
                    {{ $currentLocale === 'en' ? 'TERMS OF SALE' : 'CONDIZIONI DI VENDITA' }}
                </a>
                <a href="{{ url('/resi-e-rimborsi') }}" class="bicap-nav-link">
                    {{ $currentLocale === 'en' ? 'RETURNS & REFUNDS' : 'RESI E RIMBORSI' }}
                </a>
                <a href="{{ url('/spedizioni') }}" class="bicap-nav-link">
                    {{ $currentLocale === 'en' ? 'SHIPPING' : 'SPEDIZIONI' }}
                </a>
                <a href="{{ url('/faq') }}" class="bicap-nav-link">
                    FAQ
                </a>
                <a href="https://www.bicap.it" target="_blank" rel="noopener noreferrer" class="bicap-nav-link bicap-nav-link-yellow font-bold" style="color: #fedb00 !important;">
                    <span style="color: #fedb00 !important;">BICAP.IT</span>
                </a>
            </nav>

            <!-- Utility Action Icons (Far Right - Solid Yellow icons exactly matching bicap.shop) -->
            <div class="flex items-center space-x-3.5 sm:space-x-4 shrink-0">
                <!-- 1. Email Contact Icon (Solid Yellow) -->
                <a href="mailto:info@bicap.it" style="color: #fedb00 !important;" class="hover:opacity-80 transition-opacity p-1 inline-flex items-center" title="Contattaci via Email">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="#fedb00" style="fill: #fedb00 !important;">
                        <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                        <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
                    </svg>
                </a>

                <!-- 2. User / Shop Account Login / Area Personale (Solid Yellow) -->
                @auth
                    @php
                        $user = auth()->user();
                        $isShopCustomer = in_array($user->role, ['customer', 'user']) && !$user->b2b_customer_id;
                    @endphp
                    @if($isShopCustomer)
                        <!-- Dropdown Menu Cliente Shop -->
                        <div class="relative inline-flex items-center" x-data="{ userMenuOpen: false }" @click.away="userMenuOpen = false">
                            <button @click="userMenuOpen = !userMenuOpen" 
                                    style="color: #fedb00 !important;" 
                                    class="hover:opacity-80 transition-opacity p-1 inline-flex items-center gap-1 focus:outline-none cursor-pointer" 
                                    title="Area Personale ({{ $user->name }})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="#fedb00" style="fill: #fedb00 !important;">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
                                </svg>
                                <svg class="w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="userMenuOpen" 
                                 x-transition 
                                 class="absolute right-0 top-full mt-2 w-64 bg-black border border-zinc-700 shadow-2xl py-2 z-50 text-xs" 
                                 style="display: none; background-color: #000000 !important;">
                                <div class="px-4 py-3 border-b border-zinc-800">
                                    <p class="font-bold truncate" style="color: #ffffff !important; font-size: 13px;">{{ $user->name }}</p>
                                    <p class="truncate mt-0.5" style="color: #a1a1aa !important; font-size: 11px;">{{ $user->email }}</p>
                                </div>
                                <a href="{{ route('public.account.dashboard') }}" 
                                   style="color: #ffffff !important; text-decoration: none !important;" 
                                   class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-zinc-900 transition font-bold"
                                   onmouseover="this.style.color='#fedb00'"
                                   onmouseout="this.style.color='#ffffff'">
                                    <svg class="w-4 h-4 shrink-0" style="color: #fedb00;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    <span style="color: inherit !important;">I Miei Ordini</span>
                                </a>
                                <a href="{{ route('public.account.profile') }}" 
                                   style="color: #ffffff !important; text-decoration: none !important;" 
                                   class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-zinc-900 transition font-bold"
                                   onmouseover="this.style.color='#fedb00'"
                                   onmouseout="this.style.color='#ffffff'">
                                    <svg class="w-4 h-4 shrink-0" style="color: #fedb00;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span style="color: inherit !important;">Dati Personali & Spedizione</span>
                                </a>
                                <div class="pt-2 mt-1 border-t border-zinc-800">
                                    <form method="POST" action="{{ route('public.account.logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                style="color: #ff6b6b !important;" 
                                                class="w-full text-left flex items-center gap-2.5 px-4 py-2 hover:bg-zinc-900 transition cursor-pointer font-bold"
                                                onmouseover="this.style.color='#ffa8a8'"
                                                onmouseout="this.style.color='#ff6b6b'">
                                            <svg class="w-4 h-4 shrink-0" style="color: #ff6b6b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            <span style="color: inherit !important;">Esci</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        @php
                            $dashUrl = $user->role === 'admin' ? route('dashboard') : route('agent.dashboard');
                        @endphp
                        <a href="{{ $dashUrl }}" style="color: #fedb00 !important;" class="hover:opacity-80 transition-opacity p-1 inline-flex items-center" title="Pannello B2B / Gestione ({{ $user->name }})">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="#fedb00" style="fill: #fedb00 !important;">
                                <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                @else
                    <a href="{{ route('public.account.login') }}" style="color: #fedb00 !important;" class="hover:opacity-80 transition-opacity p-1 inline-flex items-center" title="Area Personale / Accedi">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="#fedb00" style="fill: #fedb00 !important;">
                            <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endauth

                <!-- 3. Cart Icon (Solid Yellow with Badge) -->
                @php
                    $sessionCart = session()->get('cart', []);
                    $sessionCartCount = is_array($sessionCart) ? array_sum(array_column($sessionCart, 'quantita')) : 0;
                @endphp
                <a href="{{ route('public.shop.cart.index') }}" style="color: #fedb00 !important;" class="hover:opacity-80 transition-opacity p-1 inline-flex items-center relative" title="Carrello ({{ $sessionCartCount }})">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="#fedb00" style="fill: #fedb00 !important;">
                        <path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" />
                    </svg>
                    @if($sessionCartCount > 0)
                        <span class="absolute -top-1.5 -right-2 bg-[#e35205] text-white text-[10px] font-black w-4 h-4 rounded-full flex items-center justify-center leading-none shadow-sm">
                            {{ $sessionCartCount > 99 ? '99+' : $sessionCartCount }}
                        </span>
                    @endif
                </a>

                <!-- 4. Language Switcher (Italian Flag) -->
                <div class="relative inline-flex items-center" @click.away="langOpen = false">
                    <button @click="langOpen = !langOpen" class="flex items-center gap-1 p-1 hover:opacity-80 transition" title="Cambia lingua">
                        @if($currentLocale === 'en')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" class="w-5 h-3.5 rounded-none object-cover shadow-xs"><path fill="#012169" d="M0 0h60v30H0z"/><path stroke="#FFF" stroke-width="6" d="M0 0l60 30m0-30L0 30"/><path stroke="#C8102E" stroke-width="4" d="M0 0l60 30m0-30L0 30"/><path stroke="#FFF" stroke-width="10" d="M30 0v30M0 15h60"/><path stroke="#C8102E" stroke-width="6" d="M30 0v30M0 15h60"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3 2" class="w-5 h-3.5 rounded-none object-cover shadow-xs"><rect width="3" height="2" fill="#009246"/><rect width="2" height="2" x="1" fill="#fff"/><rect width="1" height="2" x="2" fill="#ce2b37"/></svg>
                        @endif
                    </button>
                    <div x-show="langOpen" x-transition class="absolute right-0 mt-2 w-32 bg-zinc-950 border border-zinc-800 rounded-xl shadow-2xl py-1 z-50 text-xs font-bold" style="display: none;">
                        <a href="{{ route('set-locale', 'it') }}" class="flex items-center gap-2 px-3 py-2 hover:bg-zinc-800 transition text-white {{ $currentLocale === 'it' ? 'bg-zinc-800 text-[#fedb00]' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3 2" class="w-4 h-2.5 rounded-none"><rect width="3" height="2" fill="#009246"/><rect width="2" height="2" x="1" fill="#fff"/><rect width="1" height="2" x="2" fill="#ce2b37"/></svg>
                            <span>Italiano</span>
                        </a>
                        <a href="{{ route('set-locale', 'en') }}" class="flex items-center gap-2 px-3 py-2 hover:bg-zinc-800 transition text-white {{ $currentLocale === 'en' ? 'bg-zinc-800 text-[#fedb00]' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" class="w-4 h-2"><path fill="#012169" d="M0 0h60v30H0z"/><path stroke="#FFF" stroke-width="6" d="M0 0l60 30m0-30L0 30"/><path stroke="#C8102E" stroke-width="4" d="M0 0l60 30m0-30L0 30"/><path stroke="#FFF" stroke-width="10" d="M30 0v30M0 15h60"/><path stroke="#C8102E" stroke-width="6" d="M30 0v30M0 15h60"/></svg>
                            <span>English</span>
                        </a>
                    </div>
                </div>

                <!-- 5. Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-white hover:text-[#fedb00] p-1.5 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div x-show="mobileMenuOpen" class="lg:hidden bg-zinc-950 border-b border-zinc-800 text-white px-4 py-4 space-y-3" style="display: none;"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2">
        <a href="{{ url('/mission-vision') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-zinc-300 hover:text-[#fedb00] transition-colors">MISSION & VISION</a>
        <a href="{{ route('public.home') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-zinc-300 hover:text-[#fedb00] transition-colors">SHOP</a>
        <a href="{{ url('/condizioni-di-vendita') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-zinc-300 hover:text-[#fedb00] transition-colors">{{ $currentLocale === 'en' ? 'TERMS OF SALE' : 'CONDIZIONI DI VENDITA' }}</a>
        <a href="{{ url('/resi-e-rimborsi') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-zinc-300 hover:text-[#fedb00] transition-colors">{{ $currentLocale === 'en' ? 'RETURNS & REFUNDS' : 'RESI E RIMBORSI' }}</a>
        <a href="{{ url('/spedizioni') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-zinc-300 hover:text-[#fedb00] transition-colors">{{ $currentLocale === 'en' ? 'SHIPPING' : 'SPEDIZIONI' }}</a>
        <a href="{{ url('/faq') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-zinc-300 hover:text-[#fedb00] transition-colors">FAQ</a>
        <a href="https://www.bicap.it" target="_blank" class="block py-2 text-sm font-bold uppercase tracking-wider text-[#fedb00] hover:opacity-80 transition-opacity">BICAP.IT ↗</a>
        <div class="pt-3 border-t border-zinc-800 space-y-2">
            @auth
                @if(in_array(auth()->user()->role, ['customer', 'user']) && !auth()->user()->b2b_customer_id)
                    <div class="text-xs text-zinc-400 font-bold mb-1">{{ $currentLocale === 'en' ? 'PERSONAL AREA' : 'AREA PERSONALE' }} ({{ auth()->user()->name }})</div>
                    <a href="{{ route('public.account.dashboard') }}" style="color: #ffffff !important;" class="block py-1.5 text-xs font-bold uppercase tracking-wider hover:text-[#fedb00]">{{ $currentLocale === 'en' ? 'MY ORDERS' : 'I MIEI ORDINI' }}</a>
                    <a href="{{ route('public.account.profile') }}" style="color: #ffffff !important;" class="block py-1.5 text-xs font-bold uppercase tracking-wider hover:text-[#fedb00]">{{ $currentLocale === 'en' ? 'PERSONAL DATA & SHIPPING' : 'DATI PERSONALI & SPEDIZIONE' }}</a>
                    <form method="POST" action="{{ route('public.account.logout') }}" class="pt-1">
                        @csrf
                        <button type="submit" class="text-xs font-bold uppercase tracking-wider text-rose-400 hover:text-rose-300 cursor-pointer">{{ $currentLocale === 'en' ? 'Log out' : "Esci dall'Account" }}</button>
                    </form>
                @else
                    <a href="{{ auth()->user()->role === 'admin' ? route('dashboard') : route('agent.dashboard') }}" class="inline-flex items-center gap-2 bg-[#fedb00] text-black px-4 py-2 rounded-none text-xs font-black uppercase tracking-wider">
                        <span>Pannello Gestione</span>
                        <span>→</span>
                    </a>
                @endif
            @else
                <a href="{{ route('public.account.login') }}" class="w-full inline-flex items-center justify-center gap-2 bg-[#fedb00] text-black px-4 py-2.5 rounded-none text-xs font-black uppercase tracking-wider">
                    <span>Accedi Area Personale</span>
                    <span>→</span>
                </a>
                <div class="text-center pt-2">
                    <a href="{{ route('login') }}" class="text-[11px] text-zinc-400 hover:text-white underline">Accedi Portale B2B Agenti</a>
                </div>
            @endauth
        </div>
    </div>
</header>
