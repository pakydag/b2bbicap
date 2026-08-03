<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registra Nuovo Agente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.b2b.agents.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informazioni Personali</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    </div>
                                    <div>
                                        <label for="surname" class="block text-sm font-medium text-gray-700">Cognome</label>
                                        <input type="text" name="surname" id="surname" value="{{ old('surname') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail (usata per il login)</label>
                                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror" required>
                                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700">Telefono</label>
                                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                            </div>

                             <div>
                                 <div>
                                     <div class="flex items-center justify-between mb-4">
                                         <h3 class="text-lg font-medium text-gray-900">Autorizzazioni Linee</h3>
                                         <button type="button" onclick="toggleAllBrands()" class="text-xs font-bold text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded transition">Seleziona/Deseleziona Tutto</button>
                                     </div>
                                     <p class="text-sm text-gray-600 mb-4">L'agente potrà visualizzare ed ordinare solo i prodotti delle linee selezionate.</p>
                                     
                                     <div class="space-y-2 max-h-48 overflow-y-auto p-4 border border-gray-200 rounded-md bg-gray-50 mb-6">
                                         @foreach($brands as $brand)
                                             <div class="flex items-center">
                                                 <input type="checkbox" name="brands[]" id="brand_{{ $brand->id }}" value="{{ $brand->id }}" class="brand-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ is_array(old('brands')) && in_array($brand->id, old('brands')) ? 'checked' : '' }}>
                                                 <label for="brand_{{ $brand->id }}" class="ml-2 text-sm text-gray-700">{{ $brand->name }}</label>
                                             </div>
                                         @endforeach
                                         @if($brands->isEmpty())
                                             <p class="text-sm text-gray-500 italic">Nessuna linea disponibile.</p>
                                         @endif
                                     </div>
                                 
                                     <h3 class="text-lg font-medium text-gray-900 mb-4">Clienti Autorizzati</h3>
                                     <p class="text-sm text-gray-600 mb-4">L'agente potrà caricare ordini solo per i clienti selezionati.</p>

                                     <!-- Filtro di Ricerca Clienti -->
                                     <div class="mb-3">
                                         <input type="text" id="customer-search-input" onkeyup="filterB2bCustomers()" placeholder="Cerca cliente per ragione sociale o P.IVA..." class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                     </div>

                                     <!-- Clienti Selezionati Rapidi -->
                                     <div id="selected-customers-container" class="mb-4 flex flex-wrap gap-1.5 hidden"></div>
                                     
                                     <div class="space-y-2 max-h-48 overflow-y-auto p-4 border border-gray-200 rounded-md bg-gray-50">
                                         @foreach($customers as $customer)
                                             <div class="customer-item flex items-center" data-name="{{ strtolower($customer->business_name) }}" data-vat="{{ $customer->vat_number }}">
                                                 <input type="checkbox" name="customers[]" id="cust_{{ $customer->id }}" value="{{ $customer->id }}" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ is_array(old('customers')) && in_array($customer->id, old('customers')) ? 'checked' : '' }}>
                                                 <label for="cust_{{ $customer->id }}" class="ml-2 text-sm text-gray-700">{{ $customer->business_name }} ({{ $customer->vat_number }})</label>
                                             </div>
                                         @endforeach
                                         @if($customers->isEmpty())
                                             <p class="text-sm text-gray-500 italic">Nessun cliente disponibile nel database B2B.</p>
                                         @endif
                                     </div>
                                 </div>
                             </div>
                        </div>

                        <div class="mt-8 p-4 bg-indigo-50 rounded-md border border-indigo-100 italic text-sm text-indigo-700">
                            Nota: Una volta salvato, il sistema genererà una password temporanea e invierà una email all'agente con le istruzioni per impostare la propria password definitiva.
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.b2b.agents.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">Annulla</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Crea Account Agente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
