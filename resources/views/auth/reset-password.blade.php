<x-guest-layout>
    <div class="mb-5">
        <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-1">Crea Nuova Password</h2>
        <p class="text-xs text-gray-500 font-medium">Imposta una nuova password sicura per accedere al tuo account.</p>
    </div>

    <!-- Generatore Rapido di Password -->
    <div class="mb-5 p-3.5 bg-amber-50/90 border border-amber-200 rounded-2xl">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-black text-amber-950 uppercase tracking-wide">💡 Password Sicura</span>
            <button type="button" onclick="generateSecurePassword()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black text-[11px] uppercase tracking-wider rounded-lg shadow transition">
                ⚡ Genera Automatica
            </button>
        </div>
        <div id="generated-password-box" class="mt-2.5 pt-2.5 border-t border-amber-200/80 hidden">
            <div class="flex items-center justify-between bg-white px-3 py-2 rounded-xl border border-amber-300">
                <span id="generated-password-text" class="font-mono text-xs font-bold text-slate-900 select-all"></span>
                <button type="button" onclick="copyGeneratedPassword()" id="btn-copy-pass" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-900 px-2 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 transition">
                    📋 Copia
                </button>
            </div>
            <p class="text-[10px] text-amber-800 font-bold mt-1">✓ Password generata e inserita automaticamente nei campi sottostanti.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-black uppercase text-gray-700 tracking-wider mb-1">Indirizzo E-mail</label>
            <input id="email" class="block w-full border-gray-300 rounded-xl shadow-sm bg-gray-50 text-slate-600 focus:border-yellow-400 focus:ring-yellow-400 text-sm font-semibold @error('email') border-red-500 @enderror" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" readonly />
            @error('email')
                <p class="text-rose-600 text-xs mt-1.5 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-xs font-black uppercase text-gray-700 tracking-wider">Nuova Password</label>
                <button type="button" onclick="togglePasswordVisibility('password')" class="text-[11px] font-bold text-gray-500 hover:text-black">Mostra/Nascondi</button>
            </div>
            <div class="relative">
                <input id="password" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-yellow-400 focus:ring-yellow-400 text-sm font-semibold pr-10 @error('password') border-red-500 @enderror" type="password" name="password" required autocomplete="new-password" placeholder="Inserisci nuova password" oninput="checkPasswordCriteria()" />
            </div>
            @error('password')
                <p class="text-rose-600 text-xs mt-1.5 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password_confirmation" class="block text-xs font-black uppercase text-gray-700 tracking-wider">Conferma Nuova Password</label>
                <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="text-[11px] font-bold text-gray-500 hover:text-black">Mostra/Nascondi</button>
            </div>
            <div class="relative">
                <input id="password_confirmation" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-yellow-400 focus:ring-yellow-400 text-sm font-semibold pr-10 @error('password_confirmation') border-red-500 @enderror" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ripeti la nuova password" oninput="checkPasswordCriteria()" />
            </div>
            @error('password_confirmation')
                <p class="text-rose-600 text-xs mt-1.5 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Criteri di Sicurezza Password -->
        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200 text-xs space-y-1.5">
            <p class="font-black text-gray-700 uppercase tracking-wider text-[10px] mb-1">Criteri di Sicurezza Richiesti:</p>
            <div id="crit-length" class="flex items-center gap-2 text-gray-500 font-semibold">
                <span class="crit-icon">○</span> <span>Almeno 8 caratteri</span>
            </div>
            <div id="crit-upper" class="flex items-center gap-2 text-gray-500 font-semibold">
                <span class="crit-icon">○</span> <span>Almeno una lettera maiuscola (A-Z)</span>
            </div>
            <div id="crit-lower" class="flex items-center gap-2 text-gray-500 font-semibold">
                <span class="crit-icon">○</span> <span>Almeno una lettera minuscola (a-z)</span>
            </div>
            <div id="crit-number" class="flex items-center gap-2 text-gray-500 font-semibold">
                <span class="crit-icon">○</span> <span>Almeno un numero (0-9)</span>
            </div>
            <div id="crit-special" class="flex items-center gap-2 text-gray-500 font-semibold">
                <span class="crit-icon">○</span> <span>Almeno un carattere speciale (!@#$%^&*...)</span>
            </div>
            <div id="crit-match" class="flex items-center gap-2 text-gray-500 font-semibold">
                <span class="crit-icon">○</span> <span>Le due password corrispondono</span>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3.5 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg transition duration-200">
                Salva Nuova Password →
            </button>
        </div>

        <div class="text-center pt-3 border-t border-gray-100 mt-4">
            <a href="{{ route('login') }}" class="text-xs font-bold text-gray-500 hover:text-slate-900 transition">
                ← Torna al Login
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
                btn.className = 'text-[11px] font-bold text-emerald-700 px-2 py-0.5 rounded bg-emerald-50';
                setTimeout(() => {
                    btn.innerText = '📋 Copia';
                    btn.className = 'text-[11px] font-bold text-indigo-600 hover:text-indigo-900 px-2 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 transition';
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
                el.className = 'flex items-center gap-2 text-emerald-700 font-bold transition';
                icon.innerText = '✓';
            } else {
                el.className = 'flex items-center gap-2 text-gray-500 font-semibold transition';
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
