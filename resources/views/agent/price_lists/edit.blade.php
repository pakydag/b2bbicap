<x-agent-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
                    {{ __('Configura Listino & Fasce: ') }} <span class="text-indigo-600">{{ $priceList->name }}</span>
                </h2>
                @if($assignedCustomer)
                    <p class="text-xs text-gray-500 font-bold uppercase mt-1">
                        🏢 Abbinato all'azienda: <span class="text-slate-900 font-black">{{ $assignedCustomer->business_name }}</span>
                    </p>
                @endif
            </div>
            <a href="{{ route('agent.price-lists.index') }}" class="bg-gray-100 border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-gray-200 transition">
                ← Torna ai Listini
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('agent.price-lists.update', $priceList) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Informazioni Listino & Abbinamento Azienda -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-base font-black text-gray-800 uppercase tracking-wider mb-4 border-b pb-2">Informazioni & Abbinamento Azienda</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="b2b_customer_id" class="block text-xs font-bold text-gray-700 uppercase mb-1">Azienda / Cliente B2B Abbinato</label>
                            <select name="b2b_customer_id" id="b2b_customer_id" 
                                    class="w-full rounded-xl border-gray-200 p-2.5 text-sm focus:border-indigo-500 font-bold text-slate-900 bg-gray-50">
                                <option value="">-- Seleziona Azienda --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ ($assignedCustomer && $assignedCustomer->id == $c->id) ? 'selected' : '' }}>
                                        {{ $c->business_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-700 uppercase mb-1">Nome Listino *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $priceList->name) }}" required 
                                   class="w-full rounded-xl border-gray-200 p-2.5 text-sm focus:border-indigo-500 font-bold">
                        </div>

                        <div>
                            <label for="description" class="block text-xs font-bold text-gray-700 uppercase mb-1">Descrizione / Note</label>
                            <input type="text" name="description" id="description" value="{{ old('description', $priceList->description) }}" 
                                   class="w-full rounded-xl border-gray-200 p-2.5 text-sm focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Sezione Fasce di Quantità Generali (su tutti i prodotti) -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-3 border-b pb-3">
                        <div>
                            <h3 class="text-base font-black text-indigo-900 uppercase tracking-wider">1. Fasce di Quantità Generali (Valide per TUTTI i Prodotti)</h3>
                            <p class="text-xs text-gray-500 mt-1">Imposta sconti % per quantità su tutto il catalogo (es. <strong>1-10 pz</strong> -> 10%, <strong>11-20 pz</strong> -> 20%). Verranno applicati a tutti i prodotti che non hanno una regola specifica.</p>
                        </div>
                        <button type="button" onclick="addGeneralTierRow()" 
                                class="inline-flex items-center px-3.5 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-sm hover:bg-indigo-700 transition">
                            + Aggiungi Fascia Generale
                        </button>
                    </div>

                    <div class="bg-gray-50/50 rounded-2xl border border-gray-100 p-3">
                        <div class="grid grid-cols-12 gap-2 text-[10px] font-black uppercase tracking-wider text-gray-400 pb-1.5 border-b border-gray-200">
                            <div class="col-span-3">Da Quantità (Min)</div>
                            <div class="col-span-3">A Quantità (Max - opzionale)</div>
                            <div class="col-span-3">Tipo Regola</div>
                            <div class="col-span-2">Valore (% Sconto o € Netto)</div>
                            <div class="col-span-1 text-center">Azione</div>
                        </div>
                        <div id="general-tiers-container" class="space-y-2 mt-2">
                            @forelse($generalTiers as $gIdx => $gtier)
                                <div class="general-tier-row grid grid-cols-12 gap-2 items-center text-xs" data-index="{{ $gIdx }}">
                                    <div class="col-span-3">
                                        <input type="number" name="general_tiers[{{ $gIdx }}][min_quantity]" value="{{ $gtier->min_quantity }}" min="1" required class="w-full text-xs rounded-lg border-gray-200 p-2 font-bold">
                                    </div>
                                    <div class="col-span-3">
                                        <input type="number" name="general_tiers[{{ $gIdx }}][max_quantity]" value="{{ $gtier->max_quantity }}" min="1" placeholder="Illimitato" class="w-full text-xs rounded-lg border-gray-200 p-2">
                                    </div>
                                    <div class="col-span-3">
                                        <select name="general_tiers[{{ $gIdx }}][discount_type]" class="w-full text-xs rounded-lg border-gray-200 p-2">
                                            <option value="percentage" {{ $gtier->discount_type === 'percentage' ? 'selected' : '' }}>Sconto % su Prezzo Base</option>
                                            <option value="fixed_price" {{ $gtier->discount_type === 'fixed_price' ? 'selected' : '' }}>Prezzo Netto Personalizzato (€)</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" step="0.01" min="0" name="general_tiers[{{ $gIdx }}][discount_value]" value="{{ number_format($gtier->discount_value, 2, '.', '') }}" required class="w-full text-xs rounded-lg border-gray-200 p-2 font-bold text-amber-700 bg-amber-50/30">
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <button type="button" onclick="removeGeneralTierRow(this)" class="text-rose-600 hover:text-rose-800 font-bold text-sm">✕</button>
                                    </div>
                                </div>
                            @empty
                                <!-- Nessuna fascia generale registrata -->
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Fasce di Quantità per Singolo Prodotto -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 border-b pb-4">
                        <div>
                            <h3 class="text-base font-black text-gray-800 uppercase tracking-wider">2. Fasce di Quantità Specifiche per Prodotto (Eccezioni)</h3>
                            <p class="text-xs text-gray-500 mt-1">Se desideri derogare alle fasce generali per un determinato modello, aggiungi qui le sue fasce personalizzate.</p>
                        </div>
                        <div class="w-full sm:w-72">
                            <input type="text" id="productSearchInput" placeholder="🔍 Cerca per nome o codice..." 
                                   class="w-full text-xs rounded-xl border-gray-200 p-2.5 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2" id="productsListContainer">
                        @foreach($products as $product)
                            @php
                                $existingTiers = $itemsGrouped->get($product->id, collect());
                            @endphp
                            <div class="product-item-card border border-gray-100 rounded-2xl p-4 bg-gray-50/50 hover:bg-white hover:border-gray-200 transition" data-name="{{ strtolower($product->name) }} {{ strtolower($product->code) }}">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-3">
                                    <div class="flex items-center gap-3">
                                        @if($product->image)
                                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-10 h-10 object-contain rounded-xl bg-white p-1 border border-gray-100">
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">N/D</div>
                                        @endif
                                        <div>
                                            <span class="text-xs font-bold text-indigo-600">{{ $product->code }}</span>
                                            <h4 class="font-bold text-slate-900 text-sm">{{ $product->name }}</h4>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-xs">
                                            <span class="text-gray-400 font-bold uppercase">Prezzo Listino Base:</span>
                                            <span class="font-black text-slate-900">€ {{ number_format($product->price, 2, ',', '.') }}</span>
                                        </div>
                                        <button type="button" onclick="addTierRow({{ $product->id }})" 
                                                class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 rounded-xl text-xs font-bold transition">
                                            + Aggiungi Eccezione Prodotto
                                        </button>
                                    </div>
                                </div>

                                <!-- Tabella Fasce -->
                                <div class="bg-white rounded-xl border border-gray-100 p-3">
                                    <div class="grid grid-cols-12 gap-2 text-[10px] font-black uppercase tracking-wider text-gray-400 pb-1.5 border-b border-gray-100">
                                        <div class="col-span-3">Da Quantità (Min)</div>
                                        <div class="col-span-3">A Quantità (Max - opzionale)</div>
                                        <div class="col-span-3">Tipo Regola</div>
                                        <div class="col-span-2">Valore (% o € Netto)</div>
                                        <div class="col-span-1 text-center">Azione</div>
                                    </div>
                                    <div id="tiers-container-{{ $product->id }}" class="space-y-2 mt-2">
                                        @forelse($existingTiers as $idx => $tier)
                                            <div class="tier-row grid grid-cols-12 gap-2 items-center text-xs" data-index="{{ $idx }}">
                                                <div class="col-span-3">
                                                    <input type="number" name="tiers[{{ $product->id }}][{{ $idx }}][min_quantity]" value="{{ $tier->min_quantity }}" min="1" required class="w-full text-xs rounded-lg border-gray-200 p-1.5 font-bold">
                                                </div>
                                                <div class="col-span-3">
                                                    <input type="number" name="tiers[{{ $product->id }}][{{ $idx }}][max_quantity]" value="{{ $tier->max_quantity }}" min="1" placeholder="Illimitato" class="w-full text-xs rounded-lg border-gray-200 p-1.5">
                                                </div>
                                                <div class="col-span-3">
                                                    <select name="tiers[{{ $product->id }}][{{ $idx }}][discount_type]" class="w-full text-xs rounded-lg border-gray-200 p-1.5">
                                                        <option value="percentage" {{ $tier->discount_type === 'percentage' ? 'selected' : '' }}>Sconto % su Prezzo Base</option>
                                                        <option value="fixed_price" {{ $tier->discount_type === 'fixed_price' ? 'selected' : '' }}>Prezzo Netto Personalizzato (€)</option>
                                                    </select>
                                                </div>
                                                <div class="col-span-2">
                                                    <input type="number" step="0.01" min="0" name="tiers[{{ $product->id }}][{{ $idx }}][discount_value]" value="{{ number_format($tier->discount_value, 2, '.', '') }}" required class="w-full text-xs rounded-lg border-gray-200 p-1.5 font-bold text-indigo-700 bg-indigo-50/20">
                                                </div>
                                                <div class="col-span-1 text-center">
                                                    <button type="button" onclick="removeTierRow(this)" class="text-rose-600 hover:text-rose-800 font-bold text-sm">✕</button>
                                                </div>
                                            </div>
                                        @empty
                                            <!-- Nessuna fascia specifica -->
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('agent.price-lists.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-gray-200">
                        Annulla
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 shadow-md">
                        Salva Listino e Fasce di Quantità
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('productSearchInput').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.product-item-card').forEach(card => {
                const searchData = card.getAttribute('data-name');
                if (searchData.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        function addGeneralTierRow() {
            const container = document.getElementById('general-tiers-container');
            const index = container.children.length;
            
            let defaultMin = 1;
            if (index > 0) {
                const lastMinInput = container.children[index - 1].querySelector('input[name*="[min_quantity]"]');
                const lastMaxInput = container.children[index - 1].querySelector('input[name*="[max_quantity]"]');
                if (lastMaxInput && lastMaxInput.value) {
                    defaultMin = parseInt(lastMaxInput.value) + 1;
                } else if (lastMinInput && lastMinInput.value) {
                    defaultMin = parseInt(lastMinInput.value) + 10;
                }
            }

            const html = `
                <div class="general-tier-row grid grid-cols-12 gap-2 items-center text-xs" data-index="${index}">
                    <div class="col-span-3">
                        <input type="number" name="general_tiers[${index}][min_quantity]" value="${defaultMin}" min="1" required class="w-full text-xs rounded-lg border-gray-200 p-1.5 font-bold">
                    </div>
                    <div class="col-span-3">
                        <input type="number" name="general_tiers[${index}][max_quantity]" value="" min="1" placeholder="Illimitato" class="w-full text-xs rounded-lg border-gray-200 p-1.5">
                    </div>
                    <div class="col-span-3">
                        <select name="general_tiers[${index}][discount_type]" class="w-full text-xs rounded-lg border-gray-200 p-1.5">
                            <option value="percentage">Sconto % su Prezzo Base</option>
                            <option value="fixed_price">Prezzo Netto Personalizzato (€)</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <input type="number" step="0.01" min="0" name="general_tiers[${index}][discount_value]" value="0" required class="w-full text-xs rounded-lg border-gray-200 p-1.5 font-bold text-amber-700 bg-amber-50/30">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" onclick="removeGeneralTierRow(this)" class="text-rose-600 hover:text-rose-800 font-bold text-sm">✕</button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeGeneralTierRow(btn) {
            btn.closest('.general-tier-row').remove();
        }

        function addTierRow(productId) {
            const container = document.getElementById('tiers-container-' + productId);
            const index = container.children.length;
            
            let defaultMin = 1;
            if (index > 0) {
                const lastMinInput = container.children[index - 1].querySelector('input[name*="[min_quantity]"]');
                const lastMaxInput = container.children[index - 1].querySelector('input[name*="[max_quantity]"]');
                if (lastMaxInput && lastMaxInput.value) {
                    defaultMin = parseInt(lastMaxInput.value) + 1;
                } else if (lastMinInput && lastMinInput.value) {
                    defaultMin = parseInt(lastMinInput.value) + 10;
                }
            }

            const html = `
                <div class="tier-row grid grid-cols-12 gap-2 items-center text-xs" data-index="${index}">
                    <div class="col-span-3">
                        <input type="number" name="tiers[${productId}][${index}][min_quantity]" value="${defaultMin}" min="1" required class="w-full text-xs rounded-lg border-gray-200 p-1.5 font-bold">
                    </div>
                    <div class="col-span-3">
                        <input type="number" name="tiers[${productId}][${index}][max_quantity]" value="" min="1" placeholder="Illimitato" class="w-full text-xs rounded-lg border-gray-200 p-1.5">
                    </div>
                    <div class="col-span-3">
                        <select name="tiers[${productId}][${index}][discount_type]" class="w-full text-xs rounded-lg border-gray-200 p-1.5">
                            <option value="percentage">Sconto % su Prezzo Base</option>
                            <option value="fixed_price">Prezzo Netto Personalizzato (€)</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <input type="number" step="0.01" min="0" name="tiers[${productId}][${index}][discount_value]" value="0" required class="w-full text-xs rounded-lg border-gray-200 p-1.5 font-bold text-indigo-700 bg-indigo-50/20">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" onclick="removeTierRow(this)" class="text-rose-600 hover:text-rose-800 font-bold text-sm">✕</button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeTierRow(btn) {
            btn.closest('.tier-row').remove();
        }
    </script>
</x-agent-layout>
