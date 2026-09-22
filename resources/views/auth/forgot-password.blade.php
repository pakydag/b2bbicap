<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-2">Reimpostazione Password</h2>
        <p class="text-xs text-gray-600 font-medium leading-relaxed">
            Hai dimenticato la password? Nessun problema. Inserisci il tuo indirizzo email e ti invieremo un link per reimpostarla e sceglierne una nuova.
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-bold text-emerald-800 flex items-center gap-2 shadow-sm">
            <span class="text-base">✓</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-black uppercase text-gray-700 tracking-wider mb-1">Indirizzo E-mail</label>
            <input id="email" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-yellow-400 focus:ring-yellow-400 text-sm font-semibold @error('email') border-red-500 @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="latuaemail@azienda.it" />
            @error('email')
                <p class="text-rose-600 text-xs mt-1.5 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3.5 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg transition duration-200">
                Invia Link di Reimpostazione →
            </button>
        </div>

        <div class="text-center pt-3 border-t border-gray-100 mt-4">
            <a href="{{ route('login') }}" class="text-xs font-bold text-gray-500 hover:text-slate-900 transition">
                ← Torna al Login
            </a>
        </div>
    </form>
</x-guest-layout>
