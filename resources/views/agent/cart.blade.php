<x-agent-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
            {{ __('Il Tuo Carrello B2B') }}
        </h2>
    </x-slot>

    <div class="space-y-8">
        @if(empty($cart))
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-20 text-center">
                <span class="text-8xl block mb-6">🛒</span>
                <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight mb-2">Il carrello è vuoto</h3>
                <p class="text-gray-400 font-bold uppercase tracking-widest text-sm mb-10">Sfoglia il catalogo per iniziare una nuova raccolta ordini.</p>
                <a href="{{ route('agent.catalog') }}" class="inline-block bg-black border border-zinc-950 text-white px-10 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:border-yellow-400 hover:text-yellow-400 transition">Torna al Catalogo</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Items list -->
                <div class="lg:col-span-2 space-y-8">
                    @php
                        $groupedCart = [];
                        foreach($cart as $index => $item) {
                            $key = empty($item['delivery_date']) ? 'immediate' : $item['delivery_date'];
                            $groupedCart[$key][$index] = $item;
                        }
                    @endphp

                    @foreach($groupedCart as $key => $items)
                        @php
                            $groupTitle = $key === 'immediate' ? 'Pronta Consegna' : 'Consegna dal ' . $key;
                        @endphp
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight mb-3 pl-4 border-l-4 {{ $key === 'immediate' ? 'border-emerald-500' : 'border-amber-500' }}">{{ $groupTitle }}</h3>
                            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50/50">
                                        <tr class="text-xs font-black text-gray-500 uppercase tracking-widest border-b border-gray-100">
                                            <th class="px-8 py-4">Articolo</th>
                                            <th class="px-8 py-4 text-center">Dettagli</th>
                                            <th class="px-8 py-4 text-center">Prezzo</th>
                                            <th class="px-8 py-4 text-center">Q.tà</th>
                                            <th class="px-8 py-4 text-right">Subtotale</th>
                                            <th class="px-8 py-4 text-right">Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($items as $index => $item)
                                            @php
                                                $cartProd = \App\Models\B2bProduct::find($item['product_id'] ?? null);
                                                $cartImg = $cartProd && $cartProd->image ? (\Illuminate\Support\Str::startsWith($cartProd->image, ['http://', 'https://']) ? $cartProd->image : asset('storage/' . $cartProd->image)) : null;
                                            @endphp
                                            <tr class="group hover:bg-gray-50/50 transition">
                                                <td class="px-8 py-6">
                                                    <a href="{{ route('agent.product', $item['product_id']) }}" class="flex items-center space-x-4 group/item">
                                                        <div class="w-14 h-14 bg-gray-50 border border-gray-200 rounded-2xl flex items-center justify-center p-1.5 overflow-hidden shrink-0 group-hover/item:border-indigo-500 group-hover/item:shadow-md transition">
                                                            @if($cartImg)
                                                                <img src="{{ $cartImg }}" alt="{{ $item['name'] }}" class="max-w-full max-h-full object-contain group-hover/item:scale-105 transition duration-300">
                                                            @else
                                                                <span class="text-xl">👕</span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <p class="font-black text-gray-900 group-hover/item:text-indigo-600 transition uppercase leading-tight">{{ $item['name'] }}</p>
                                                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-tighter mb-1">{{ $item['brand'] }}</p>
                                                            @if(!empty($item['delivery_date']))
                                                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 border border-amber-100 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">
                                                                    📅 {{ $item['delivery_date'] }}
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 border border-emerald-100 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">
                                                                    ✓ Immediata
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </a>
                                                </td>
                                                <td class="px-8 py-6 text-center">
                                                    <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg font-bold text-xs uppercase tracking-tight">
                                                        {{ $item['color'] ?? 'Unico' }} / {{ $item['size'] }}
                                                    </span>
                                                </td>
                                                <td class="px-8 py-6 text-center uppercase">
                                                    <div class="flex flex-col items-center justify-center">
                                                        @if(isset($item['original_price']) && $item['original_price'] > $item['price'])
                                                            <span class="text-[10px] text-gray-400 line-through font-medium mb-0.5">
                                                                € {{ number_format($item['original_price'], 2, ',', '.') }}
                                                            </span>
                                                            <span class="font-black text-indigo-700 text-sm">
                                                                € {{ number_format($item['price'], 2, ',', '.') }}
                                                            </span>
                                                        @else
                                                            <span class="font-bold text-gray-900 text-sm">
                                                                € {{ number_format($item['price'], 2, ',', '.') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-8 py-6 text-center">
                                                    <form action="{{ route('agent.cart.update') }}" method="POST" class="inline-flex items-center justify-center gap-1 bg-gray-50 border border-gray-200 rounded-xl p-1 shadow-inner">
                                                        @csrf
                                                        <input type="hidden" name="index" value="{{ $index }}">
                                                        <button type="submit" name="action" value="decrease" 
                                                                class="w-7 h-7 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 font-black text-gray-700 flex items-center justify-center transition shadow-sm" title="Riduci quantità">
                                                            -
                                                        </button>
                                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" 
                                                               onchange="this.form.submit()" 
                                                               class="w-12 text-center rounded-lg border-transparent p-0 text-xs font-black text-slate-900 focus:border-indigo-500 focus:ring-0 bg-transparent">
                                                        <button type="submit" name="action" value="increase" 
                                                                class="w-7 h-7 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 font-black text-gray-700 flex items-center justify-center transition shadow-sm" title="Aumenta quantità">
                                                            +
                                                        </button>
                                                    </form>
                                                    @if(isset($item['available_qty']))
                                                        <div class="text-[9px] text-gray-400 font-bold mt-1 text-center">
                                                            Max disp: {{ $item['available_qty'] }} pz
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-8 py-6 text-right font-black text-indigo-900">
                                                    € {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}
                                                </td>
                                                <td class="px-8 py-6 text-right">
                                                    <form action="{{ route('agent.cart.remove', $index) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-rose-400 hover:text-rose-600 transition">
                                                            <span class="text-xl leading-none">×</span>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Checkout Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-10 space-y-8 sticky top-6">
                        <div>
                            @if(auth()->user()->role === 'customer')
                                <h3 class="font-black text-lg text-gray-900 uppercase tracking-tight mb-4 border-b border-gray-50 pb-2">Riepilogo Azienda</h3>
                                <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 mb-6">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Ordinante</p>
                                    <p class="font-black text-indigo-900 uppercase">{{ auth()->user()->b2bCustomer->business_name }}</p>
                                    <p class="text-[10px] text-gray-500 font-bold mt-1">P.IVA: {{ auth()->user()->b2bCustomer->vat_number ?? 'N/D' }}</p>
                                </div>
                                <form action="{{ route('agent.process_checkout') }}" method="POST" id="checkout-form">
                                    @csrf
                                    <div class="space-y-6">
                            @else
                                <h3 class="font-black text-lg text-gray-900 uppercase tracking-tight mb-4 border-b border-gray-50 pb-2">Selezione Cliente</h3>
                                <p class="text-xs text-gray-400 italic mb-6">Per procedere all'invio dell'ordine, seleziona il cliente nell'anagrafica autorizzata.</p>
                                
                                <form action="{{ route('agent.process_checkout') }}" method="POST" id="checkout-form">
                                    @csrf
                                    <div class="space-y-6">
                                        <div x-data="{ 
                                                search: '',
                                                show: false,
                                                selectedName: 'Seleziona un cliente...',
                                                selectedId: '',
                                                customers: [
                                                    @foreach($customers as $customer)
                                                    { id: '{{ $customer->id }}', name: '{{ addslashes($customer->business_name) }}', vat: '{{ $customer->vat_number }}' },
                                                    @endforeach
                                                ],
                                                get filteredCustomers() {
                                                    if (this.search === '') return this.customers;
                                                    return this.customers.filter(c => 
                                                        c.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                                        c.vat.toLowerCase().includes(this.search.toLowerCase())
                                                    );
                                                }
                                            }" class="relative">
                                            
                                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Cliente Autorizzato *</label>
                                            
                                            <!-- Custom Searchable Select -->
                                            <div class="relative">
                                                <button type="button" @click="show = !show" 
                                                        class="w-full bg-white border border-gray-200 rounded-2xl p-4 text-left text-base font-bold flex justify-between items-center focus:ring-2 focus:ring-indigo-500 transition">
                                                    <span x-text="selectedName" :class="selectedId ? 'text-gray-900' : 'text-gray-400'"></span>
                                                    <span class="text-xs">▼</span>
                                                </button>
                                                
                                                <input type="hidden" name="b2b_customer_id" x-model="selectedId" required>

                                                <div x-show="show" @click.away="show = false" 
                                                     class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden" 
                                                     x-cloak>
                                                    <div class="p-3 border-b border-gray-50">
                                                        <input type="text" x-model="search" placeholder="Cerca per nome o P.IVA..." 
                                                               class="w-full border-gray-100 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 p-3 bg-gray-50">
                                                    </div>
                                                    <div class="max-h-60 overflow-y-auto">
                                                        <template x-for="customer in filteredCustomers" :key="customer.id">
                                                            <div @click="selectedId = customer.id; selectedName = customer.name; show = false; search = ''" 
                                                                 class="px-4 py-3 hover:bg-indigo-50 cursor-pointer transition">
                                                                <p class="font-black text-gray-900 text-sm uppercase" x-text="customer.name"></p>
                                                                <p class="text-[10px] text-gray-400 font-bold" x-text="'P.IVA: ' + customer.vat"></p>
                                                            </div>
                                                        </template>
                                                        <div x-show="filteredCustomers.length === 0" class="p-4 text-center text-xs text-gray-400 italic">
                                                            Nessun cliente trovato per questa ricerca.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                            @endif

                                    <div>
                                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Note per la sede</label>
                                        <textarea name="notes" placeholder="Eventuali istruzioni speciali..." rows="4" class="w-full border-gray-200 rounded-2xl text-base focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                    </div>

                                    @php
                                        $generalTiers = collect();
                                        $currentCustomer = $customer ?? (auth()->user()->role === 'customer' ? auth()->user()->b2bCustomer : null);
                                        if ($currentCustomer && $currentCustomer->b2b_price_list_id) {
                                            $generalTiers = \App\Models\B2bPriceListItem::where('b2b_price_list_id', $currentCustomer->b2b_price_list_id)
                                                ->whereNull('b2b_product_id')
                                                ->orderBy('min_quantity', 'asc')
                                                ->get();
                                        }
                                        $totalCartQty = collect($cart)->sum('quantity');
                                    @endphp

                                    @if($generalTiers->count() > 0)
                                        <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6">
                                            <h4 class="font-black text-indigo-900 uppercase tracking-tight text-sm mb-3 flex items-center gap-2">
                                                <span class="text-lg">📉</span> Sconti Quantità
                                            </h4>
                                            <ul class="space-y-1">
                                                @foreach($generalTiers as $tier)
                                                    <li class="flex justify-between items-center text-xs font-bold text-indigo-600/70 p-2 border border-transparent">
                                                        <span>
                                                            Da {{ $tier->min_quantity }} {{ $tier->max_quantity ? 'a ' . $tier->max_quantity : 'in poi' }} pz
                                                        </span>
                                                        <span>
                                                            {{ $tier->discount_type === 'percentage' ? '-' . floatval($tier->discount_value) . '%' : '€ ' . number_format($tier->discount_value, 2, ',', '.') }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <p class="text-[10px] text-indigo-500 font-bold mt-3 leading-tight">
                                                Lo sconto si applica sul <strong>totale dei pezzi di ogni singolo ordine</strong>. Aumenta la quantità all'interno dello stesso gruppo di consegna per ottenere uno sconto maggiore!
                                            </p>
                                        </div>
                                    @endif

                                    <div class="bg-black border border-zinc-950 rounded-3xl p-6 text-white shadow-xl space-y-4">
                                        <div class="border-b border-zinc-800 pb-4 mb-4">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-[10px] font-bold uppercase text-zinc-400">Totale Carrello ({{ collect($cart)->sum('quantity') }} pz)</span>
                                                <span class="text-sm font-black text-zinc-300">€ {{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']), 2, ',', '.') }}</span>
                                            </div>
                                            <p class="text-[9px] text-zinc-500 italic">Il totale include tutti gli articoli attualmente nel carrello.</p>
                                        </div>

                                        @foreach($groupedCart as $key => $items)
                                            @php
                                                $groupTitle = $key === 'immediate' ? 'Pronta Consegna' : 'Consegna dal ' . $key;
                                                $groupQty = collect($items)->sum('quantity');
                                                $groupTotal = collect($items)->sum(fn($i) => $i['price'] * $i['quantity']);
                                            @endphp
                                            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
                                                <h5 class="text-xs font-black text-white uppercase tracking-tight mb-2 flex items-center gap-2">
                                                    <span class="{{ $key === 'immediate' ? 'text-emerald-400' : 'text-amber-400' }}">●</span> {{ $groupTitle }}
                                                </h5>
                                                <div class="flex justify-between items-center mb-1 text-xs">
                                                    <span class="text-zinc-400 font-bold">Pezzi</span>
                                                    <span class="font-black text-white">{{ $groupQty }}</span>
                                                </div>
                                                <div class="flex justify-between items-center mb-4">
                                                    <span class="text-xs text-zinc-400 font-bold">Totale</span>
                                                    <span class="text-lg font-black text-yellow-400">€ {{ number_format($groupTotal, 2, ',', '.') }}</span>
                                                </div>
                                                <button type="submit" name="delivery_group" value="{{ $key }}" class="w-full bg-white text-black hover:bg-yellow-400 hover:text-black py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition duration-300">
                                                    Invia questo Ordine
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-agent-layout>
