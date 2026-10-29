@php
    $consentEnabled = ($global_settings['cookie_consent_enabled'] ?? '0') === '1';
    $consentText = $global_settings['cookie_consent_text'] ?? 'Questo sito utilizza i cookie per migliorare l\'esperienza di navigazione.';
@endphp

@if($consentEnabled)
<!-- Wrapper dello Stato (Senza posizionamento fisso per non bloccare i click) -->
<div id="cookie-consent-master-wrapper" 
     x-data="{ 
        visible: false,
        showSettings: false,
        hasCookie: document.cookie.includes('cookie_consent='),
        prefs: {
            analytics: true,
            marketing: true
        },
        init() {
            if (!this.hasCookie) {
                setTimeout(() => { this.visible = true; }, 400);
            }
        },
        acceptAll() {
            this.setCookie('accepted');
            this.close();
        },
        rejectAll() {
            this.setCookie('rejected');
            this.close();
        },
        savePrefs() {
            this.setCookie(encodeURIComponent(JSON.stringify(this.prefs)));
            this.close();
        },
        setCookie(val) {
            document.cookie = 'cookie_consent=' + val + '; expires=Fri, 31 Dec 2030 23:59:59 GMT; path=/; SameSite=Lax';
            this.hasCookie = true;
        },
        close() {
            this.visible = false;
            this.showSettings = false;
            setTimeout(() => { window.location.reload(); }, 100);
        }
    }"
