<x-agent-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
                {{ __('I Miei Listini Prezzi & Sconti') }}
            </h2>
            <a href="{{ route('agent.price-lists.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-wider hover:bg-indigo-700 shadow-sm transition">
                + Nuovo Listino
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-8">
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

            <!-- Tabella Listini Creati dall'Agente -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-black text-gray-800 uppercase tracking-wider mb-4 border-b pb-3">Listini Personalizzati</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[11px] font-black text-gray-400 uppercase tracking-widest">
                                <th class="py-3 px-4">Nome Listino</th>
                                <th class="py-3 px-4">Sconto Generale %</th>
                                <th class="py-3 px-4 text-center">Fasce Prodotto Specifiche</th>
                                <th class="py-3 px-4 text-center">Aziende Associate</th>
                                <th class="py-3 px-4 text-right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @forelse($priceLists as $list)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-4 font-bold text-slate-900">
                                        {{ $list->name }}
                                        @if($list->description)
                                            <p class="text-xs font-normal text-gray-500 mt-0.5">{{ $list->description }}</p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($list->general_discount_percent > 0)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-amber-50 text-amber-700 border border-amber-200">
                                                -{{ number_format($list->general_discount_percent, 1) }}% su tutto
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 font-medium">Nessuno sconto globale</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                            {{ $list->items_count }} {{ $list->items_count === 1 ? 'regola' : 'regole' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($list->customers->count() > 0)
                                            @foreach($list->customers as $cust)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    🏢 {{ $cust->business_name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-xs text-gray-400 font-medium">Nessuna azienda</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('agent.price-lists.edit', $list) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                                Modifica / Fasce
                                            </a>
                                            <form action="{{ route('agent.price-lists.destroy', $list) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo listino?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition">
                                                    Elimina
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500 text-sm">
                                        Non hai ancora creato alcun listino. Clicca su <strong>"+ Nuovo Listino"</strong> per iniziarne uno.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Abbinamento Aziende / Clienti al Listino -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-black text-gray-800 uppercase tracking-wider mb-2">Abbinamento Listini alle Tue Aziende</h3>
                <p class="text-xs text-gray-500 mb-6">Assegna un listino personalizzato a ciascuna azienda gestita. Se lasci l'opzione "Prezzi Standard", all'azienda verranno applicati i prezzi di listino senza sconti particolari.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($customers as $customer)
                        <div class="border border-gray-100 rounded-2xl p-4 bg-gray-50/50 flex flex-col justify-between space-y-3">
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $customer->business_name }}</h4>
                                <p class="text-xs text-gray-500">P.IVA: {{ $customer->vat_number ?: 'N/D' }}</p>
                            </div>

                            <form action="{{ route('agent.price-lists.assign_customer') }}" method="POST" class="pt-2 border-t border-gray-100">
                                @csrf
                                <input type="hidden" name="b2b_customer_id" value="{{ $customer->id }}">
                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Listino Applicato:</label>
                                <div class="flex gap-2">
                                    <select name="b2b_price_list_id" class="flex-1 text-xs rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 p-2">
                                        <option value="">Prezzi Standard (Nessun Listino)</option>
                                        @foreach($priceLists as $list)
                                            <option value="{{ $list->id }}" {{ $customer->b2b_price_list_id == $list->id ? 'selected' : '' }}>
                                                {{ $list->name }} {{ $list->general_discount_percent > 0 ? "(-{$list->general_discount_percent}%)" : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                                        Salva
                                    </button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="col-span-full py-6 text-center text-gray-500 text-sm">
                            Nessuna azienda associata al tuo account di agente.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-agent-layout>
