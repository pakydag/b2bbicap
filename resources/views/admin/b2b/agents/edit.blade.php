<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('Modifica Agente: ') }} {{ $agent->name }} {{ $agent->surname }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5 font-bold">Modifica anagrafica agente, assegnazione brand e clienti</p>
            </div>
            <a href="{{ route('admin.b2b.agents.index') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-slate-900 text-white font-black rounded-xl text-xs uppercase tracking-wider hover:bg-yellow-400 hover:text-slate-950 transition shadow">
                ← Torna agli Agenti
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl space-y-6">
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6">
            <form action="{{ route('admin.b2b.agents.update', $agent) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide mb-4">Informazioni Personali</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Nome</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $agent->name) }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm" required>
                            </div>
                            <div>
                                <label for="surname" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Cognome</label>
                                <input type="text" name="surname" id="surname" value="{{ old('surname', $agent->surname) }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm" required>
                            </div>
                            <div>
                                <label for="email" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">E-mail</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $agent->email) }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm @error('email') border-red-500 @enderror" required>
                                @error('email') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Telefono</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $agent->phone) }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Autorizzazioni Linee</h3>
                            <button type="button" onclick="toggleAllBrands()" class="text-[10px] font-black uppercase text-slate-900 bg-yellow-100 hover:bg-yellow-200 px-2.5 py-1 rounded-lg transition">Tutti / Nessuno</button>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">L'agente potrà visualizzare ed ordinare solo i prodotti delle linee abilitate.</p>
                        
                        <div class="space-y-2 max-h-44 overflow-y-auto p-3.5 border border-gray-100 rounded-xl bg-gray-50 mb-6">
                            @php $assignedBrands = $agent->b2bBrands->pluck('id')->toArray(); @endphp
                            @foreach($brands as $brand)
                                <div class="flex items-center">
                                    <input type="checkbox" name="brands[]" id="brand_{{ $brand->id }}" value="{{ $brand->id }}" class="brand-checkbox h-4 w-4 text-black border-gray-300 rounded focus:ring-yellow-400" {{ in_array($brand->id, old('brands', $assignedBrands)) ? 'checked' : '' }}>
                                    <label for="brand_{{ $brand->id }}" class="ml-2 text-xs font-bold text-slate-800">{{ $brand->name }}</label>
                                </div>
                            @endforeach
                        </div>

                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide mb-1">Clienti Autorizzati</h3>
                        <p class="text-xs text-gray-500 mb-2">L'agente potrà caricare ordini solo per i clienti selezionati.</p>
                        
                        <!-- Filtro di Ricerca Clienti -->
                        <div class="mb-2">
                            <input type="text" id="customer-search-input" onkeyup="filterB2bCustomers()" placeholder="Cerca cliente per nome o P.IVA..." class="w-full text-xs font-bold border-gray-200 rounded-xl shadow-sm focus:border-yellow-400 focus:ring-yellow-400">
                        </div>

                        <!-- Clienti Selezionati Rapidi -->
                        <div id="selected-customers-container" class="mb-3 flex flex-wrap gap-1.5 hidden"></div>

                        <div class="space-y-2 max-h-48 overflow-y-auto p-3.5 border border-gray-100 rounded-xl bg-gray-50">
                            @php $assignedCustomers = $agent->b2bCustomers->pluck('id')->toArray(); @endphp
                            @foreach($customers as $customer)
                                <div class="customer-item flex items-center" data-name="{{ strtolower($customer->business_name) }}" data-vat="{{ $customer->vat_number }}">
                                    <input type="checkbox" name="customers[]" id="cust_{{ $customer->id }}" value="{{ $customer->id }}" class="h-4 w-4 text-black border-gray-300 rounded focus:ring-yellow-400" {{ in_array($customer->id, old('customers', $assignedCustomers)) ? 'checked' : '' }}>
                                    <label for="cust_{{ $customer->id }}" class="ml-2 text-xs font-medium text-slate-800">{{ $customer->business_name }} ({{ $customer->vat_number }})</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-amber-50/90 rounded-2xl border border-amber-200">
                    <label class="flex items-start sm:items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="send_email_notification" value="1" checked class="mt-0.5 sm:mt-0 w-5 h-5 text-yellow-500 bg-white border-amber-300 rounded focus:ring-yellow-400 focus:ring-2 cursor-pointer">
                        <div>
                            <span class="text-xs font-black text-amber-950 uppercase tracking-wide">✉️ Invia email con il riepilogo aggiornato delle autorizzazioni all'agente</span>
                            <p class="text-[11px] text-amber-800 font-medium mt-0.5">Se attiva, invierà all'agente un'email di notifica con l'elenco aggiornato delle linee e delle aziende clienti a cui è stato abilitato.</p>
                        </div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                    <a href="{{ route('admin.b2b.agents.index') }}" class="px-4 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-900 transition">Annulla</a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow transition">
                        Salva Modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleAllBrands() {
            const checkboxes = document.querySelectorAll('.brand-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }

        function filterB2bCustomers() {
            const query = document.getElementById('customer-search-input').value.toLowerCase();
            const items = document.querySelectorAll('.customer-item');
            items.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const vat = item.getAttribute('data-vat') || '';
                if (name.includes(query) || vat.includes(query)) {
                    item.style.setProperty('display', 'flex', 'important');
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });
        }

        function updateSelectedCustomersList() {
            const container = document.getElementById('selected-customers-container');
            if (!container) return;
            
            container.innerHTML = '';
            const checkedBoxes = document.querySelectorAll('input[name="customers[]"]:checked');
            
            if (checkedBoxes.length > 0) {
                container.classList.remove('hidden');
                
                const label = document.createElement('div');
                label.className = 'w-full text-xs font-bold text-gray-500 mb-1';
                label.innerText = 'Clienti Selezionati (clicca la × per rimuoverli):';
                container.appendChild(label);
                
                checkedBoxes.forEach(cb => {
                    const labelText = cb.nextElementSibling.innerText;
                    
                    const badge = document.createElement('span');
                    badge.className = 'inline-flex items-center bg-indigo-50 text-indigo-700 text-xs px-2.5 py-1 rounded-md border border-indigo-100 font-medium shadow-sm';
                    badge.innerHTML = `
                        <span>${labelText}</span>
                        <button type="button" class="ml-1.5 text-indigo-400 hover:text-indigo-900 font-bold focus:outline-none" onclick="uncheckCustomer('${cb.id}')">×</button>
                    `;
                    container.appendChild(badge);
                });
            } else {
                container.classList.add('hidden');
            }
        }
        
        function uncheckCustomer(checkboxId) {
            const cb = document.getElementById(checkboxId);
            if (cb) {
                cb.checked = false;
                cb.dispatchEvent(new Event('change'));
                updateSelectedCustomersList();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="customers[]"]');
            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateSelectedCustomersList);
            });
            updateSelectedCustomersList();
        });
    </script>
</x-app-layout>
