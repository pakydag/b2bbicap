@php 
    $user = Auth::user();
    $shop_enabled = \App\Models\Setting::where('key', 'shop_enabled')->value('value') == '1'; 
    $booking_enabled = \App\Models\Setting::where('key', 'booking_enabled')->value('value') == '1';
    $englishEnabled = \App\Models\Setting::where('key', 'english_enabled')->value('value') == '1';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configurazione Sito') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        activeTab: 'generali' 
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200">

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Tabs Menu -->
                    <div class="flex border-b mb-6 border-gray-200">
                        <button @click="activeTab = 'generali'" :class="{'bg-indigo-50 border-t border-l border-r border-indigo-200 text-indigo-700 font-bold': activeTab === 'generali', 'text-gray-600 hover:text-indigo-600': activeTab !== 'generali'}" class="py-2 px-4 rounded-t-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            ⚙️ Generali & Logo
                        </button>

                        <button @click="activeTab = 'email'" :class="{'bg-indigo-50 border-t border-l border-r border-indigo-200 text-indigo-700 font-bold': activeTab === 'email', 'text-gray-600 hover:text-indigo-600': activeTab !== 'email'}" class="py-2 px-4 rounded-t-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 ml-1 text-sm">
                            📧 Email (SMTP)
                        </button>

                        @if($user->is_super_admin || ($shop_enabled && $user->can_manage_shop))
                        <button @click="activeTab = 'pagamenti'" :class="{'bg-indigo-50 border-t border-l border-r border-indigo-200 text-indigo-700 font-bold': activeTab === 'pagamenti', 'text-gray-600 hover:text-indigo-600': activeTab !== 'pagamenti'}" class="py-2 px-4 rounded-t-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 ml-1 text-sm">
                            🛍️ Pagamenti Shop
                        </button>
                        @endif

                        @if($user->is_super_admin || ($booking_enabled && $user->can_manage_booking))
                        <button @click="activeTab = 'pagamenti_booking'" :class="{'bg-indigo-50 border-t border-l border-r border-indigo-200 text-indigo-700 font-bold': activeTab === 'pagamenti_booking', 'text-gray-600 hover:text-indigo-600': activeTab !== 'pagamenti_booking'}" class="py-2 px-4 rounded-t-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 ml-1 text-sm">
                            🏨 Pagamenti Booking
                        </button>
                        @endif

                        @if($user->is_super_admin || $user->can_manage_agents)
                        <button @click="activeTab = 'pagamenti_b2b'" :class="{'bg-indigo-50 border-t border-l border-r border-indigo-200 text-indigo-700 font-bold': activeTab === 'pagamenti_b2b', 'text-gray-600 hover:text-indigo-600': activeTab !== 'pagamenti_b2b'}" class="py-2 px-4 rounded-t-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 ml-1 text-sm">
                            💳 Pagamenti B2B
                        </button>
                        @endif

                        @if($user->is_super_admin)
                        <button @click="activeTab = 'ai_spoki'" :class="{'bg-indigo-50 border-t border-l border-r border-indigo-200 text-indigo-700 font-bold': activeTab === 'ai_spoki', 'text-gray-600 hover:text-indigo-600': activeTab !== 'ai_spoki'}" class="py-2 px-4 rounded-t-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 ml-1 text-sm">
                            🤖 AI & Spoki
                        </button>

                        <button @click="activeTab = 'pixels'" :class="{'bg-indigo-50 border-t border-l border-r border-indigo-200 text-indigo-700 font-bold': activeTab === 'pixels', 'text-gray-600 hover:text-indigo-600': activeTab !== 'pixels'}" class="py-2 px-4 rounded-t-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 ml-1 text-sm">
                            📊 Pixel & Cookie
                        </button>

                        <button @click="activeTab = 'contatti'" :class="{'bg-indigo-50 border-t border-l border-r border-indigo-200 text-indigo-700 font-bold': activeTab === 'contatti', 'text-gray-600 hover:text-indigo-600': activeTab !== 'contatti'}" class="py-2 px-4 rounded-t-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 ml-1 text-sm">
                            📱 Contatti & Social
                        </button>
                        @endif
                    </div>

                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        
                        <!-- TAB: Generali -->
                        <div x-show="activeTab === 'generali'" class="space-y-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2">Impostazioni Visive Principali</h3>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Dominio Sito (Per Sitemap & SEO)</label>
                                <div class="flex items-center gap-4 max-w-2xl">
                                    <input type="url" name="site_domain" value="{{ old('site_domain', $settings['site_domain'] ?? config('app.url')) }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="https://www.elysionostuni.it">
                                    <button type="button" onclick="event.preventDefault(); document.getElementById('sitemap-form').submit();" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm whitespace-nowrap">
                                        Rigenera Sitemap
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Inserisci l'URL completo del sito, es: https://www.tuosito.it. Verrà utilizzato per generare correttamente i link nella sitemap.</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Logo Principale (Header e Email)</label>
                                @if(!empty($settings['site_logo']))
                                    <div class="mb-3 px-4 py-2 bg-gray-50 border rounded inline-block">
                                        <img src="{{ asset($settings['site_logo']) }}" alt="Logo Attuale" class="h-16 object-contain">
                                    </div>
                                @endif
                                <div class="flex mt-1 relative rounded-md shadow-sm max-w-lg">
                                    <input type="text" id="site_logo" name="site_logo" value="{{ old('site_logo', $settings['site_logo'] ?? '') }}" readonly placeholder="Seleziona Immagine..." class="shadow border rounded-l w-full py-2 px-3 bg-white focus:outline-none">
                                    <button type="button" id="btn-sfoglia-logo" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none">
                                        <span>Sfoglia...</span>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Scegli il file immagine (PNG, JPG o SVG) dal File Manager.</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Favicon (Icona Browser)</label>
                                @if(!empty($settings['site_favicon']))
                                    <div class="mb-3 px-4 py-2 bg-gray-50 border rounded inline-block">
                                        <img src="{{ asset($settings['site_favicon']) }}" alt="Favicon Attuale" class="h-8 w-8 object-contain">
                                    </div>
                                @endif
                                <div class="flex mt-1 relative rounded-md shadow-sm max-w-lg">
                                    <input type="text" id="site_favicon" name="site_favicon" value="{{ old('site_favicon', $settings['site_favicon'] ?? '') }}" readonly placeholder="Seleziona Icona..." class="shadow border rounded-l w-full py-2 px-3 bg-white focus:outline-none">
                                    <button type="button" id="btn-sfoglia-favicon" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none">
                                        <span>Sfoglia...</span>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Scegli il file immagine (solitamente .ico o .png 32x32) dal File Manager.</p>
                            </div>

                            @if($user->is_super_admin)
                                <div class="mt-8 border-t pt-6 border-gray-100">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-1.998A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                                        </svg>
                                        Opzioni Super Amministratore
                                    </h3>
                                    <div class="space-y-4">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="shop_enabled" value="1" class="sr-only peer" {{ (isset($settings['shop_enabled']) && $settings['shop_enabled'] == '1') ? 'checked' : '' }}>
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                            <span class="ms-3 text-sm font-medium text-gray-900">Abilita Modulo Shop B2B</span>
                                        </label>
                                        <p class="text-xs text-gray-500">Attivando questa opzione, un nuovo menu 'Shop Online' apparirà nell'amministrazione.</p>

                                        <label class="inline-flex items-center cursor-pointer mt-4">
                                            <input type="checkbox" name="booking_enabled" value="1" class="sr-only peer" {{ (isset($settings['booking_enabled']) && $settings['booking_enabled'] == '1') ? 'checked' : '' }}>
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                            <span class="ms-3 text-sm font-medium text-gray-900">Abilita Modulo Booking</span>
                                        </label>
                                        <p class="text-xs text-gray-500">Attivando questa opzione, un nuovo menu 'Booking' apparirà nell'amministrazione.</p>

                                        <label class="inline-flex items-center cursor-pointer mt-4 border-t pt-4 w-full">
                                            <input type="checkbox" name="english_enabled" value="1" class="sr-only peer" {{ (isset($settings['english_enabled']) && $settings['english_enabled'] == '1') ? 'checked' : '' }}>
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                            <span class="ms-3 text-sm font-medium text-gray-950 font-bold">Aggiungi Inglese (Abilita Doppia Lingua ITA/ENG)</span>
                                        </label>
                                        <p class="text-xs text-gray-500">Se abilitato, mostrerà le bandierine per il cambio lingua sul sito e i doppi campi nel gestionale.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- TAB: Email -->
                        <div x-show="activeTab === 'email'" style="display: none;" class="space-y-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2">Configurazione Provider di Posta (SMTP)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Protocollo (Mailer)</label>
                                    <select name="mail_mailer" class="shadow border rounded w-full py-2 px-3 focus:outline-none">
                                        <option value="smtp" {{ (old('mail_mailer', $settings['mail_mailer'] ?? 'smtp') == 'smtp') ? 'selected' : '' }}>SMTP</option>
                                        <option value="log" {{ (old('mail_mailer', $settings['mail_mailer'] ?? '') == 'log') ? 'selected' : '' }}>Log (Solo Test)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Indirizzo Server (Host)</label>
                                    <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="es. smtp.gmail.com">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Porta Server</label>
                                    <input type="text" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="587">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                                    <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                                    <input type="password" name="mail_password" value="{{ old('mail_password', $settings['mail_password'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Mittente</label>
                                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Nome Mittente</label>
                                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Pagamenti Shop -->
                        @if($user->is_super_admin || $shop_enabled)
                        <div x-show="activeTab === 'pagamenti'" style="display: none;" class="space-y-6">
                            <div class="bg-gray-50 p-4 border rounded-md mb-6">
                                <h3 class="text-md font-bold text-gray-800 mb-3 border-b pb-2">Metodi di Pagamento SHOP</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="payment_stripe_enabled" value="1" class="sr-only peer" {{ (isset($settings['payment_stripe_enabled']) && $settings['payment_stripe_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Carta di Credito (Stripe)</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="payment_paypal_enabled" value="1" class="sr-only peer" {{ (isset($settings['payment_paypal_enabled']) && $settings['payment_paypal_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">PayPal</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="payment_bonifico_enabled" value="1" class="sr-only peer" {{ (isset($settings['payment_bonifico_enabled']) && $settings['payment_bonifico_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Bonifico Bancario</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="payment_contrassegno_enabled" value="1" class="sr-only peer" {{ (isset($settings['payment_contrassegno_enabled']) && $settings['payment_contrassegno_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Pagamento alla Consegna (Contrassegno)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- TAB: Pagamenti Booking -->
                        @if($user->is_super_admin || $booking_enabled)
                        <div x-show="activeTab === 'pagamenti_booking'" style="display: none;" class="space-y-6">
                            <div class="bg-gray-50 p-4 border rounded-md mb-6">
                                <h3 class="text-md font-bold text-gray-800 mb-3 border-b pb-2">Metodi di Pagamento BOOKING</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="booking_payment_stripe_enabled" value="1" class="sr-only peer" {{ (isset($settings['booking_payment_stripe_enabled']) && $settings['booking_payment_stripe_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Carta di Credito (Stripe)</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="booking_payment_paypal_enabled" value="1" class="sr-only peer" {{ (isset($settings['booking_payment_paypal_enabled']) && $settings['booking_payment_paypal_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">PayPal</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="booking_payment_bonifico_enabled" value="1" class="sr-only peer" {{ (isset($settings['booking_payment_bonifico_enabled']) && $settings['booking_payment_bonifico_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Bonifico Bancario</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- TAB: Pagamenti B2B -->
                        @if($user->is_super_admin || $user->can_manage_agents)
                        <div x-show="activeTab === 'pagamenti_b2b'" style="display: none;" class="space-y-6">
                            <div class="bg-gray-50 p-4 border rounded-md mb-6">
                                <h3 class="text-md font-bold text-gray-800 mb-3 border-b pb-2 text-indigo-600 uppercase">Metodi di Pagamento B2B</h3>
                                <p class="text-xs text-gray-500 mb-4">Abilitando questi metodi, potrai inviare link di pagamento diretti dalla gestione ordini B2B.</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="b2b_payment_stripe_enabled" value="1" class="sr-only peer" {{ (isset($settings['b2b_payment_stripe_enabled']) && $settings['b2b_payment_stripe_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Carta di Credito (Stripe)</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="b2b_payment_paypal_enabled" value="1" class="sr-only peer" {{ (isset($settings['b2b_payment_paypal_enabled']) && $settings['b2b_payment_paypal_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">PayPal</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="b2b_payment_bonifico_enabled" value="1" class="sr-only peer" {{ (isset($settings['b2b_payment_bonifico_enabled']) && $settings['b2b_payment_bonifico_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Bonifico Bancario</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- TAB: AI & Spoki -->
                        <div x-show="activeTab === 'ai_spoki'" style="display: none;" class="space-y-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2">Integrazioni AI & Automazioni</h3>
                            
                            @if($user->is_super_admin)
                                <div class="bg-gray-50 p-4 border rounded-md mb-6">
                                    <h4 class="text-sm font-bold text-gray-800 mb-3 border-b pb-2">Moduli Sistema</h4>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="spoki_enabled" value="1" class="sr-only peer" {{ (isset($settings['spoki_enabled']) && $settings['spoki_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Abilita Sincronizzazione Spoki (WhatsApp)</span>
                                    </label>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Vapi AI Secret Key</label>
                                    <input type="password" name="vapi_key" value="{{ old('vapi_key', $settings['vapi_key'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="vapi-secret-...">
                                    <p class="text-xs text-gray-500 mt-1">Chiave segreta per l'integrazione con l'assistente vocale Vapi.ai.</p>
                                </div>
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Vapi Assistant ID</label>
                                    <input type="text" name="vapi_assistant_id" value="{{ old('vapi_assistant_id', $settings['vapi_assistant_id'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="5a05fa0e-...">
                                    <p class="text-xs text-gray-500 mt-1">L'ID dell'assistente configurato su Vapi.ai.</p>
                                </div>
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Spoki API Key</label>
                                    <input type="password" name="spoki_key" value="{{ old('spoki_key', $settings['spoki_key'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="Spoki API Key...">
                                    <p class="text-xs text-gray-500 mt-1">Chiave API per inviare messaggi e sincronizzare contatti su Spoki.</p>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Pixel & Cookie -->
                        <div x-show="activeTab === 'pixels'" style="display: none;" class="space-y-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2">Tracking & Tracciamento</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Google Analytics ID (GT-XXXXX o G-XXXXX)</label>
                                    <input type="text" name="tracking_ga_analytics" value="{{ old('tracking_ga_analytics', $settings['tracking_ga_analytics'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="es. G-XXXXXXXXXX">
                                    <p class="text-xs text-gray-500 mt-1">Inserisci solo l'ID di tracciamento o l'ID della proprietà (es: G-XXXXX).</p>
                                </div>
                                
                                <div class="col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Google Tag Manager ID (GTM)</label>
                                    <input type="text" name="tracking_gtm" value="{{ old('tracking_gtm', $settings['tracking_gtm'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="es. GTM-XXXXXXX">
                                    <p class="text-xs text-gray-500 mt-1">Inserisci solo l'ID del contenitore GTM.</p>
                                </div>
                                
                                <div class="col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Facebook Pixel ID</label>
                                    <input type="text" name="tracking_facebook_pixel" value="{{ old('tracking_facebook_pixel', $settings['tracking_facebook_pixel'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="es. 1234567890">
                                    <p class="text-xs text-gray-500 mt-1">Inserisci solo l'ID del Pixel di Facebook.</p>
                                </div>
                            </div>

                            <div class="mt-8 border-t pt-6 border-gray-100">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.647.166 1.317.166 2 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.683.057-1.353.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Gestione Consenso Cookie (GDPR)
                                </h3>
                                <div class="bg-gray-50 p-4 border rounded-md">
                                    <label class="inline-flex items-center cursor-pointer mb-4">
                                        <input type="checkbox" name="cookie_consent_enabled" value="1" class="sr-only peer" {{ (isset($settings['cookie_consent_enabled']) && $settings['cookie_consent_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Abilita Banner Cookie Consent</span>
                                    </label>
                                    
                                    <div class="mt-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Testo Banner Informativo</label>
                                        <textarea name="cookie_consent_text" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none text-sm">{{ old('cookie_consent_text', $settings['cookie_consent_text'] ?? 'Questo sito utilizza i cookie per migliorare l\'esperienza di navigazione. Continuando accetti il loro utilizzo.') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-8 border-t pt-6 border-gray-100">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    Accessibilità
                                </h3>
                                <div class="bg-gray-50 p-4 border rounded-md">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="accessibility_panel_enabled" value="1" class="sr-only peer" {{ (!isset($settings['accessibility_panel_enabled']) || $settings['accessibility_panel_enabled'] == '1') ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Abilita Widget Accessibilità sul Sito</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Contatti & Social -->
                        <div x-show="activeTab === 'contatti'" style="display: none;" class="space-y-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2">Informazioni Aziendali & Social</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Indirizzo Sede</label>
                                    <input type="text" name="company_address" value="{{ old('company_address', $settings['company_address'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="Via Roma 1, 00100 Roma (RM)">
                                </div>
                                
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Telefono Contatto</label>
                                    <input type="text" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="+39 06 1234567">
                                </div>

                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Pubblica (Footer)</label>
                                    <input type="email" name="company_email_public" value="{{ old('company_email_public', $settings['company_email_public'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="info@azienda.it">
                                </div>

                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Partita IVA / Codice Fiscale</label>
                                    <input type="text" name="company_vat" value="{{ old('company_vat', $settings['company_vat'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="01234567890">
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Breve Descrizione Footer</label>
                                    <textarea name="footer_description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none text-sm" placeholder="Siamo un'azienda leader nel settore... (max 200 caratteri)">{{ old('footer_description', $settings['footer_description'] ?? '') }}</textarea>
                                </div>

                                @if($englishEnabled)
                                    <div class="col-span-2 bg-indigo-50/30 p-3 rounded border border-indigo-100/50">
                                        <label class="block text-indigo-950 text-sm font-bold mb-2">Breve Descrizione Footer [INGLESE]</label>
                                        <textarea name="footer_description_en" rows="3" class="shadow appearance-none border border-indigo-300 rounded w-full py-2 px-3 focus:outline-none text-sm bg-white" placeholder="Enter short footer description in English...">{{ old('footer_description_en', $settings['footer_description_en'] ?? '') }}</textarea>
                                    </div>
                                @endif

                                <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
                                    <h4 class="col-span-2 text-md font-bold text-gray-800">Link Social Network</h4>
                                    
                                    <div>
                                        <label class="block text-gray-700 text-xs font-bold mb-1">Facebook URL</label>
                                        <input type="text" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" class="shadow appearance-none border rounded w-full py-1 px-2 focus:outline-none text-sm" placeholder="https://facebook.com/box">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-gray-700 text-xs font-bold mb-1">Instagram URL</label>
                                        <input type="text" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" class="shadow appearance-none border rounded w-full py-1 px-2 focus:outline-none text-sm" placeholder="https://instagram.com/box">
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-xs font-bold mb-1">LinkedIn URL</label>
                                        <input type="text" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}" class="shadow appearance-none border rounded w-full py-1 px-2 focus:outline-none text-sm" placeholder="https://linkedin.com/company/box">
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-xs font-bold mb-1">YouTube URL</label>
                                        <input type="text" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" class="shadow appearance-none border rounded w-full py-1 px-2 focus:outline-none text-sm" placeholder="https://youtube.com/c/box">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chiavi API (Visibili se almeno un modulo ha Stripe/PayPal attivo) -->
                        <div x-show="activeTab === 'pagamenti' || activeTab === 'pagamenti_booking' || activeTab === 'pagamenti_b2b'" style="display: none;" class="space-y-6 pt-6 border-t">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2">Configurazione API (Comuni a Shop e Booking)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- ... existing API keys content ... -->
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Stripe Publisheable Key</label>
                                    <input type="text" name="stripe_key" value="{{ old('stripe_key', $settings['stripe_key'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="pk_test_...">
                                </div>
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Stripe Secret Key</label>
                                    <input type="password" name="stripe_secret" value="{{ old('stripe_secret', $settings['stripe_secret'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="sk_test_...">
                                </div>
                            </div>
                            <!-- PayPal configuration -->
                            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2 mt-6">Configurazione PayPal</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">PayPal Client ID</label>
                                    <input type="text" name="paypal_client_id" value="{{ old('paypal_client_id', $settings['paypal_client_id'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="Client ID...">
                                </div>
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">PayPal Client Secret</label>
                                    <input type="password" name="paypal_secret" value="{{ old('paypal_secret', $settings['paypal_secret'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="Secret...">
                                </div>
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Modalità PayPal</label>
                                    <select name="paypal_mode" class="shadow border rounded w-full py-2 px-3 focus:outline-none">
                                        <option value="sandbox" {{ (old('paypal_mode', $settings['paypal_mode'] ?? 'sandbox') == 'sandbox') ? 'selected' : '' }}>Sandbox (Test)</option>
                                        <option value="live" {{ (old('paypal_mode', $settings['paypal_mode'] ?? '') == 'live') ? 'selected' : '' }}>Live (Produzione)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Bonifico configuration -->
                            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2 mt-6">Coordinate Bonifico Bancario</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Intestato A (Intestazione)</label>
                                    <input type="text" name="bonifico_intestazione" value="{{ old('bonifico_intestazione', $settings['bonifico_intestazione'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="Ragione Sociale...">
                                </div>
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Nome Banca</label>
                                    <input type="text" name="bonifico_banca" value="{{ old('bonifico_banca', $settings['bonifico_banca'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none" placeholder="Es. Intesa Sanpaolo">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">IBAN</label>
                                    <input type="text" name="bonifico_iban" value="{{ old('bonifico_iban', $settings['bonifico_iban'] ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none text-xl font-mono uppercase tracking-widest" placeholder="IT00...">
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-8 rounded shadow focus:outline-none focus:shadow-outline text-lg">
                                Salva Configurazione
                            </button>
                        </div>
                    </form>

                    <!-- Form per la rigenerazione manuale della Sitemap -->
                    <form id="sitemap-form" action="{{ route('admin.settings.sitemap') }}" method="POST" class="hidden">
                        @csrf
                    </form>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let fmActiveInput = null;

            document.addEventListener("DOMContentLoaded", function() {
                // Selezione Logo
                if (document.getElementById('btn-sfoglia-logo')) {
                    document.getElementById('btn-sfoglia-logo').addEventListener('click', (e) => {
                        e.preventDefault();
                        fmActiveInput = document.getElementById('site_logo');
                        window.open('{{ url('file-manager/fm-button') }}', 'fm', 'width=1400,height=800');
                    });
                }

                // Selezione Favicon
                if (document.getElementById('btn-sfoglia-favicon')) {
                    document.getElementById('btn-sfoglia-favicon').addEventListener('click', (e) => {
                        e.preventDefault();
                        fmActiveInput = document.getElementById('site_favicon');
                        window.open('{{ url('file-manager/fm-button') }}', 'fm', 'width=1400,height=800');
                    });
                }
            });

            // Callback richiamata dal pacchetto File Manager alexusmai
            function fmSetLink($url) {
                const baseUrl = '{{ config('app.url') }}';
                let relativeUrl = $url.replace(baseUrl, '');
                
                if (!relativeUrl.startsWith('http')) {
                    let cleanPath = relativeUrl.replace(/^\/+/, '');
                    if (!cleanPath.startsWith('storage/')) {
                        relativeUrl = '/storage/' + cleanPath;
                    } else {
                        relativeUrl = '/' + cleanPath;
                    }
                }

                if (fmActiveInput) {
                    fmActiveInput.value = relativeUrl;
                }
            }
        </script>
    @endpush
</x-app-layout>