>
    <!-- 1. Banner Principale -->
    <div x-show="visible && !showSettings"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed bottom-6 left-4 right-4 md:left-auto md:right-8 md:max-w-md z-[2147483647]"
         style="display: none; position: fixed !important; bottom: 1.5rem !important; right: 2rem !important; z-index: 2147483647 !important; pointer-events: auto !important;"
    >
        <div class="bg-white rounded-3xl shadow-[0_30px_70px_-15px_rgba(0,0,0,0.5)] ring-1 ring-gray-900/10 p-6 sm:p-8 border-t-8 border-indigo-600 relative overflow-hidden pointer-events-auto">
            <div class="absolute top-2 right-2">
                <button @click="visible = false" class="text-gray-300 hover:text-gray-500 p-2 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="flex flex-col gap-4">
                <div class="bg-indigo-50 w-12 h-12 rounded-2xl flex items-center justify-center border border-indigo-100">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                    </svg>
                </div>
                
                <div>
                    <h4 class="text-lg font-black text-gray-900 mb-2 leading-tight">Privacy & Cookies</h4>
                    <p class="text-sm leading-relaxed text-gray-600 mb-6">
                        {{ $consentText }} Utilizziamo tecnologie avanzate per offrirti un'esperienza su misura. 
                        <button @click="showSettings = true" class="text-indigo-600 font-bold hover:underline">Impostazioni</button>.
                    </p>
                    
                    <div class="flex flex-col gap-2">
                        <button @click="acceptAll()" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold text-sm uppercase tracking-widest hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all active:scale-[0.97]">Accetta tutto</button>
                        <button @click="rejectAll()" class="w-full text-gray-400 py-2 font-bold text-[10px] uppercase tracking-widest hover:text-gray-700 transition-colors">Rifiuta non necessari</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Modal Personalizzazione -->
    <template x-if="showSettings">
        <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 pointer-events-auto" style="z-index: 2147483647 !important;">
            <!-- Overlay Backdrop -->
            <div x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 class="fixed inset-0 bg-gray-900/90 backdrop-blur-xl"
                 style="position: fixed !important; z-index: 2147483646 !important;"
                 @click="showSettings = false"></div>

            <!-- Modal Content -->
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-10"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="bg-white rounded-[2.5rem] shadow-2xl p-8 sm:p-12 w-full max-w-2xl relative z-[2147483647] border border-white/20"
            >
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">Centro Privacy</h3>
                        <p class="text-gray-400 text-xs mt-1 uppercase tracking-widest">Gestione granulare del consenso</p>
                    </div>
                    <button @click="showSettings = false" class="bg-gray-100 text-gray-500 hover:text-gray-900 p-3 rounded-full transition-all">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="space-y-4 sm:space-y-5 max-h-[50vh] overflow-y-auto px-1 custom-scrollbar">
                    <!-- Necessari -->
                    <div class="p-5 rounded-3xl bg-gray-50 border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-bold text-gray-900 flex items-center gap-3">
                                <span class="bg-white p-2 rounded-xl shadow-sm text-lg">🛡️</span>
                                <div>
                                    <span class="block">Cookie Tecnici</span>
                                    <span class="block text-[10px] text-indigo-600 uppercase font-black tracking-widest">Sempre Attivi</span>
                                </div>
                            </label>
                            <div class="w-12 h-6 bg-indigo-600/20 rounded-full relative opacity-50"><div class="absolute right-1 top-1 bg-indigo-600 w-4 h-4 rounded-full"></div></div>
                        </div>
                        <p class="text-[11px] text-gray-400 leading-relaxed ml-11">Indispensabili per il login, la sicurezza e le funzioni base del sito.</p>
                    </div>

                    <!-- Analitici -->
                    <div class="p-5 rounded-3xl bg-white border border-gray-100 hover:border-indigo-100 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-bold text-gray-900 flex items-center gap-3">
                                <span class="bg-gray-50 p-2 rounded-xl shadow-sm group-hover:bg-indigo-50 text-lg transition-colors">📈</span>
                                <span>Statistiche e Analytics</span>
                            </label>
                            <button @click="prefs.analytics = !prefs.analytics" 
                                    :class="prefs.analytics ? 'bg-indigo-600' : 'bg-gray-200'"
                                    class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-all duration-300">
                                <span :class="prefs.analytics ? 'translate-x-5' : 'translate-x-0'" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-lg transition duration-300 ease-in-out"></span>
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-400 leading-relaxed ml-11">Ci permettono di misurare l'afflusso e migliorare i contenuti più apprezzati.</p>
                    </div>

                    <!-- Marketing -->
                    <div class="p-5 rounded-3xl bg-white border border-gray-100 hover:border-indigo-100 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-bold text-gray-900 flex items-center gap-3">
                                <span class="bg-gray-50 p-2 rounded-xl shadow-sm group-hover:bg-indigo-50 text-lg transition-colors">🎯</span>
                                <span>Marketing & Advertising</span>
                            </label>
                            <button @click="prefs.marketing = !prefs.marketing" 
                                    :class="prefs.marketing ? 'bg-indigo-600' : 'bg-gray-200'"
                                    class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-all duration-300">
                                <span :class="prefs.marketing ? 'translate-x-5' : 'translate-x-0'" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-lg transition duration-300 ease-in-out"></span>
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-400 leading-relaxed ml-11">Utilizzati per proporti offerte e contenuti personalizzati sui social media.</p>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t flex flex-col sm:flex-row items-center justify-between gap-6">
                    <button @click="rejectAll()" class="text-xs font-black text-gray-300 hover:text-gray-900 uppercase tracking-[0.2em] transition-colors">Rifiuta tutti</button>
                    <div class="flex gap-4 w-full sm:w-auto">
                        <button @click="acceptAll()" class="flex-1 sm:flex-none text-gray-900 px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest border-2 border-gray-900 hover:bg-gray-50 transition-all">Accetta tutto</button>
                        <button @click="savePrefs()" class="flex-1 sm:flex-none bg-indigo-600 text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 shadow-[0_15px_35px_rgba(79,70,229,0.4)] transition-all">Salva scelte</button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- 3. Trigger Flottante (Revoca) - POSIZIONATO A SINISTRA -->
    <div x-show="!visible && !showSettings && hasCookie" 
         class="fixed bottom-6 left-6 pointer-events-auto"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 scale-50"
         x-transition:enter-end="opacity-100 scale-100"
         style="display: none; position: fixed !important; bottom: 1.5rem !important; left: 1.5rem !important; right: auto !important; z-index: 2147483647 !important;"
    >
        <button @click="visible = true; showSettings = true" 
                class="bg-gray-900 text-white p-3.5 rounded-full shadow-[0_10px_30px_rgba(0,0,0,0.35)] hover:bg-indigo-600 hover:scale-110 active:scale-90 transition-all border-2 border-white cursor-pointer"
                title="Privacy & Cookie">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
            </svg>
        </button>
    </div>

    <!-- Funzioni Globali per il Footer e altri link -->
    <script>
        window.openCookiePreferences = function() {
            const root = document.getElementById('cookie-consent-master-wrapper');
            if (root) {
                const data = window.Alpine?.$data(root);
                if (data) {
                    data.visible = true;
                    data.showSettings = true;
                }
            }
        };
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
</div>
@endif
