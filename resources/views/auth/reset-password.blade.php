<x-guest-layout>
    @php
        $isEn = app()->getLocale() === 'en';
    @endphp

    <div class="mb-5 text-center">
        <h2 class="text-base sm:text-lg font-black text-white uppercase tracking-tight mb-1">
            {{ $isEn ? 'Create New Password' : 'Crea Nuova Password' }}
        </h2>
        <p class="text-xs text-zinc-400 font-medium">
            {{ $isEn ? 'Set a new secure password to access your account.' : 'Imposta una nuova password sicura per accedere al tuo account.' }}
        </p>
    </div>

    <!-- Generatore Rapido di Password -->
    <div class="mb-5 p-3.5 bg-zinc-950/70 border border-yellow-400/30 rounded-2xl">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-black text-yellow-400 uppercase tracking-wide">💡 Password Sicura</span>
            <button type="button" onclick="generateSecurePassword()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black text-[11px] uppercase tracking-wider rounded-xl shadow transition cursor-pointer">
                ⚡ Genera Automatica
            </button>
        </div>
        <div id="generated-password-box" class="mt-2.5 pt-2.5 border-t border-zinc-800 hidden">
            <div class="flex items-center justify-between bg-zinc-900 px-3 py-2 rounded-xl border border-zinc-700">
                <span id="generated-password-text" class="font-mono text-xs font-bold text-yellow-300 select-all"></span>
                <button type="button" onclick="copyGeneratedPassword()" id="btn-copy-pass" class="text-[11px] font-bold text-zinc-300 hover:text-white px-2 py-0.5 rounded bg-zinc-800 hover:bg-zinc-700 transition cursor-pointer">
                    📋 Copia
                </button>
            </div>
            <p class="text-[10px] text-zinc-400 font-bold mt-1">✓ Password generata e inserita automaticamente nei campi sottostanti.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase text-zinc-300 tracking-wider mb-1.5">
                {{ $isEn ? 'Email Address' : 'Indirizzo E-mail' }}
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-500 text-sm pointer-events-none">
                    ✉️
                </span>
                <input id="email" class="w-full bg-zinc-950/40 border border-zinc-800 rounded-2xl pl-10 pr-4 py-3 text-sm font-semibold text-zinc-400 focus:outline-none cursor-not-allowed shadow-inner" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" readonly />
            </div>
            @error('email')
                <p class="text-rose-400 text-xs mt-1.5 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold uppercase text-zinc-300 tracking-wider">
                    {{ $isEn ? 'New Password' : 'Nuova Password' }}
                </label>
                <button type="button" onclick="togglePasswordVisibility('password')" class="text-[11px] font-bold text-yellow-400 hover:text-yellow-300 cursor-pointer">Mostra/Nascondi</button>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400 text-sm pointer-events-none">
                    🔒
                </span>
                <input id="password" class="w-full bg-zinc-950/70 border border-zinc-700/80 rounded-2xl pl-10 pr-4 py-3 text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition shadow-inner @error('password') border-red-500 @enderror" type="password" name="password" required autocomplete="new-password" placeholder="Inserisci nuova password" oninput="checkPasswordCriteria()" />
            </div>
            @error('password')
                <p class="text-rose-400 text-xs mt-1.5 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password_confirmation" class="block text-xs font-bold uppercase text-zinc-300 tracking-wider">
                    {{ $isEn ? 'Confirm New Password' : 'Conferma Nuova Password' }}
                </label>
                <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="text-[11px] font-bold text-yellow-400 hover:text-yellow-300 cursor-pointer">Mostra/Nascondi</button>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400 text-sm pointer-events-none">
                    🔒
                </span>
                <input id="password_confirmation" class="w-full bg-zinc-950/70 border border-zinc-700/80 rounded-2xl pl-10 pr-4 py-3 text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition shadow-inner @error('password_confirmation') border-red-500 @enderror" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ripeti la nuova password" oninput="checkPasswordCriteria()" />
            </div>
            @error('password_confirmation')
                <p class="text-rose-400 text-xs mt-1.5 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Criteri di Sicurezza Password -->
        <div class="p-3.5 bg-zinc-950/50 rounded-2xl border border-zinc-800 text-xs space-y-1.5">
            <p class="font-black text-zinc-400 uppercase tracking-wider text-[10px] mb-1">Criteri di Sicurezza:</p>
            <div id="crit-length" class="flex items-center gap-2 text-zinc-500 font-semibold">
                <span class="crit-icon">○</span> <span>Almeno 8 caratteri</span>
            </div>
            <div id="crit-upper" class="flex items-center gap-2 text-zinc-500 font-semibold">
                <span class="crit-icon">○</span> <span>Almeno una lettera maiuscola (A-Z)</span>
            </div>
            <div id="crit-lower" class="flex items-center gap-2 text-zinc-500 font-semibold">
                <span class="crit-icon">○</span> <span>Almeno una lettera minuscola (a-z)</span>
            </div>
            <div id="crit-number" class="flex items-center gap-2 text-zinc-500 font-semibold">
                <span class="crit-icon">○</span> <span>Almeno un numero (0-9)</span>
            </div>
            <div id="crit-special" class="flex items-center gap-2 text-zinc-500 font-semibold">
                <span class="crit-icon">○</span> <span>Almeno un carattere speciale (!@#$%^&*...)</span>
            </div>
            <div id="crit-match" class="flex items-center gap-2 text-zinc-500 font-semibold">
                <span class="crit-icon">○</span> <span>Le due password corrispondono</span>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-300 active:scale-[0.99] text-slate-950 font-black uppercase tracking-wider text-xs py-3.5 px-6 rounded-2xl shadow-lg shadow-yellow-400/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>{{ $isEn ? 'Save New Password' : 'Salva Nuova Password' }}</span>
                <span class="text-sm font-bold">→</span>
            </button>
        </div>

        <div class="text-center pt-3 border-t border-zinc-800/80 mt-4">
            <a href="{{ route('login') }}" class="text-xs font-bold text-zinc-400 hover:text-yellow-400 transition">
                ← {{ $isEn ? 'Back to Login' : 'Torna al Login' }}
            </a>
        </div>
    </form>

    <script>
        function generateSecurePassword() {
            const uppers = "ABCDEFGHJKLMNPQRSTUVWXYZ";
            const lowers = "abcdefghijkmnopqrstuvwxyz";
            const numbers = "23456789";
            const symbols = "!@#$%^&*_-+=";
            
            // Garantisce almeno 1 per ogni categoria richiesta
            let pass = "";
            pass += uppers.charAt(Math.floor(Math.random() * uppers.length));
            pass += uppers.charAt(Math.floor(Math.random() * uppers.length));
            pass += lowers.charAt(Math.floor(Math.random() * lowers.length));
            pass += lowers.charAt(Math.floor(Math.random() * lowers.length));
            pass += numbers.charAt(Math.floor(Math.random() * numbers.length));
            pass += numbers.charAt(Math.floor(Math.random() * numbers.length));
            pass += symbols.charAt(Math.floor(Math.random() * symbols.length));
            pass += symbols.charAt(Math.floor(Math.random() * symbols.length));

            const allChars = uppers + lowers + numbers + symbols;
            for (let i = 0; i < 6; i++) {
                pass += allChars.charAt(Math.floor(Math.random() * allChars.length));
            }

            // Mescola i caratteri in modo casuale
            pass = pass.split('').sort(() => 0.5 - Math.random()).join('');

            // Inserisce nei campi
            const passInput = document.getElementById('password');
            const confInput = document.getElementById('password_confirmation');
            passInput.value = pass;
            confInput.value = pass;

            // Mostra il box con la password generata
            const box = document.getElementById('generated-password-box');
            const text = document.getElementById('generated-password-text');
            text.innerText = pass;
            box.classList.remove('hidden');

            checkPasswordCriteria();
        }

        function copyGeneratedPassword() {
            const text = document.getElementById('generated-password-text').innerText;
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('btn-copy-pass');
                btn.innerText = '✓ Copiato!';
                btn.className = 'text-[11px] font-bold text-yellow-400 px-2 py-0.5 rounded bg-zinc-800';
                setTimeout(() => {
                    btn.innerText = '📋 Copia';
                    btn.className = 'text-[11px] font-bold text-zinc-300 hover:text-white px-2 py-0.5 rounded bg-zinc-800 hover:bg-zinc-700 transition cursor-pointer';
                }, 2000);
            });
        }

        function togglePasswordVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }

        function setCritStatus(elementId, isValid) {
            const el = document.getElementById(elementId);
            if (!el) return;
            const icon = el.querySelector('.crit-icon');
            if (isValid) {
                el.className = 'flex items-center gap-2 text-emerald-400 font-bold transition';
                icon.innerText = '✓';
            } else {
                el.className = 'flex items-center gap-2 text-zinc-500 font-semibold transition';
                icon.innerText = '○';
            }
        }

        function checkPasswordCriteria() {
            const pass = document.getElementById('password').value || '';
            const conf = document.getElementById('password_confirmation').value || '';

            setCritStatus('crit-length', pass.length >= 8);
            setCritStatus('crit-upper', /[A-Z]/.test(pass));
            setCritStatus('crit-lower', /[a-z]/.test(pass));
            setCritStatus('crit-number', /[0-9]/.test(pass));
            setCritStatus('crit-special', /[^A-Za-z0-9]/.test(pass));
            setCritStatus('crit-match', pass.length > 0 && pass === conf);
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkPasswordCriteria();
        });
    </script>
</x-guest-layout>
