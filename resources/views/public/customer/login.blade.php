@extends('public.layouts.main')

@section('title', 'Area Personale - ' . config('app.name'))

@section('content')
<div class="bg-zinc-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8" x-data="{
    tab: '{{ old('register_mode') ? 'register' : ($activeTab ?? 'login') }}',
    showPass: false,
    showRegisterPass: false,
    regPass: '',
    passStrength: 0,
    hasLength: false,
    hasUpper: false,
    hasNumber: false,
    hasSymbol: false,
    checkPassword() {
        const p = this.regPass || '';
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
        
        // Mescola i caratteri
        this.regPass = pass.split('').sort(() => 0.5 - Math.random()).join('');
        this.checkPassword();
        
        const confirmInput = document.getElementById('reg_password_confirmation');
        if (confirmInput) {
            confirmInput.value = this.regPass;
        }
    }
}">
    <div class="max-w-xl mx-auto">
        
        <!-- Breadcrumb & Top Bar -->
        <div class="flex items-center justify-between text-xs font-semibold text-zinc-500 mb-6 uppercase tracking-wider">
            <a href="{{ route('public.home') }}" class="hover:text-black flex items-center gap-1 transition-colors">
                <span>&larr;</span> <span>Torna al Catalogo</span>
            </a>
            <span class="text-zinc-400">Area Personale</span>
        </div>

        <!-- Feedback Alert Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-sm font-semibold rounded-none shadow-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 text-sm font-semibold rounded-none shadow-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any() && !old('register_mode'))
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 text-sm font-medium rounded-none shadow-xs">
                <div class="font-bold mb-1">Attenzione:</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Principale -->
        <div class="bg-white border border-zinc-200 shadow-xl overflow-hidden rounded-none">
            
            <!-- Header Card Bicap -->
            <div class="bg-zinc-950 p-6 sm:p-8 text-white relative border-b border-zinc-800">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-[#fedb00] text-black text-[10px] font-black uppercase tracking-wider mb-2">
                            AREA CLIENTI BICAP
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
                            Area Personale
                        </h1>
                        <p class="text-xs sm:text-sm text-zinc-400 mt-1">
                            Accedi per gestire i tuoi ordini, spedizioni e dati di fatturazione
                        </p>
                    </div>
                    <div class="hidden sm:block">
                        <div class="w-14 h-14 bg-zinc-900 border border-zinc-800 flex items-center justify-center text-[#fedb00]">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tabs Switcher -->
                <div class="flex border-t border-zinc-800 mt-6 pt-4 gap-4">
                    <button type="button" 
                            @click="tab = 'login'" 
                            :class="tab === 'login' ? 'text-[#fedb00] border-b-2 border-[#fedb00] font-black' : 'text-zinc-400 hover:text-white border-b-2 border-transparent font-bold'"
                            class="pb-2 text-xs uppercase tracking-wider transition-colors cursor-pointer">
                        Hai già un Account? Accedi
                    </button>
                    <button type="button" 
                            @click="tab = 'register'" 
                            :class="tab === 'register' ? 'text-[#fedb00] border-b-2 border-[#fedb00] font-black' : 'text-zinc-400 hover:text-white border-b-2 border-transparent font-bold'"
                            class="pb-2 text-xs uppercase tracking-wider transition-colors cursor-pointer">
                        Nuovo Cliente? Registrati
                    </button>
                </div>
            </div>

            <!-- TAB 1: LOGIN FORM -->
            <div x-show="tab === 'login'" class="p-6 sm:p-8 space-y-6">
                <form action="{{ route('public.account.login.submit') }}" method="POST" class="space-y-5">
                    @csrf
                    @if(request()->filled('redirect_to'))
                        <input type="hidden" name="redirect_to" value="{{ request()->input('redirect_to') }}">
                    @endif

                    <!-- Email Input -->
                    <div>
                        <label for="login_email" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1.5">
                            Indirizzo E-mail *
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                            </span>
                            <input type="email" 
                                   id="login_email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username" 
                                   placeholder="nome@dominio.it" 
                                   class="w-full bg-white border border-zinc-300 rounded-none pl-9 pr-3 py-2.5 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none placeholder-zinc-400">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="login_password" class="block text-xs font-bold uppercase tracking-wider text-zinc-700">
                                Password *
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" target="_blank" class="text-xs text-zinc-500 hover:text-black underline transition-colors">
                                    Password dimenticata?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input :type="showPass ? 'text' : 'password'" 
                                   id="login_password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="••••••••" 
                                   class="w-full bg-white border border-zinc-300 rounded-none pl-9 pr-10 py-2.5 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none placeholder-zinc-400">
                            <button type="button" 
                                    @click="showPass = !showPass" 
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-black cursor-pointer">
                                <span class="text-xs font-semibold" x-text="showPass ? 'Nascondi' : 'Mostra'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Ricordami & Submit -->
                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-zinc-700 select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded-none border-zinc-300 text-black focus:ring-black cursor-pointer">
                            <span>Rimani collegato</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-3.5 px-6 bg-black hover:bg-zinc-800 text-white font-bold text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer border-0 shadow-md">
                            <span>Accedi all'Area Personale</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB 2: REGISTER FORM -->
            <div x-show="tab === 'register'" class="p-6 sm:p-8 space-y-6" style="display: none;">
                @if(isset($errors) && $errors->any() && old('register_mode'))
                    <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 text-xs font-medium">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('public.account.register.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="register_mode" value="1">
                    @if(request()->filled('redirect_to'))
                        <input type="hidden" name="redirect_to" value="{{ request()->input('redirect_to') }}">
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="reg_nome" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                Nome *
                            </label>
                            <input type="text" id="reg_nome" name="nome" value="{{ old('nome') }}" required
                                   placeholder="Mario"
                                   class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                        </div>
                        <div>
                            <label for="reg_cognome" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                Cognome *
                            </label>
                            <input type="text" id="reg_cognome" name="cognome" value="{{ old('cognome') }}" required
                                   placeholder="Rossi"
                                   class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="reg_email" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                            Indirizzo E-mail *
                        </label>
                        <input type="email" id="reg_email" name="email" value="{{ old('email') }}" required
                               placeholder="mario.rossi@esempio.it"
                               class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                    </div>

                    <div>
                        <label for="reg_telefono" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                            Telefono / Cellulare *
                        </label>
                        <input type="text" id="reg_telefono" name="telefono" value="{{ old('telefono') }}" required
                               placeholder="+39 340 1234567"
                               class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                    </div>

                    <!-- Password con Generatore -->
                    <div class="pt-2 border-t border-zinc-200">
                        <div class="flex items-center justify-between mb-1">
                            <label for="reg_password" class="block text-xs font-bold uppercase tracking-wider text-zinc-700">
                                Scegli una Password *
                            </label>
                            <button type="button" 
                                    @click="generatePassword()" 
                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-700 hover:text-black transition-colors cursor-pointer">
                                <span>⚡ Genera Password Sicura</span>
                            </button>
                        </div>
                        <div class="relative">
                            <input :type="showRegisterPass ? 'text' : 'password'" 
                                   id="reg_password" 
                                   name="password" 
                                   x-model="regPass"
                                   @input="checkPassword()"
                                   required 
                                   placeholder="Minimo 8 caratteri con maiuscola, numero e simbolo"
                                   class="w-full bg-white border border-zinc-300 rounded-none pl-3 pr-10 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                            <button type="button" 
                                    @click="showRegisterPass = !showRegisterPass" 
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-black cursor-pointer">
                                <span class="text-xs font-semibold" x-text="showRegisterPass ? 'Nascondi' : 'Mostra'"></span>
                            </button>
                        </div>

                        <!-- Indicatori di complessità password -->
                        <div class="mt-2 p-3 bg-zinc-50 border border-zinc-200 text-xs">
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
                        <label for="reg_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                            Conferma Password *
                        </label>
                        <input type="password" 
                               id="reg_password_confirmation" 
                               name="password_confirmation" 
                               required 
                               placeholder="Ripeti la password"
                               class="w-full bg-white border border-zinc-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none">
                    </div>

                    <div class="pt-3">
                        <button type="submit" 
                                class="w-full py-3.5 px-6 bg-[#fedb00] hover:bg-[#e6c600] text-black font-black text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer border-0 shadow-md">
                            <span>Registrati e Accedi</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Card con link al Portale B2B per Agenti / Rivenditori -->
            <div class="bg-zinc-100 p-4 sm:p-5 border-t border-zinc-200 text-center">
                <p class="text-xs text-zinc-600">
                    Sei un agente o rivenditore all'ingrosso autorizzato?
                    <a href="{{ route('login') }}" class="font-bold text-black underline hover:text-[#e35205] ml-1 transition-colors">
                        Accedi al Portale B2B &rarr;
                    </a>
                </p>
            </div>

        </div>

    </div>
</div>
@endsection
