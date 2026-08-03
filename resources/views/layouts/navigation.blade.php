<aside class="flex-shrink-0 w-64 bg-white text-gray-800 hidden md:flex flex-col h-full border-r border-gray-100 shadow-sm" x-data="{ open: false }">
    <!-- Menù di Navigazione Scrollabile -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1" aria-label="Sidebar">
        @php 
            $shop_enabled = \App\Models\Setting::where('key', 'shop_enabled')->value('value') == '1'; 
            $booking_enabled = \App\Models\Setting::where('key', 'booking_enabled')->value('value') == '1';
            $activeClass = 'bg-yellow-400 text-slate-950 font-black shadow-lg shadow-yellow-400/20 rounded-xl';
            $inactiveClass = 'text-gray-600 hover:bg-gray-100/80 hover:text-slate-950 font-bold rounded-xl';
        @endphp

        @if(Auth::check() && Auth::user()->role === 'admin')
            @php
                $user = Auth::user();
            @endphp

            <!-- Categoria: SHOP (condizionale) -->
            @if($shop_enabled && ($user->is_super_admin || $user->can_manage_shop))
            <div class="pt-2 pb-2">
                <p class="px-3 text-[10px] font-black tracking-widest text-gray-400 uppercase">Shop</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('admin.shop.categorie.index') }}" class="{{ request()->routeIs('admin.shop.categorie.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                        <span class="mr-3 text-base">📁</span>
                        <span>Categorie</span>
                    </a>
                    <a href="{{ route('admin.shop.collezioni.index') }}" class="{{ request()->routeIs('admin.shop.collezioni.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                        <span class="mr-3 text-base">📂</span>
                        <span>Collezioni</span>
                    </a>
                    <a href="{{ route('admin.shop.marche.index') }}" class="{{ request()->routeIs('admin.shop.marche.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                        <span class="mr-3 text-base">🏷️</span>
                        <span>Marche</span>
                    </a>
                    <a href="{{ route('admin.shop.prodotti.index') }}" class="{{ request()->routeIs('admin.shop.prodotti.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                        <span class="mr-3 text-base">📦</span>
                        <span>Prodotti & Inventario</span>
                    </a>
                    <a href="{{ route('admin.shop.ordini.index') }}" class="{{ request()->routeIs('admin.shop.ordini.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                        <span class="mr-3 text-base">🛒</span>
                        <span>Gestione Ordini</span>
                    </a>
                    <a href="{{ route('admin.shop.configuration') }}" class="{{ request()->routeIs('admin.shop.configuration') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                        <span class="mr-3 text-base">⚙️</span>
                        <span>Configurazione Generali</span>
                    </a>
                    <a href="{{ route('admin.shop.shipping_costs.index') }}" class="{{ request()->routeIs('admin.shop.shipping_costs.index') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                        <span class="mr-3 text-base">🚚</span>
                        <span>Spese di Spedizione</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Categoria: BOOKING (condizionale) -->
            @if($booking_enabled && ($user->is_super_admin || $user->can_manage_booking))
            <div class="pt-4 pb-2 border-t border-gray-100/80 mt-2">
                <p class="px-3 text-[10px] font-black tracking-widest text-gray-400 uppercase">Booking</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('admin.booking.structures.index') }}" class="{{ request()->routeIs('admin.booking.structures.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">🏨</span>
                        <span>Strutture</span>
                    </a>
                    <a href="{{ route('admin.booking.bookings.index') }}" class="{{ request()->routeIs('admin.booking.bookings.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">🧾</span>
                        <span>Prenotazioni</span>
                    </a>
                    <a href="{{ route('admin.booking.calendar') }}" class="{{ request()->routeIs('admin.booking.calendar') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">🗓️</span>
                        <span>Calendario</span>
                    </a>
                    <a href="{{ route('admin.booking.services.index') }}" class="{{ request()->routeIs('admin.booking.services.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">🛠️</span>
                        <span>Servizi</span>
                    </a>
                    <a href="{{ route('admin.booking.extras.index') }}" class="{{ request()->routeIs('admin.booking.extras.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">➕</span>
                        <span>Servizi Extra</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Categoria: VOIP & AI (condizionale) -->
            @if($user->is_super_admin || $user->can_manage_voip)
            <div class="pt-4 pb-2 border-t border-gray-100/80 mt-2">
                <p class="px-3 text-[10px] font-black tracking-widest text-gray-400 uppercase">Voip & AI</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('admin.appointments.index') }}" class="{{ request()->routeIs('admin.appointments.index') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">📅</span>
                        <span>Agenda</span>
                    </a>
                    <a href="{{ route('admin.vapi.index') }}" class="{{ request()->routeIs('admin.vapi.index') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">🤖</span>
                        <span>Agente AI</span>
                    </a>
                    <a href="{{ route('admin.departments.index') }}" class="{{ request()->routeIs('admin.departments.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">🏢</span>
                        <span>Gestione Reparti</span>
                    </a>
                    <a href="{{ route('admin.vapi.tickets.index') }}" class="{{ request()->routeIs('admin.vapi.tickets.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">🎫</span>
                        <span>Ticket Ricevuti</span>
                    </a>
                    <a href="{{ route('admin.vapi.sms.index') }}" class="{{ request()->routeIs('admin.vapi.sms.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">💬</span>
                        <span>SMS Ricevuti</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Categoria: AGENTI & B2B (condizionale) -->
            @if($user->is_super_admin || $user->can_manage_agents)
            <div class="pt-4 pb-2 border-t border-gray-100/80 mt-2">
                <p class="px-3 text-[10px] font-black tracking-widest text-gray-400 uppercase">Agenti & B2B</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('admin.b2b.dashboard') }}" class="{{ request()->routeIs('admin.b2b.dashboard') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">📊</span>
                        <span>Dashboard B2B</span>
                    </a>
                    <a href="{{ route('admin.b2b.orders.index') }}" class="{{ request()->routeIs('admin.b2b.orders.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">📝</span>
                        <span>Ordini Ricevuti</span>
                    </a>
                    <a href="{{ route('admin.b2b.agents.index') }}" class="{{ request()->routeIs('admin.b2b.agents.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">👤</span>
                        <span>Agenti</span>
                    </a>
                    <a href="{{ route('admin.b2b.customers.index') }}" class="{{ request()->routeIs('admin.b2b.customers.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">🏢</span>
                        <span>Clienti B2B</span>
                    </a>
                    <a href="{{ route('admin.b2b.products.index') }}" class="{{ request()->routeIs('admin.b2b.products.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">📦</span>
                        <span>Inventario Prodotti</span>
                    </a>
                    <a href="{{ route('admin.b2b.brands.index') }}" class="{{ request()->routeIs('admin.b2b.brands.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">🏷️</span>
                        <span>Gestione Linee</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Categoria: SITO (Solo per Super Admin) -->
            @if($user->is_super_admin)
            <div class="pt-4 pb-2 border-t border-gray-100/80 mt-2">
                <p class="px-3 text-[10px] font-black tracking-widest text-gray-400 uppercase">Sito</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('admin.articoli.index') }}" class="{{ request()->routeIs('admin.articoli.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">📰</span>
                        <span>Articoli</span>
                    </a>
                    <a href="{{ route('admin.home.edit') }}" class="{{ request()->routeIs('admin.home.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">✏️</span>
                        <span>Home Page Editor</span>
                    </a>
                    <a href="{{ route('admin.sezioni.index') }}" class="{{ request()->routeIs('admin.sezioni.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">🧩</span>
                        <span>Sezioni Sito</span>
                    </a>
                    <a href="{{ route('admin.global-widgets.index') }}" class="{{ request()->routeIs('admin.global-widgets.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">⚙️</span>
                        <span>Widget Globali</span>
                    </a>
                    <a href="{{ route('admin.contatti.index') }}" class="{{ request()->routeIs('admin.contatti.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm transition-all duration-200">
                        <span class="mr-3 text-base">📧</span>
                        <span>Form Contatti</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Link Singoli Inferiori -->
            <div class="pt-4 pb-2 border-t border-gray-100/80 mt-2 space-y-1">
                @if($user->is_super_admin)
                <a href="{{ route('admin.filemanager') }}" class="{{ request()->routeIs('admin.filemanager') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                    <span class="mr-3 text-base">📁</span>
                    <span>File Manager</span>
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                    <span class="mr-3 text-base">👥</span>
                    <span>Amministratori</span>
                </a>
                @endif

                <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? $activeClass : $inactiveClass }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                    <span class="mr-3 text-base">⚙️</span>
                    <span>Configurazione</span>
                </a>
            </div>
            @endif
    </nav>
</aside>

<!-- Mobile Menu Header -->
<div class="md:hidden flex items-center justify-between bg-black px-4 py-3 border-b border-zinc-900 w-full" x-data="{ open: false }">
    <a href="{{ route('dashboard') }}" class="text-white font-bold tracking-wider flex items-center gap-2">
        <x-application-logo class="block h-8 w-auto fill-current text-yellow-400 inline-block" />
        <span class="text-xs uppercase font-black tracking-widest text-zinc-400">BICAP Admin</span>
    </a>
    <button @click="open = !open" type="button" class="text-slate-400 hover:text-white focus:outline-none focus:text-white">
        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    
    <!-- Mobile Dropdown -->
    <div x-show="open" @click.away="open = false" class="absolute top-14 left-0 w-full bg-black border-b border-zinc-900 shadow-xl z-50 overflow-y-auto max-h-[80vh]" style="display: none;">
        <nav class="px-3 py-4 space-y-1.5">
            @if(Auth::check() && Auth::user()->role === 'admin')
                @php 
                    $user = Auth::user(); 
                    $mobileActive = 'block px-4 py-2.5 rounded-lg text-sm font-black bg-yellow-400 text-slate-950';
                    $mobileInactive = 'block px-4 py-2.5 rounded-lg text-sm font-bold text-slate-300 hover:text-white hover:bg-zinc-900/60';
                @endphp
                
                @if($shop_enabled && ($user->is_super_admin || $user->can_manage_shop))
                <div class="mb-4">
                    <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Shop</p>
                    <a href="{{ route('admin.shop.categorie.index') }}" class="{{ request()->routeIs('admin.shop.categorie.*') ? $mobileActive : $mobileInactive }}">📁 Categorie</a>
                    <a href="{{ route('admin.shop.collezioni.index') }}" class="{{ request()->routeIs('admin.shop.collezioni.*') ? $mobileActive : $mobileInactive }}">📂 Collezioni</a>
                    <a href="{{ route('admin.shop.marche.index') }}" class="{{ request()->routeIs('admin.shop.marche.*') ? $mobileActive : $mobileInactive }}">🏷️ Marche</a>
                    <a href="{{ route('admin.shop.prodotti.index') }}" class="{{ request()->routeIs('admin.shop.prodotti.*') ? $mobileActive : $mobileInactive }}">📦 Prodotti & Inventario</a>
                    <a href="{{ route('admin.shop.ordini.index') }}" class="{{ request()->routeIs('admin.shop.ordini.*') ? $mobileActive : $mobileInactive }}">🛒 Gestione Ordini</a>
                    <a href="{{ route('admin.shop.configuration') }}" class="{{ request()->routeIs('admin.shop.configuration') ? $mobileActive : $mobileInactive }}">⚙️ Configurazione Shop</a>
                    <a href="{{ route('admin.shop.shipping_costs.index') }}" class="{{ request()->routeIs('admin.shop.shipping_costs.index') ? $mobileActive : $mobileInactive }}">🚚 Spese di Spedizione</a>
                </div>
                @endif

                @if($booking_enabled && ($user->is_super_admin || $user->can_manage_booking))
                <div class="mb-4 border-t border-zinc-800 pt-3">
                    <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Booking</p>
                    <a href="{{ route('admin.booking.structures.index') }}" class="{{ request()->routeIs('admin.booking.structures.*') ? $mobileActive : $mobileInactive }}">🏨 Strutture</a>
                    <a href="{{ route('admin.booking.bookings.index') }}" class="{{ request()->routeIs('admin.booking.bookings.*') ? $mobileActive : $mobileInactive }}">🧾 Prenotazioni</a>
                    <a href="{{ route('admin.booking.calendar') }}" class="{{ request()->routeIs('admin.booking.calendar') ? $mobileActive : $mobileInactive }}">🗓️ Calendario</a>
                </div>
                @endif

                @if($user->is_super_admin || $user->can_manage_voip)
                <div class="mb-4 border-t border-zinc-800 pt-3">
                    <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Voip & AI</p>
                    <a href="{{ route('admin.vapi.index') }}" class="{{ request()->routeIs('admin.vapi.index') ? $mobileActive : $mobileInactive }}">🤖 Agente AI</a>
                    <a href="{{ route('admin.departments.index') }}" class="{{ request()->routeIs('admin.departments.*') ? $mobileActive : $mobileInactive }}">🏢 Gestione Reparti</a>
                    <a href="{{ route('admin.vapi.tickets.index') }}" class="{{ request()->routeIs('admin.vapi.tickets.*') ? $mobileActive : $mobileInactive }}">🎫 Ticket Ricevuti</a>
                    <a href="{{ route('admin.vapi.sms.index') }}" class="{{ request()->routeIs('admin.vapi.sms.*') ? $mobileActive : $mobileInactive }}">💬 SMS Ricevuti</a>
                </div>
                @endif

                @if($user->is_super_admin || $user->can_manage_agents)
                <div class="mb-4 border-t border-zinc-800 pt-3">
                    <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Agenti & B2B</p>
                    <a href="{{ route('admin.b2b.dashboard') }}" class="{{ request()->routeIs('admin.b2b.dashboard') ? $mobileActive : $mobileInactive }}">📊 Dashboard B2B</a>
                    <a href="{{ route('admin.b2b.orders.index') }}" class="{{ request()->routeIs('admin.b2b.orders.*') ? $mobileActive : $mobileInactive }}">📝 Ordini Ricevuti</a>
                    <a href="{{ route('admin.b2b.agents.index') }}" class="{{ request()->routeIs('admin.b2b.agents.*') ? $mobileActive : $mobileInactive }}">👤 Agenti</a>
                    <a href="{{ route('admin.b2b.customers.index') }}" class="{{ request()->routeIs('admin.b2b.customers.*') ? $mobileActive : $mobileInactive }}">🏢 Clienti B2B</a>
                    <a href="{{ route('admin.b2b.products.index') }}" class="{{ request()->routeIs('admin.b2b.products.*') ? $mobileActive : $mobileInactive }}">📦 Inventario Prodotti</a>
                </div>
                @endif

                @if($user->is_super_admin)
                <div class="mb-4 border-t border-zinc-800 pt-3">
                    <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Sito</p>
                    <a href="{{ route('admin.articoli.index') }}" class="{{ request()->routeIs('admin.articoli.*') ? $mobileActive : $mobileInactive }}">📰 Articoli</a>
                    <a href="{{ route('admin.home.edit') }}" class="{{ request()->routeIs('admin.home.*') ? $mobileActive : $mobileInactive }}">✏️ Home Page Editor</a>
                    <a href="{{ route('admin.sezioni.index') }}" class="{{ request()->routeIs('admin.sezioni.*') ? $mobileActive : $mobileInactive }}">🧩 Sezioni Sito</a>
                    <a href="{{ route('admin.global-widgets.index') }}" class="{{ request()->routeIs('admin.global-widgets.*') ? $mobileActive : $mobileInactive }}">⚙️ Widget Globali</a>
                    <a href="{{ route('admin.contatti.index') }}" class="{{ request()->routeIs('admin.contatti.*') ? $mobileActive : $mobileInactive }}">📧 Form Contatti</a>
                </div>
                @endif

                <div class="border-t border-zinc-800 pt-3">
                    @if($user->is_super_admin)
                    <a href="{{ route('admin.filemanager') }}" class="{{ request()->routeIs('admin.filemanager') ? $mobileActive : $mobileInactive }}">📁 File Manager</a>
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? $mobileActive : $mobileInactive }}">👥 Amministratori</a>
                    @endif

                    <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? $mobileActive : $mobileInactive }}">⚙️ Configurazione</a>
                </div>
                
                <div class="border-t border-zinc-800 pt-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-4 py-2.5 rounded-lg text-sm font-black text-rose-400 hover:text-white hover:bg-rose-950/30">🚪 Esci</button>
                    </form>
                </div>
            @endif
        </nav>
    </div>
</div>
