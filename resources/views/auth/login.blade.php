<x-guest-layout>
    @php
        $isEn = app()->getLocale() === 'en';
    @endphp

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-xs font-bold text-emerald-400 bg-emerald-950/50 border border-emerald-800/60 p-3 rounded-xl" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-base sm:text-lg font-black text-white uppercase tracking-tight">
            {{ $isEn ? 'Sign In to Your Account' : 'Accedi al Portale' }}
        </h2>
        <p class="text-xs text-zinc-400 mt-1">
            {{ $isEn ? 'Enter your credentials to access the catalog & orders' : 'Inserisci le tue credenziali per visualizzare listini e catalogo' }}
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1.5">
                {{ $isEn ? 'Email Address' : 'Indirizzo E-mail' }}
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400 text-sm pointer-events-none">
                    ✉️
                </span>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username" 
                       placeholder="nome@azienda.it" 
                       class="auth-input block w-full rounded-2xl pl-10 pr-4 py-3 shadow-inner" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-400 font-bold" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-zinc-300">
                    {{ $isEn ? 'Password' : 'Password' }}
                </label>
                @if (Route::has('password.request'))
                    <a class="text-[11px] font-bold text-yellow-400 hover:text-yellow-300 transition" href="{{ route('password.request') }}">
                        {{ $isEn ? 'Forgot Password?' : 'Password dimenticata?' }}
                    </a>
                @endif
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400 text-sm pointer-events-none">
                    🔒
                </span>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password" 
                       placeholder="••••••••" 
                       class="auth-input block w-full rounded-2xl pl-10 pr-4 py-3 shadow-inner" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-400 font-bold" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" 
                       type="checkbox" 
                       class="rounded-lg border-zinc-700 bg-zinc-950 text-yellow-400 focus:ring-yellow-400 focus:ring-offset-zinc-900 cursor-pointer w-4 h-4" 
                       name="remember">
                <span class="ms-2 text-xs font-medium text-zinc-400 group-hover:text-zinc-200 transition">
                    {{ $isEn ? 'Remember me' : 'Ricordami' }}
                </span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-300 active:scale-[0.99] text-slate-950 font-black uppercase tracking-wider text-xs py-3.5 px-6 rounded-2xl shadow-lg shadow-yellow-400/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>{{ $isEn ? 'Sign In' : 'Accedi' }}</span>
                <span class="text-sm font-bold">→</span>
            </button>
        </div>
    </form>
</x-guest-layout>
