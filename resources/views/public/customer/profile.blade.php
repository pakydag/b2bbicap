@extends('public.layouts.main')

@section('title', 'Dati Personali e Spedizione - Area Personale ' . config('app.name'))

@section('content')
<div class="bg-zinc-50 min-h-screen pt-8 pb-24" x-data="{
    isAzienda: {{ old('is_azienda', $customer->ragione_sociale ? '1' : '0') }} == '1',
    changePass: false,
    newPass: '',
    passStrength: 0,
    hasLength: false,
    hasUpper: false,
    hasNumber: false,
    hasSymbol: false,
    checkPassword() {
        const p = this.newPass || '';
        this.hasLength = p.length >= 8;
        this.hasUpper = /[A-Z]/.test(p);
        this.hasNumber = /[0-9]/.test(p);
        this.hasSymbol = /[^A-Za-z0-9]/.test(p);
        
        let score = 0;
        if (this.hasLength) score++;
        if (this.hasUpper) score++;
        if (this.hasNumber) score++;
        if (this.hasSymbol) score++;
        this.passStrength = score;
    },
    generatePassword() {
        const uppers = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        const lowers = 'abcdefghijkmnopqrstuvwxyz';
        const numbers = '23456789';
        const symbols = '!@#$%&*?+-=';
        
        let pass = '';
        pass += uppers.charAt(Math.floor(Math.random() * uppers.length));
        pass += lowers.charAt(Math.floor(Math.random() * lowers.length));
        pass += numbers.charAt(Math.floor(Math.random() * numbers.length));
        pass += symbols.charAt(Math.floor(Math.random() * symbols.length));
        
        const all = uppers + lowers + numbers + symbols;
        for (let i = 4; i < 12; i++) {
            pass += all.charAt(Math.floor(Math.random() * all.length));
        }
        
        this.newPass = pass.split('').sort(() => 0.5 - Math.random()).join('');
        this.checkPassword();
        
        const confirmInput = document.getElementById('new_password_confirmation');
        if (confirmInput) {
            confirmInput.value = this.newPass;
        }
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center text-xs font-semibold text-zinc-500 mb-6 uppercase tracking-wider">
            <a href="{{ route('public.home') }}" class="hover:text-black transition-colors">Home</a>
            <span class="mx-2 text-zinc-400">/</span>
            <span class="text-zinc-800">Area Personale</span>
            <span class="mx-2 text-zinc-400">/</span>
            <span class="text-[#e35205] font-bold">Dati Personali & Spedizione</span>
        </nav>

        <!-- Flash messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-sm font-semibold rounded-none shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 text-sm font-semibold rounded-none shadow-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 text-sm font-medium rounded-none shadow-xs">
                <div class="font-bold mb-1">Ci sono stati degli errori nel salvataggio:</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Banner Benvenuto Utente -->
        <div class="bg-zinc-950 text-white p-6 sm:p-8 rounded-none border-b-4 border-[#fedb00] shadow-lg mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-[#fedb00] text-black text-[10px] font-black uppercase tracking-wider mb-2">
                        PROFILO CLIENTE BICAP
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
                        Gestione Dati Personali
                    </h1>
                    <p class="text-xs sm:text-sm text-zinc-400 mt-1">
                        Modifica il tuo recapito di spedizione predefinito, le informazioni fiscali e la password di accesso.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('public.account.dashboard') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 text-xs font-bold uppercase tracking-wider border border-white/20 transition-colors">
                        <svg class="w-4 h-4 text-[#fedb00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>Torna ai Miei Ordini</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Grid Layout: Sidebar & Form -->
        <div class="lg:grid lg:grid-cols-12 lg:gap-x-8 items-start">
            
            <!-- Sidebar Nav Area Personale -->
            <aside class="lg:col-span-3 mb-8 lg:mb-0 space-y-4">
                <div class="bg-white border border-zinc-200 p-4 shadow-sm">
                    <h2 class="text-xs font-black uppercase tracking-wider text-zinc-400 mb-3 px-3">
                        Navigazione
                    </h2>
                    <nav class="space-y-1">
                        <a href="{{ route('public.account.dashboard') }}" 
                           class="text-zinc-700 hover:text-black hover:bg-zinc-100 group rounded-none px-3.5 py-2.5 flex items-center text-xs font-bold uppercase tracking-wider transition-colors">
                            <span class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-zinc-400 group-hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>I Miei Ordini</span>
                            </span>
                        </a>

                        <a href="{{ route('public.account.profile') }}" 
                           class="bg-zinc-950 text-[#fedb00] group rounded-none px-3.5 py-2.5 flex items-center text-xs font-black uppercase tracking-wider">
                            <span class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-[#fedb00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Dati Personali & Spedizione</span>
                            </span>
                        </a>
                    </nav>

                    <form method="POST" action="{{ route('public.account.logout') }}" class="mt-4 pt-4 border-t border-zinc-200">
                        @csrf
                        <button type="submit" class="w-full text-left text-rose-600 hover:text-rose-900 hover:bg-rose-50 px-3.5 py-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wider transition-colors cursor-pointer border-0">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Esci dall'Account</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content: Form Modifica Dati -->
            <div class="lg:col-span-9 space-y-8">
                
                <form action="{{ route('public.account.updateProfile') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- 1. I Tuoi Dati Anagrafici -->
                    <div class="bg-white border border-zinc-200 p-6 sm:p-8 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-zinc-200 pb-4 mb-6">
                            <div class="w-8 h-8 bg-black text-[#fedb00] flex items-center justify-center font-black text-sm">1</div>
                            <div>
                                <h2 class="text-base font-black text-black uppercase tracking-tight">I Tuoi Dati Anagrafici</h2>
                                <p class="text-xs text-zinc-500">I tuoi riferimenti di contatto personali</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="nome" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                    Nome *
                                </label>
                                <input type="text" id="nome" name="nome" 
                                       value="{{ old('nome', $customer->nome ?? (explode(' ', $user->name)[0] ?? '')) }}" 
                                       required 
                                       class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                            </div>

                            <div>
                                <label for="cognome" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                    Cognome *
                                </label>
                                <input type="text" id="cognome" name="cognome" 
                                       value="{{ old('cognome', $customer->cognome ?? (count(explode(' ', $user->name)) > 1 ? implode(' ', array_slice(explode(' ', $user->name), 1)) : '')) }}" 
                                       required 
                                       class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                    Indirizzo Email (Account)
                                </label>
                                <input type="email" id="email" value="{{ $user->email }}" readonly 
                                       class="w-full bg-zinc-100 border border-zinc-300 rounded-none px-3 py-2 text-sm text-zinc-500 cursor-not-allowed">
                                <p class="text-[11px] text-zinc-400 mt-1">L'email è associata al tuo account di accesso.</p>
                            </div>

                            <div>
                                <label for="telefono" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                    Telefono / Cellulare *
                                </label>
                                <input type="text" id="telefono" name="telefono" 
                                       value="{{ old('telefono', $customer->telefono ?? $user->phone) }}" 
                                       required 
                                       placeholder="+39 340 1234567"
                                       class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Indirizzo di Spedizione -->
                    <div class="bg-white border border-zinc-200 p-6 sm:p-8 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-zinc-200 pb-4 mb-6">
                            <div class="w-8 h-8 bg-black text-[#fedb00] flex items-center justify-center font-black text-sm">2</div>
                            <div>
                                <h2 class="text-base font-black text-black uppercase tracking-tight">Indirizzo di Spedizione Predefinito</h2>
                                <p class="text-xs text-zinc-500">Verrà utilizzato per precompilare la spedizione durante il checkout</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label for="nazione" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                    Nazione *
                                </label>
                                <select id="nazione" name="nazione" required 
                                        class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                                    <option value="Italia" {{ old('nazione', $customer->nazione ?? 'Italia') == 'Italia' ? 'selected' : '' }}>Italia</option>
                                    <option value="San Marino" {{ old('nazione', $customer->nazione) == 'San Marino' ? 'selected' : '' }}>San Marino</option>
                                    <option value="Francia" {{ old('nazione', $customer->nazione) == 'Francia' ? 'selected' : '' }}>Francia</option>
                                    <option value="Germania" {{ old('nazione', $customer->nazione) == 'Germania' ? 'selected' : '' }}>Germania</option>
                                    <option value="Spagna" {{ old('nazione', $customer->nazione) == 'Spagna' ? 'selected' : '' }}>Spagna</option>
                                    <option value="Svizzera" {{ old('nazione', $customer->nazione) == 'Svizzera' ? 'selected' : '' }}>Svizzera</option>
                                    <option value="Austria" {{ old('nazione', $customer->nazione) == 'Austria' ? 'selected' : '' }}>Austria</option>
                                    <option value="Belgio" {{ old('nazione', $customer->nazione) == 'Belgio' ? 'selected' : '' }}>Belgio</option>
                                    <option value="Olanda" {{ old('nazione', $customer->nazione) == 'Olanda' ? 'selected' : '' }}>Olanda</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="indirizzo" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                    Indirizzo e Numero Civico *
                                </label>
                                <input type="text" id="indirizzo" name="indirizzo" 
                                       value="{{ old('indirizzo', $customer->indirizzo) }}" 
                                       required 
                                       placeholder="Via Roma 123"
                                       class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                            </div>

                            <div>
                                <label for="citta" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                    Città *
                                </label>
                                <input type="text" id="citta" name="citta" 
                                       value="{{ old('citta', $customer->citta) }}" 
                                       required 
                                       placeholder="Milano"
                                       class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                            </div>

                            <div>
                                <label for="cap" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                    CAP *
                                </label>
                                <input type="text" id="cap" name="cap" 
                                       value="{{ old('cap', $customer->cap) }}" 
                                       required 
                                       placeholder="20100"
                                       class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- 3. Fatturazione Aziendale -->
                    <div class="bg-white border border-zinc-200 p-6 sm:p-8 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-zinc-200 pb-4 mb-6">
                            <div class="w-8 h-8 bg-black text-[#fedb00] flex items-center justify-center font-black text-sm">3</div>
                            <div>
                                <h2 class="text-base font-black text-black uppercase tracking-tight">Fatturazione Aziendale</h2>
                                <p class="text-xs text-zinc-500">Se acquisti per conto di un'azienda o con Partita IVA</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="flex items-start gap-3 p-3 bg-zinc-50 border border-zinc-200 cursor-pointer select-none">
                                <input type="checkbox" id="is_azienda" name="is_azienda" value="1" x-model="isAzienda" 
                                       class="w-4 h-4 rounded-none border-zinc-300 text-black focus:ring-black mt-0.5 cursor-pointer">
                                <div>
                                    <span class="block text-xs font-bold uppercase tracking-wider text-black">
                                        Richiedi Fattura Elettronica Aziendale (Partita IVA)
                                    </span>
                                    <span class="block text-xs text-zinc-500 mt-0.5">
                                        Seleziona per inserire Ragione Sociale, Partita IVA, Codice SDI e PEC.
                                    </span>
                                </div>
                            </label>

                            <div x-show="isAzienda" 
                                 x-transition 
                                 style="display: none;" 
                                 class="p-5 bg-zinc-50 border border-zinc-200 space-y-4">
                                
                                <div>
                                    <label for="ragione_sociale" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                        Ragione Sociale Azienda *
                                    </label>
                                    <input type="text" id="ragione_sociale" name="ragione_sociale" 
                                           value="{{ old('ragione_sociale', $customer->ragione_sociale) }}" 
                                           :required="isAzienda"
                                           placeholder="Es. Rossi Impianti S.r.l."
                                           class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="partita_iva" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                            Partita IVA *
                                        </label>
                                        <input type="text" id="partita_iva" name="partita_iva" 
                                               value="{{ old('partita_iva', $customer->partita_iva) }}" 
                                               :required="isAzienda"
                                               placeholder="IT01234567890"
                                               class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                                    </div>

                                    <div>
                                        <label for="codice_fiscale" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                            Codice Fiscale (Se diverso da P.IVA)
                                        </label>
                                        <input type="text" id="codice_fiscale" name="codice_fiscale" 
                                               value="{{ old('codice_fiscale', $customer->codice_fiscale) }}" 
                                               placeholder="Es. RSSMRA80A01H501U"
                                               class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                                    </div>

                                    <div>
                                        <label for="sdi" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                            Codice SDI Destinatario
                                        </label>
                                        <input type="text" id="sdi" name="sdi" 
                                               value="{{ old('sdi', $customer->sdi) }}" 
                                               placeholder="Es. M5UXCR1 (7 caratteri)"
                                               class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                                    </div>

                                    <div>
                                        <label for="pec" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                            PEC Aziendale
                                        </label>
                                        <input type="email" id="pec" name="pec" 
                                               value="{{ old('pec', $customer->pec) }}" 
                                               placeholder="azienda@pec.it"
                                               class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Preferenze e Sicurezza / Cambio Password -->
                    <div class="bg-white border border-zinc-200 p-6 sm:p-8 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-zinc-200 pb-4 mb-6">
                            <div class="w-8 h-8 bg-black text-[#fedb00] flex items-center justify-center font-black text-sm">4</div>
                            <div>
                                <h2 class="text-base font-black text-black uppercase tracking-tight">Preferenze Pagamento & Sicurezza</h2>
                                <p class="text-xs text-zinc-500">Metodo di pagamento predefinito e aggiornamento password</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="metodo_pagamento_preferito" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                    Metodo di Pagamento Preferito
                                </label>
                                <select id="metodo_pagamento_preferito" name="metodo_pagamento_preferito" 
                                        class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                                    <option value="">Nessuna preferenza (sceglierò al checkout)</option>
                                    <option value="stripe" {{ old('metodo_pagamento_preferito', $customer->metodo_pagamento_preferito) == 'stripe' ? 'selected' : '' }}>Carta di Credito (Stripe)</option>
                                    <option value="paypal" {{ old('metodo_pagamento_preferito', $customer->metodo_pagamento_preferito) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="bonifico" {{ old('metodo_pagamento_preferito', $customer->metodo_pagamento_preferito) == 'bonifico' ? 'selected' : '' }}>Bonifico Bancario</option>
                                    <option value="contrassegno" {{ old('metodo_pagamento_preferito', $customer->metodo_pagamento_preferito) == 'contrassegno' ? 'selected' : '' }}>Contrassegno (Pagamento alla Consegna)</option>
                                </select>
                            </div>

                            <!-- Sezione Cambio Password Opzionale -->
                            <div class="pt-4 border-t border-zinc-200">
                                <button type="button" 
                                        @click="changePass = !changePass" 
                                        class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-black hover:text-[#e35205] transition-colors cursor-pointer">
                                    <svg class="w-4 h-4 text-[#fedb00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span x-text="changePass ? 'Annulla Modifica Password' : 'Vuoi cambiare la tua Password? Clicca qui'"></span>
                                </button>

                                <div x-show="changePass" 
                                     x-transition 
                                     style="display: none;" 
                                     class="mt-4 p-5 bg-zinc-50 border border-zinc-200 space-y-4">
                                    
                                    <div>
                                        <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                            Password Attuale *
                                        </label>
                                        <input type="password" id="current_password" name="current_password" 
                                               placeholder="Inserisci la password attuale per autorizzare la modifica"
                                               class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                                    </div>

                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label for="new_password" class="block text-xs font-bold uppercase tracking-wider text-zinc-700">
                                                Nuova Password *
                                            </label>
                                            <button type="button" 
                                                    @click="generatePassword()" 
                                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-700 hover:text-black transition-colors cursor-pointer">
                                                <span>⚡ Genera Password Sicura</span>
                                            </button>
                                        </div>
                                        <input type="password" id="new_password" name="new_password" 
                                               x-model="newPass" 
                                               @input="checkPassword()"
                                               placeholder="Minimo 8 caratteri con maiuscola, numero e simbolo"
                                               class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                                        
                                        <!-- Indicatori di complessità password -->
                                        <div class="mt-2 p-3 bg-white border border-zinc-200 text-xs">
                                            <div class="flex items-center justify-between mb-1.5">
                                                <span class="font-bold text-zinc-700">Requisiti minimi di sicurezza:</span>
                                                <span class="font-bold" :class="{
                                                    'text-rose-500': passStrength < 2,
                                                    'text-amber-500': passStrength >= 2 && passStrength < 4,
                                                    'text-emerald-600': passStrength === 4
                                                }" x-text="passStrength === 4 ? 'Ottima' : (passStrength >= 2 ? 'Media' : 'Debole')"></span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-1 text-[11px] text-zinc-600">
                                                <div class="flex items-center gap-1" :class="hasLength ? 'text-emerald-700 font-bold' : 'text-zinc-500'">
                                                    <span x-text="hasLength ? '✓' : '○'"></span> <span>Almeno 8 caratteri</span>
                                                </div>
                                                <div class="flex items-center gap-1" :class="hasUpper ? 'text-emerald-700 font-bold' : 'text-zinc-500'">
                                                    <span x-text="hasUpper ? '✓' : '○'"></span> <span>Almeno 1 maiuscola</span>
                                                </div>
                                                <div class="flex items-center gap-1" :class="hasNumber ? 'text-emerald-700 font-bold' : 'text-zinc-500'">
                                                    <span x-text="hasNumber ? '✓' : '○'"></span> <span>Almeno 1 numero</span>
                                                </div>
                                                <div class="flex items-center gap-1" :class="hasSymbol ? 'text-emerald-700 font-bold' : 'text-zinc-500'">
                                                    <span x-text="hasSymbol ? '✓' : '○'"></span> <span>Almeno 1 simbolo (!@#$...)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="new_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                            Conferma Nuova Password *
                                        </label>
                                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                                               placeholder="Ripeti la nuova password"
                                               class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-4 pt-4">
                        <a href="{{ route('public.account.dashboard') }}" class="px-6 py-3 border border-zinc-300 hover:border-black text-black text-xs font-bold uppercase tracking-wider transition-colors">
                            Annulla
                        </a>
                        <button type="submit" 
                                class="px-8 py-3.5 bg-black hover:bg-zinc-800 text-white font-black text-xs uppercase tracking-wider transition-colors shadow-md cursor-pointer border-0">
                            Salva Tutte le Modifiche &rarr;
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>
</div>
@endsection
