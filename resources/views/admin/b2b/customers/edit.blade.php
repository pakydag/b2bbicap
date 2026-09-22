<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('Modifica Cliente B2B') }}: {{ $customer->business_name }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5 font-bold">Modifica anagrafica, codice gestionale e condizioni</p>
            </div>
            <a href="{{ route('admin.b2b.customers.index') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-slate-900 text-white font-black rounded-xl text-xs uppercase tracking-wider hover:bg-yellow-400 hover:text-slate-950 transition shadow">
                ← Torna ai Clienti
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-6">
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6">
            <form action="{{ route('admin.b2b.customers.update', $customer) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <label for="code" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Codice Gestionale</label>
                        <input type="text" name="code" id="code" value="{{ old('code', $customer->code) }}" placeholder="es. 9999" class="block w-full border-gray-200 rounded-xl text-xs font-mono font-black text-indigo-700 bg-indigo-50/50 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                        <p class="text-[10px] text-gray-400 mt-1 font-semibold">Codice FTPS / CSV ordini</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="business_name" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Ragione Sociale *</label>
                        <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $customer->business_name) }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm @error('business_name') border-red-500 @enderror" required>
                        @error('business_name') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="vat_number" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Partita IVA / Cod. Fiscale</label>
                        <input type="text" name="vat_number" id="vat_number" value="{{ old('vat_number', $customer->vat_number) }}" class="block w-full border-gray-200 rounded-xl text-xs font-mono font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">E-mail Aziendale</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                    </div>

                    <div>
                        <label for="contact_name" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Nome Referente</label>
                        <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name', $customer->contact_name) }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                    </div>

                    <div>
                        <label for="contact_surname" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Cognome Referente</label>
                        <input type="text" name="contact_surname" id="contact_surname" value="{{ old('contact_surname', $customer->contact_surname) }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Telefono</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                    </div>

                    <div>
                        <label for="payment_condition_id" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Condizioni di Pagamento</label>
                        <select name="payment_condition_id" id="payment_condition_id" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                            <option value="">Seleziona condizione...</option>
                            @foreach($conditions as $condition)
                                <option value="{{ $condition->id }}" {{ old('payment_condition_id', $customer->payment_condition_id) == $condition->id ? 'selected' : '' }}>{{ $condition->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="b2b_price_list_id" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Listino Prezzi Assegnato</label>
                        <select name="b2b_price_list_id" id="b2b_price_list_id" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                            <option value="">Listino Predefinito di Sistema</option>
                            @foreach($priceLists as $list)
                                <option value="{{ $list->id }}" {{ old('b2b_price_list_id', $customer->b2b_price_list_id) == $list->id ? 'selected' : '' }}>{{ $list->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sezione Agente di Riferimento -->
                    <div class="border-t border-gray-100 pt-5 md:col-span-2">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-3">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide flex items-center gap-2">
                                    <span>👤</span> Agente di Riferimento
                                </h3>
                                <p class="text-xs text-gray-500 font-medium">Visualizza o assegna l'agente commerciale responsabile di questo cliente</p>
                            </div>
                        </div>

                        <!-- Card Agenti Attualmente Assegnati -->
                        @if($customer->agents->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                @foreach($customer->agents as $ag)
                                    <div class="p-3.5 bg-slate-900 text-white rounded-2xl shadow-sm border border-slate-800 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-yellow-400 text-slate-950 font-black flex items-center justify-center text-sm shrink-0">
                                                {{ strtoupper(substr($ag->name, 0, 1)) }}{{ strtoupper(substr($ag->surname ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="font-black text-sm text-white leading-tight">{{ $ag->name }} {{ $ag->surname }}</h4>
                                                <p class="text-xs text-yellow-400 font-medium">{{ $ag->email }}</p>
                                                @if($ag->phone)
                                                    <p class="text-[11px] text-gray-300 font-mono">📞 {{ $ag->phone }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.b2b.agents.edit', $ag) }}" class="p-2 bg-slate-800 hover:bg-yellow-400 hover:text-slate-950 text-gray-300 rounded-xl transition text-xs font-bold" title="Vedi Scheda Agente">
                                            ↗
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-3.5 bg-amber-50/70 border border-amber-200 rounded-xl text-xs text-amber-900 font-medium flex items-center gap-2 mb-4">
                                <span>⚠️</span>
                                <span>Nessun agente attualmente associato a questo cliente. Puoi assegnarne uno selezionandolo qui sotto.</span>
                            </div>
                        @endif

                        <!-- Assegnazione / Selezione Agente -->
                        <div>
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1.5">
                                Assegna / Modifica Agente Commerciale
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 p-3 bg-gray-50 border border-gray-200 rounded-xl max-h-44 overflow-y-auto">
                                @forelse($agents as $agentOption)
                                    <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 hover:border-yellow-400 cursor-pointer text-xs transition">
                                        <input type="checkbox" name="agent_ids[]" value="{{ $agentOption->id }}" {{ in_array($agentOption->id, old('agent_ids', $assignedAgentIds)) ? 'checked' : '' }} class="h-4 w-4 text-black border-gray-300 rounded focus:ring-yellow-400">
                                        <span class="font-bold text-slate-800">{{ $agentOption->name }} {{ $agentOption->surname }}</span>
                                    </label>
                                @empty
                                    <p class="text-xs text-gray-400 col-span-full">Nessun agente configurato nel sistema.</p>
                                @endforelse
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 font-semibold">Seleziona gli agenti commerciali che hanno in carico questo cliente</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5 md:col-span-2">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Credenziali Accesso Portale B2B</h3>
                                <p class="text-xs text-gray-500 font-medium">Abilita o aggiorna le credenziali dell'azienda cliente per accedere ed ordinare online</p>
                            </div>
                            <button type="button" onclick="generateCustomerPassword()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black text-[11px] uppercase tracking-wider rounded-xl shadow transition">
                                ⚡ Genera Password Sicura
                            </button>
                        </div>

                        <!-- Box Password Generata -->
                        <div id="generated-password-box" class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl hidden">
                            <div class="flex items-center justify-between bg-white px-3 py-2 rounded-lg border border-amber-300">
                                <span id="generated-password-text" class="font-mono text-xs font-bold text-slate-900 select-all"></span>
                                <button type="button" onclick="copyCustomerPassword()" id="btn-copy-pass" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-900 px-2 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 transition">
                                    📋 Copia
                                </button>
                            </div>
                            <p class="text-[10px] text-amber-800 font-bold mt-1">✓ Password generata e inserita automaticamente nel campo sottostante.</p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="b2b_email" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Email di Accesso / Login (lascia vuoto per disabilitare)</label>
                                <input type="email" name="b2b_email" id="b2b_email" value="{{ old('b2b_email', $customerUser?->email) }}" placeholder="es. acquisti@azienda.it" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm @error('b2b_email') border-red-500 @enderror">
                                @error('b2b_email') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="b2b_password" class="block text-xs font-black text-gray-700 uppercase tracking-wider">Nuova Password (lascia vuoto per non modificare)</label>
                                    <button type="button" onclick="togglePasswordVisibility('b2b_password')" class="text-[11px] font-bold text-gray-500 hover:text-black transition">
                                        👁️ Mostra / Nascondi
                                    </button>
                                </div>
                                <input type="password" name="b2b_password" id="b2b_password" placeholder="Inserisci nuova password o clicca su 'Genera Password Sicura'" oninput="checkCustomerPasswordCriteria()" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm @error('b2b_password') border-red-500 @enderror">
                                @error('b2b_password') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Criteri di Sicurezza Password -->
                        <div class="mt-4 p-3.5 bg-gray-50 rounded-xl border border-gray-200 text-xs space-y-1.5">
                            <p class="font-black text-gray-700 uppercase tracking-wider text-[10px] mb-1">Criteri di Sicurezza Richiesti per la Password:</p>
                            <div id="crit-length" class="flex items-center gap-2 text-gray-500 font-semibold text-xs">
                                <span class="crit-icon">○</span> <span>Almeno 8 caratteri</span>
                            </div>
                            <div id="crit-upper" class="flex items-center gap-2 text-gray-500 font-semibold text-xs">
                                <span class="crit-icon">○</span> <span>Almeno una lettera maiuscola (A-Z)</span>
                            </div>
                            <div id="crit-lower" class="flex items-center gap-2 text-gray-500 font-semibold text-xs">
                                <span class="crit-icon">○</span> <span>Almeno una lettera minuscola (a-z)</span>
                            </div>
                            <div id="crit-number" class="flex items-center gap-2 text-gray-500 font-semibold text-xs">
                                <span class="crit-icon">○</span> <span>Almeno un numero (0-9)</span>
                            </div>
                            <div id="crit-special" class="flex items-center gap-2 text-gray-500 font-semibold text-xs">
                                <span class="crit-icon">○</span> <span>Almeno un carattere speciale (!@#$%^&*...)</span>
                            </div>
                        </div>

                        <!-- Spunta Invio Email Notifica -->
                        <div class="mt-4 p-4 bg-amber-50/90 rounded-2xl border border-amber-200">
                            <label class="flex items-start sm:items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="send_email_notification" value="1" checked class="mt-0.5 sm:mt-0 w-5 h-5 text-yellow-500 bg-white border-amber-300 rounded focus:ring-yellow-400 focus:ring-2 cursor-pointer">
                                <div>
                                    <span class="text-xs font-black text-amber-950 uppercase tracking-wide">✉️ Invia email di notifica / aggiornamento all'azienda cliente</span>
                                    <p class="text-[11px] text-amber-800 font-medium mt-0.5">Se attiva, invierà un'email al cliente con il riepilogo aggiornato dell'anagrafica, l'agente commerciale di riferimento assegnato e le credenziali di accesso al portale.</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.b2b.customers.index') }}" class="px-4 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-900 transition">Annulla</a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow transition">
                        Aggiorna Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function generateCustomerPassword() {
            const uppers = "ABCDEFGHJKLMNPQRSTUVWXYZ";
            const lowers = "abcdefghijkmnopqrstuvwxyz";
            const numbers = "23456789";
            const symbols = "!@#$%^&*_-+=";
            
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

            pass = pass.split('').sort(() => 0.5 - Math.random()).join('');

            const passInput = document.getElementById('b2b_password');
            passInput.value = pass;

            const box = document.getElementById('generated-password-box');
            const text = document.getElementById('generated-password-text');
            text.innerText = pass;
            box.classList.remove('hidden');

            checkCustomerPasswordCriteria();
        }

        function copyCustomerPassword() {
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
                el.className = 'flex items-center gap-2 text-emerald-700 font-bold transition text-xs';
                icon.innerText = '✓';
            } else {
                el.className = 'flex items-center gap-2 text-gray-500 font-semibold transition text-xs';
                icon.innerText = '○';
            }
        }

        function checkCustomerPasswordCriteria() {
            const pass = document.getElementById('b2b_password').value || '';

            setCritStatus('crit-length', pass.length >= 8);
            setCritStatus('crit-upper', /[A-Z]/.test(pass));
            setCritStatus('crit-lower', /[a-z]/.test(pass));
            setCritStatus('crit-number', /[0-9]/.test(pass));
            setCritStatus('crit-special', /[^A-Za-z0-9]/.test(pass));
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkCustomerPasswordCriteria();
        });
    </script>
</x-app-layout>
