<x-guest-layout>
    @php
        $isEn = app()->getLocale() === 'en';
    @endphp

    <div class="mb-6 text-center">
        <h2 class="text-base sm:text-lg font-black text-white uppercase tracking-tight">
            {{ $isEn ? 'Reset Password' : 'Reimpostazione Password' }}
        </h2>
        <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
            {{ $isEn ? 'Forgot your password? Enter your email address to receive a reset link.' : 'Hai dimenticato la password? Inserisci la tua email per ricevere il link di reimpostazione.' }}
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-5 p-3.5 bg-emerald-950/50 border border-emerald-800/60 rounded-2xl text-xs font-bold text-emerald-400 flex items-center gap-2 shadow-sm">
            <span class="text-sm">✓</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
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
                       placeholder="nome@azienda.it" 
                       class="w-full bg-zinc-950/70 border border-zinc-700/80 rounded-2xl pl-10 pr-4 py-3 text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition shadow-inner @error('email') border-red-500 @enderror" />
            </div>
            @error('email')
                <p class="text-rose-400 text-xs mt-1.5 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-300 active:scale-[0.99] text-slate-950 font-black uppercase tracking-wider text-xs py-3.5 px-6 rounded-2xl shadow-lg shadow-yellow-400/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>{{ $isEn ? 'Send Reset Link' : 'Invia Link di Reimpostazione' }}</span>
                <span class="text-sm font-bold">→</span>
            </button>
        </div>

        <div class="text-center pt-3 border-t border-zinc-800/80 mt-4">
            <a href="{{ route('login') }}" class="text-xs font-bold text-zinc-400 hover:text-yellow-400 transition">
                ← {{ $isEn ? 'Back to Login' : 'Torna al Login' }}
            </a>
        </div>
    </form>
</x-guest-layout>
