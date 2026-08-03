<x-agent-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
                {{ __('Nuovo Listino Prezzi') }}
            </h2>
            <a href="{{ route('agent.price-lists.index') }}" class="bg-gray-100 border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-gray-200 transition">
                ← Torna ai Listini
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-6">
                <form action="{{ route('agent.price-lists.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Selezione Azienda obbligatoria -->
                        <div>
                            <label for="b2b_customer_id" class="block text-xs font-bold text-gray-700 uppercase mb-1">Seleziona Azienda / Cliente B2B *</label>
                            <select name="b2b_customer_id" id="b2b_customer_id" required 
                                    class="w-full rounded-xl border-gray-200 p-3 text-sm focus:border-indigo-500 font-bold text-slate-900 bg-gray-50">
                                <option value="">-- Scegli l'Azienda --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('b2b_customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->business_name }} {{ $c->vat_number ? "(P.IVA: {$c->vat_number})" : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('b2b_customer_id')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-700 uppercase mb-1">Nome Listino *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                                   placeholder="Es. Listino Azienda X, Listino Personalizzato 2026..."
                                   class="w-full rounded-xl border-gray-200 p-3 text-sm focus:border-indigo-500">
                            @error('name')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Sezione Fasce di Quantità Generali -->
                    <div class="border border-indigo-100 bg-indigo-50/30 rounded-2xl p-6">
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <h3 class="text-sm font-black text-indigo-900 uppercase tracking-wide">Fasce di Quantità Generali (su TUTTI i prodotti)</h3>
                                <p class="text-xs text-gray-500">Puoi scegliere per ogni fascia se applicare una <strong>% di sconto</strong> oppure un <strong>prezzo netto fisso (€)</strong>.</p>
                            </div>
                            <button type="button" onclick="addGeneralTierRow()" 
                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-sm hover:bg-indigo-700 transition">
                                + Aggiungi Fascia Quantità
                            </button>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 p-3">
                            <div class="grid grid-cols-12 gap-2 text-[10px] font-black uppercase tracking-wider text-gray-400 pb-1.5 border-b border-gray-100">
                                <div class="col-span-3">Da Quantità (Min)</div>
                                <div class="col-span-3">A Quantità (Max - opzionale)</div>
                                <div class="col-span-3">Tipo Regola</div>
                                <div class="col-span-2">Valore (% Sconto o € Netto)</div>
                                <div class="col-span-1 text-center">Azione</div>
                            </div>
                            <div id="general-tiers-container" class="space-y-2 mt-2">
                                <div class="general-tier-row grid grid-cols-12 gap-2 items-center text-xs">
                                    <div class="col-span-3">
                                        <input type="number" name="general_tiers[0][min_quantity]" value="1" min="1" required class="w-full text-xs rounded-lg border-gray-200 p-2 font-bold">
                                    </div>
                                    <div class="col-span-3">
                                        <input type="number" name="general_tiers[0][max_quantity]" value="10" min="1" placeholder="Illimitato" class="w-full text-xs rounded-lg border-gray-200 p-2">
                                    </div>
                                    <div class="col-span-3">
                                        <select name="general_tiers[0][discount_type]" class="w-full text-xs rounded-lg border-gray-200 p-2">
                                            <option value="percentage">Sconto % su Prezzo Base</option>
                                            <option value="fixed_price">Prezzo Netto Personalizzato (€)</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" step="0.01" min="0" name="general_tiers[0][discount_value]" value="10" required class="w-full text-xs rounded-lg border-gray-200 p-2 font-bold text-amber-700 bg-amber-50/20">
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <button type="button" onclick="removeGeneralTierRow(this)" class="text-rose-600 hover:text-rose-800 font-bold text-sm">✕</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold text-gray-700 uppercase mb-1">Descrizione / Note</label>
                        <textarea name="description" id="description" rows="2" 
                                  placeholder="Note sulle condizioni concordate con l'azienda..."
                                  class="w-full rounded-xl border-gray-200 p-3 text-sm focus:border-indigo-500">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('agent.price-lists.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-gray-200">
                            Annulla
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 shadow-md">
                            Crea Listino e Personalizza Prodotti
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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
                <div class="general-tier-row grid grid-cols-12 gap-2 items-center text-xs">
                    <div class="col-span-3">
                        <input type="number" name="general_tiers[${index}][min_quantity]" value="${defaultMin}" min="1" required class="w-full text-xs rounded-lg border-gray-200 p-2 font-bold">
                    </div>
                    <div class="col-span-3">
                        <input type="number" name="general_tiers[${index}][max_quantity]" value="" min="1" placeholder="Illimitato" class="w-full text-xs rounded-lg border-gray-200 p-2">
                    </div>
                    <div class="col-span-3">
                        <select name="general_tiers[${index}][discount_type]" class="w-full text-xs rounded-lg border-gray-200 p-2">
                            <option value="percentage">Sconto % su Prezzo Base</option>
                            <option value="fixed_price">Prezzo Netto Personalizzato (€)</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <input type="number" step="0.01" min="0" name="general_tiers[${index}][discount_value]" value="0" required class="w-full text-xs rounded-lg border-gray-200 p-2 font-bold text-amber-700 bg-amber-50/20">
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
    </script>
</x-agent-layout>
