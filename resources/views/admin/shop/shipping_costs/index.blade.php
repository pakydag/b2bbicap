<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestione Spedizioni Shop</h2>
    </x-slot>

    <div class="py-12" x-data="{ search: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900">Configurazione Costi Spedizione</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Gestisci tutte le nazioni di consegna abilitate nello Shop e le relative tariffe.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <form action="{{ route('admin.shop.shipping_costs.init_eu') }}" method="POST" onsubmit="return confirm('Vuoi caricare/reimpostare le 26 nazioni dell\'Unione Europea a € 20,00 e l\'Italia a € 8,00?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-400 hover:bg-amber-500 text-slate-900 font-black text-xs uppercase tracking-wider rounded-xl shadow-sm transition-all cursor-pointer">
                            <span>🇪🇺</span>
                            <span>Carica Paesi UE (€ 20) + Italia (€ 8)</span>
                        </button>
                    </form>
                    <a href="{{ route('admin.shop.configuration') }}" class="text-sm font-bold text-slate-600 hover:text-black transition-colors">&larr; Configurazione Shop</a>
                </div>
            </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-400 p-4 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Configurazione Soglia -->
            <div class="lg:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 h-full">
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Spedizione Gratuita
                        </h3>
                        <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                            Imposta la soglia minima dell'ordine per offrire la spedizione gratuita e scegli se applicarla solo per le consegne in Italia o a tutte le nazioni. Imposta a 0 per disabilitare.
                        </p>

                        <form action="{{ route('admin.shop.shipping_costs.threshold') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label for="shop_free_shipping_threshold" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Soglia Minima Ordine (€)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-bold">€</span>
                                    </div>
                                    <input type="number" step="0.01" name="shop_free_shipping_threshold" id="shop_free_shipping_threshold" value="{{ $freeThreshold }}" class="bg-slate-50 border-slate-200 text-slate-900 text-lg font-bold rounded-xl focus:ring-black focus:border-black block w-full pl-10 p-3.5 transition-all" placeholder="Es. 89.00">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Applica Spedizione Gratuita a</label>
                                <div class="space-y-2.5">
                                    <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-slate-50/60 cursor-pointer transition-all has-checked:border-slate-900 has-checked:bg-white has-checked:shadow-sm">
                                        <input type="radio" name="shop_free_shipping_scope" value="italy" {{ ($freeShippingScope ?? 'italy') === 'italy' ? 'checked' : '' }} class="w-4 h-4 text-black focus:ring-black border-slate-300">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-extrabold text-slate-900 flex items-center gap-1.5">
                                                <span>🇮🇹</span> Solo Italia
                                            </span>
                                            <span class="text-[11px] text-slate-500">Solo per gli ordini con consegna in Italia</span>
                                        </div>
                                    </label>

                                    <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-slate-50/60 cursor-pointer transition-all has-checked:border-slate-900 has-checked:bg-white has-checked:shadow-sm">
                                        <input type="radio" name="shop_free_shipping_scope" value="all" {{ ($freeShippingScope ?? 'italy') === 'all' ? 'checked' : '' }} class="w-4 h-4 text-black focus:ring-black border-slate-300">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-extrabold text-slate-900 flex items-center gap-1.5">
                                                <span>🌍</span> Tutte le Nazioni
                                            </span>
                                            <span class="text-[11px] text-slate-500">Valida per tutte le 27 nazioni abilitate</span>
                                        </div>
                                    </label>
                                </div>
                            <div class="pt-4 border-t border-slate-100">
                                <label for="payment_contrassegno_fee" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center justify-between">
                                    <span>Supplemento Contrassegno (€)</span>
                                    <span class="text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Solo Italia 🇮🇹</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-bold">€</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" name="payment_contrassegno_fee" id="payment_contrassegno_fee" value="{{ $contrassegnoFee ?? '5.00' }}" class="bg-slate-50 border-slate-200 text-slate-900 text-lg font-bold rounded-xl focus:ring-black focus:border-black block w-full pl-10 p-3.5 transition-all" placeholder="5.00">
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1.5">
                                    Costo fisso addizionale per chi sceglie il pagamento alla consegna (valido solo per l'Italia).
                                </p>
                            </div>

                            <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-bold py-3.5 px-6 rounded-xl shadow transition-all flex items-center justify-center cursor-pointer">
                                Salva Impostazioni
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Elenco Nazioni e Costi -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 mb-8">
                    <div class="p-8">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                            <h3 class="text-xl font-bold text-slate-800 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Costi per Nazione ({{ $costs->count() }})
                            </h3>

                            <!-- Search Filter Input -->
                            <div class="w-full sm:w-64">
                                <input type="text" x-model="search" placeholder="Cerca nazione..." class="w-full bg-slate-50 border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-black focus:border-black">
                            </div>
                        </div>
                        
                        <!-- Form Aggiunta / Aggiornamento -->
                        <form action="{{ route('admin.shop.shipping_costs.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            @csrf
                            <div class="col-span-1">
                                <label for="nazione" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nazione</label>
                                <input type="text" name="nazione" id="nazione" required list="country_list" class="w-full bg-white border-slate-200 rounded-xl focus:ring-black focus:border-black p-3 text-sm font-semibold" placeholder="Es. Spagna">
                                <datalist id="country_list">
                                    <option value="Italia">
                                    <option value="Austria">
                                    <option value="Belgio">
                                    <option value="Bulgaria">
                                    <option value="Cipro">
                                    <option value="Croazia">
                                    <option value="Danimarca">
                                    <option value="Estonia">
                                    <option value="Finlandia">
                                    <option value="Francia">
                                    <option value="Germania">
                                    <option value="Grecia">
                                    <option value="Irlanda">
                                    <option value="Lettonia">
                                    <option value="Lituania">
                                    <option value="Lussemburgo">
                                    <option value="Malta">
                                    <option value="Paesi Bassi">
                                    <option value="Polonia">
                                    <option value="Portogallo">
                                    <option value="Repubblica Ceca">
                                    <option value="Romania">
                                    <option value="Slovacchia">
                                    <option value="Slovenia">
                                    <option value="Spagna">
                                    <option value="Svezia">
                                    <option value="Ungheria">
                                    <option value="Svizzera">
                                    <option value="Regno Unito">
                                </datalist>
                            </div>
                            <div class="col-span-1">
                                <label for="costo" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Costo Spedizione (€)</label>
                                <input type="number" step="0.01" min="0" name="costo" id="costo" required class="w-full bg-white border-slate-200 rounded-xl focus:ring-black focus:border-black p-3 text-sm font-bold" placeholder="Es. 20.00">
                            </div>
                            <div class="col-span-1 flex items-end">
                                <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-bold py-3 px-6 rounded-xl transition-all cursor-pointer shadow-sm">
                                    Salva Nazione
                                </button>
                            </div>
                        </form>

                        <!-- Tabella Elenco con Modifica Diretta Inline -->
                        <div class="overflow-x-auto max-h-[600px] overflow-y-auto border border-slate-100 rounded-xl">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead class="bg-slate-50 sticky top-0 z-10">
                                    <tr>
                                        <th class="px-5 py-3.5 text-left text-xs font-extrabold text-slate-500 uppercase tracking-wider">Nazione</th>
                                        <th class="px-5 py-3.5 text-left text-xs font-extrabold text-slate-500 uppercase tracking-wider">Costo Spedizione (€)</th>
                                        <th class="px-5 py-3.5 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 bg-white">
                                    @forelse($costs as $cost)
                                        <tr class="hover:bg-slate-50/70 transition-colors"
                                            x-show="!search || '{{ strtolower($cost->nazione) }}'.includes(search.toLowerCase())">
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm font-bold text-slate-800">
                                                <div class="flex items-center gap-2">
                                                    <span>{{ $cost->nazione }}</span>
                                                    @if(strtolower($cost->nazione) === 'italia')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                                            🇮🇹 Predefinito
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap">
                                                <form action="{{ route('admin.shop.shipping_costs.update', $cost) }}" method="POST" class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <span class="text-xs font-bold text-slate-400">€</span>
                                                    <input type="number" step="0.01" min="0" name="costo" value="{{ $cost->costo }}" class="w-24 bg-slate-50 border border-slate-200 rounded-lg py-1 px-2 text-sm font-bold text-slate-900 focus:bg-white focus:ring-1 focus:ring-black">
                                                    <button type="submit" class="px-2.5 py-1 bg-slate-800 hover:bg-black text-white text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-2xs" title="Aggiorna tariffa per {{ $cost->nazione }}">
                                                        Aggiorna
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-right text-sm">
                                                <form action="{{ route('admin.shop.shipping_costs.destroy', $cost) }}" method="POST" class="inline" onsubmit="return confirm('Eliminare la nazione {{ $cost->nazione }} dalle destinazioni di spedizione?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer">
                                                        Elimina
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">
                                                Nessun costo configurato. Clicca su "Carica Paesi UE (€ 20) + Italia (€ 8)" per pre-popolare le destinazioni.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
</x-app-layout>
