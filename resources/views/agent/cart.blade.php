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
            <div class="flex flex-col 2xl:flex-row gap-8 items-start min-w-0">
                <!-- Items list (Massima Larghezza e Visibilità) -->
                <div class="w-full 2xl:flex-1 space-y-8 min-w-0">
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
                            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight mb-4 pl-4 border-l-4 {{ $key === 'immediate' ? 'border-emerald-500' : 'border-amber-500' }}">{{ $groupTitle }}</h3>
                            <div class="bg-white rounded-3xl sm:rounded-[36px] shadow-sm border border-gray-100 overflow-hidden min-w-0">
                                <div class="overflow-x-auto w-full min-w-0">
                                    <table class="w-full text-left">
                                        <thead class="bg-gray-50/75">
                                            <tr class="text-xs font-black text-gray-500 uppercase tracking-widest border-b border-gray-100">
                                                <th class="px-6 py-4">Articolo</th>
                                                <th class="px-4 py-4 text-center">Dettagli</th>
                                                <th class="px-4 py-4 text-center">Prezzo Cad.</th>
                                                <th class="px-4 py-4 text-center">Quantità</th>
                                                <th class="px-6 py-4 text-right">Subtotale</th>
                                                <th class="px-4 py-4 text-center">Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                        @foreach($items as $index => $item)
                                            @php
                                                $cartProd = \App\Models\B2bProduct::find($item['product_id'] ?? null);
                                                $cartImg = $cartProd && $cartProd->image ? (\Illuminate\Support\Str::startsWith($cartProd->image, ['http://', 'https://']) ? $cartProd->image : asset('storage/' . $cartProd->image)) : null;
                                                $isProductException = !empty($item['price_details']['is_product_exception']);
                                                $ruleSummary = $item['price_details']['rule_summary'] ?? null;
                                                $originalPrice = $item['original_price'] ?? ($cartProd ? $cartProd->price : $item['price']);
                                            @endphp
                                            <tr class="group hover:bg-gray-50/50 transition">
                                                <td class="px-6 py-5">
                                                    <a href="{{ route('agent.product', $item['product_id']) }}" class="flex items-center space-x-4 group/item">
                                                        <div class="w-16 h-16 bg-gray-50 border border-gray-200 rounded-2xl flex items-center justify-center p-1.5 overflow-hidden shrink-0 group-hover/item:border-indigo-500 shadow-sm transition">
                                                            @if($cartImg)
                                                                <img src="{{ $cartImg }}" alt="{{ $item['name'] }}" class="max-w-full max-h-full object-contain group-hover/item:scale-105 transition duration-300">
                                                            @else
                                                                <span class="text-2xl">👕</span>
                                                            @endif
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="font-black text-gray-900 group-hover/item:text-indigo-600 transition uppercase text-sm sm:text-base leading-tight">{{ $item['name'] }}</p>
                                                            <p class="text-xs text-gray-400 font-black uppercase tracking-wider mt-0.5 mb-1.5">{{ $item['brand'] }}</p>
                                                            <div class="flex flex-wrap items-center gap-1.5">
                                                                @if(!empty($item['delivery_date']))
                                                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 border border-amber-200 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider">
                                                                        📅 {{ $item['delivery_date'] }}
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider">
                                                                        ✓ Immediata
                                                                    </span>
                                                                @endif

                                                                @if($isProductException)
                                                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-900 border border-amber-200 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider">
                                                                        ⭐ Prezzo Riservato
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </a>
                                                </td>
                                                <td class="px-4 py-5 text-center whitespace-nowrap">
                                                    <span class="bg-indigo-50 text-indigo-800 border border-indigo-100 px-3 py-1.5 rounded-xl font-black text-xs uppercase tracking-wider">
                                                        {{ $item['color'] ?? 'Unico' }} / {{ $item['size'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-5 text-center uppercase whitespace-nowrap">
                                                    <div class="flex flex-col items-center justify-center">
                                                        @if($isProductException)
                                                            @if($originalPrice > $item['price'])
                                                                <span class="text-xs text-gray-400 line-through font-medium mb-0.5">
                                                                    € {{ number_format($originalPrice, 2, ',', '.') }}
                                                                </span>
                                                            @endif
                                                            <span class="font-black text-indigo-700 text-base leading-none mb-1">
                                                                € {{ number_format($item['price'], 2, ',', '.') }}
                                                            </span>
                                                            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-tight" title="Prezzo calcolato da regola specifica / listino personalizzato">
                                                                ⭐ Listino Personalizzato
                                                            </span>
                                                            @if($ruleSummary)
                                                                <span class="text-[9px] text-gray-500 font-bold mt-0.5 tracking-tight">
                                                                    {{ $ruleSummary }}
                                                                </span>
                                                            @endif
                                                        @elseif(isset($item['original_price']) && $item['original_price'] > $item['price'])
                                                            <span class="text-xs text-gray-400 line-through font-medium mb-0.5">
                                                                € {{ number_format($item['original_price'], 2, ',', '.') }}
                                                            </span>
                                                            <span class="font-black text-indigo-700 text-base leading-none mb-1">
                                                                € {{ number_format($item['price'], 2, ',', '.') }}
                                                            </span>
                                                            <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-tight">
                                                                📉 Sconto Quantità
                                                            </span>
                                                            @if($ruleSummary)
                                                                <span class="text-[9px] text-indigo-500 font-bold mt-0.5 tracking-tight">
                                                                    {{ $ruleSummary }}
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="font-black text-gray-900 text-base">
                                                                € {{ number_format($item['price'], 2, ',', '.') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-5 text-center whitespace-nowrap">
                                                    <form action="{{ route('agent.cart.update') }}" method="POST" class="inline-flex items-center justify-center gap-1 bg-gray-50 border border-gray-200 rounded-xl p-1 shadow-inner">
                                                        @csrf
                                                        <input type="hidden" name="index" value="{{ $index }}">
                                                        <button type="submit" name="action" value="decrease" 
                                                                class="w-7 h-7 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 font-black text-gray-700 flex items-center justify-center transition shadow-sm text-sm" title="Riduci quantità">
                                                            -
                                                        </button>
                                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" 
                                                                onchange="this.form.submit()" 
                                                                class="w-12 text-center rounded-lg border-transparent p-0 text-sm font-black text-slate-900 focus:border-indigo-500 focus:ring-0 bg-transparent">
                                                        <button type="submit" name="action" value="increase" 
                                                                class="w-7 h-7 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 font-black text-gray-700 flex items-center justify-center transition shadow-sm text-sm" title="Aumenta quantità">
                                                            +
                                                        </button>
                                                    </form>
                                                    @if(isset($item['available_qty']))
                                                        <div class="text-[10px] text-gray-400 font-bold mt-1 text-center">
                                                            Max: {{ $item['available_qty'] }} pz
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-5 text-right font-black text-indigo-900 whitespace-nowrap text-base sm:text-lg">
                                                    € {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-5 text-center whitespace-nowrap">
                                                    <form action="{{ route('agent.cart.remove', $index) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-rose-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-xl transition" title="Rimuovi dal carrello">
                                                            <span class="text-2xl leading-none">&times;</span>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Checkout Sidebar -->
                <div class="w-full 2xl:w-[420px] 2xl:shrink-0 min-w-0">
                    <div class="bg-white rounded-3xl sm:rounded-[40px] shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6 sticky top-6">
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
                                <h3 class="font-black text-lg text-gray-900 uppercase tracking-tight mb-2 border-b border-gray-50 pb-2">Selezione Cliente & Listino</h3>
                                
                                @if(!$customer)
                                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-4 text-xs font-bold text-amber-900 flex items-start gap-3 shadow-sm">
                                        <span class="text-xl">⚠️</span>
                                        <div>
                                            <p class="font-black uppercase text-xs">Nessun Cliente Attivo</p>
                                            <p class="text-[11px] text-amber-800 font-medium mt-0.5 leading-relaxed">I prezzi attuali nel carrello sono a Listino Base. Seleziona un'azienda autorizzata per applicare il listino dedicato prima dell'invio.</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-4 flex items-center justify-between shadow-sm">
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 block">Cliente Attivo</span>
                                            <p class="font-black text-emerald-950 text-sm uppercase">{{ $customer->business_name }}</p>
                                            <p class="text-[10px] text-emerald-800 font-medium">P.IVA: {{ $customer->vat_number ?? 'N/D' }}</p>
                                        </div>
                                        @if($customer->priceList)
                                            <span class="bg-emerald-600 text-white text-[9px] font-black uppercase px-2.5 py-1 rounded-full shadow-sm">
                                                🏷️ {{ $customer->priceList->name }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <div x-data="{ 
                                        search: '',
                                        show: false,
                                        selectedName: '{{ $customer ? addslashes(($customer->code ? '[' . $customer->code . '] ' : '') . $customer->business_name) : 'Seleziona un cliente...' }}',
                                        selectedId: '{{ $customer ? $customer->id : '' }}',
                                        customers: [
                                            @foreach($customers as $c)
                                            { 
                                                id: '{{ $c->id }}', 
                                                code: '{{ addslashes($c->code ?? '') }}', 
                                                name: '{{ addslashes($c->business_name) }}', 
                                                vat: '{{ $c->vat_number }}',
                                                priceList: '{{ addslashes($c->priceList?->name ?? 'Listino Base') }}'
                                            },
                                            @endforeach
                                        ],
                                        get filteredCustomers() {
                                            if (this.search === '') return this.customers;
                                            const s = this.search.toLowerCase();
                                            return this.customers.filter(c => 
                                                c.name.toLowerCase().includes(s) || 
                                                (c.code && c.code.toLowerCase().includes(s)) || 
                                                c.vat.toLowerCase().includes(s) ||
                                                c.priceList.toLowerCase().includes(s)
                                            );
                                        },
                                        changeCustomer(cId) {
                                            const form = document.createElement('form');
                                            form.method = 'POST';
                                            form.action = '{{ route('agent.select_customer') }}';
                                            
                                            const csrf = document.createElement('input');
                                            csrf.type = 'hidden';
                                            csrf.name = '_token';
                                            csrf.value = '{{ csrf_token() }}';
                                            form.appendChild(csrf);

                                            const input = document.createElement('input');
                                            input.type = 'hidden';
                                            input.name = 'b2b_customer_id';
                                            input.value = cId;
                                            form.appendChild(input);

                                            document.body.appendChild(form);
                                            form.submit();
                                        }
                                    }" class="relative mb-6">
                                    
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Cambia Cliente / Listino *</label>
                                    
                                    <!-- Custom Searchable Select -->
                                    <div class="relative">
                                        <button type="button" @click="show = !show" 
                                                class="w-full bg-white border border-gray-200 rounded-2xl p-3.5 text-left text-sm font-bold flex justify-between items-center focus:ring-2 focus:ring-yellow-400 transition shadow-sm cursor-pointer hover:border-yellow-400">
                                            <span x-text="selectedName" :class="selectedId ? 'text-gray-900 font-black' : 'text-gray-400'"></span>
                                            <span class="text-xs text-gray-400">▼</span>
                                        </button>

                                        <div x-show="show" @click.away="show = false" 
                                             class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden" 
                                             x-cloak>
                                            <div class="p-3 border-b border-gray-50 bg-gray-50">
                                                <input type="text" x-model="search" placeholder="Cerca per codice, nome o P.IVA..." 
                                                       class="w-full border-gray-200 rounded-xl text-xs focus:ring-yellow-400 focus:border-yellow-400 p-2.5 bg-white">
                                            </div>
                                            <div class="max-h-60 overflow-y-auto divide-y divide-gray-50">
                                                <template x-for="c in filteredCustomers" :key="c.id">
                                                    <div @click="changeCustomer(c.id)" 
                                                         class="px-4 py-3 hover:bg-amber-50 cursor-pointer transition flex items-center justify-between group">
                                                        <div>
                                                            <div class="flex items-center gap-2">
                                                                <template x-if="c.code">
                                                                    <span class="text-[9px] font-mono font-bold bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded border border-gray-200" x-text="c.code"></span>
                                                                </template>
                                                                <p class="font-black text-gray-900 text-xs uppercase group-hover:text-amber-900" x-text="c.name"></p>
                                                            </div>
                                                            <div class="flex items-center gap-2 mt-0.5 text-[10px] text-gray-400 font-medium">
                                                                <span x-text="'P.IVA: ' + (c.vat || 'N/D')"></span>
                                                                <span class="text-amber-700 font-bold" x-text="'• ' + c.priceList"></span>
                                                            </div>
                                                        </div>
                                                        <span class="text-[10px] font-black uppercase text-amber-700 opacity-0 group-hover:opacity-100 transition">
                                                            Applica Listino →
                                                        </span>
                                                    </div>
                                                </template>
                                                <div x-show="filteredCustomers.length === 0" class="p-4 text-center text-xs text-gray-400 italic">
                                                    Nessun cliente trovato per questa ricerca.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <form action="{{ route('agent.process_checkout') }}" method="POST" id="checkout-form">
                                    @csrf
                                    <input type="hidden" name="b2b_customer_id" value="{{ $customer ? $customer->id : '' }}">
                                    <div class="space-y-6">
                            @endif

                                    <div>
                                        <label class="block text-xs font-black text-gray-700 uppercase tracking-widest mb-1.5 flex items-center justify-between">
                                            <span>Riferimento Ordine Interno *</span>
                                            <span class="text-[10px] text-amber-600 font-bold lowercase bg-amber-50 px-2 py-0.5 rounded border border-amber-200">obbligatorio</span>
                                        </label>
                                        <input type="text" name="internal_reference" required 
                                               placeholder="Es. ORD-2026-001 o N. Ordine Interno..." 
                                               value="{{ old('internal_reference') }}" 
                                               class="w-full border-gray-300 rounded-2xl text-sm font-bold text-gray-900 focus:ring-yellow-400 focus:border-yellow-400 p-3.5 bg-gray-50/50 shadow-inner">
                                        <p class="text-[10px] text-gray-400 font-medium mt-1 leading-tight">
                                            Questo riferimento sarà visualizzato negli elenchi ordini, nei documenti PDF e nelle email di conferma.
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Note per la sede</label>
                                        <textarea name="notes" placeholder="Eventuali istruzioni speciali..." rows="4" class="w-full border-gray-200 rounded-2xl text-base focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                    </div>

                                    @php
                                        $currentCustomer = $customer ?? (auth()->user()->role === 'customer' ? auth()->user()->b2bCustomer : null);
                                        if (!isset($assignedPriceList) && $currentCustomer && $currentCustomer->b2b_price_list_id) {
                                            $assignedPriceList = \App\Models\B2bPriceList::find($currentCustomer->b2b_price_list_id);
                                        }
                                        if ((!isset($generalTiers) || $generalTiers->isEmpty()) && $assignedPriceList) {
                                            $generalTiers = \App\Models\B2bPriceListItem::where('b2b_price_list_id', $assignedPriceList->id)
                                                ->whereNull('b2b_product_id')
                                                ->orderBy('min_quantity', 'asc')
                                                ->get();
                                        }
                                        if ((!isset($productSpecificTiers) || $productSpecificTiers->isEmpty()) && $assignedPriceList) {
                                            $productSpecificTiers = \App\Models\B2bPriceListItem::with('product.brand')
                                                ->where('b2b_price_list_id', $assignedPriceList->id)
                                                ->whereNotNull('b2b_product_id')
                                                ->orderBy('b2b_product_id')
                                                ->orderBy('min_quantity', 'asc')
                                                ->get();
                                        }
                                        $cartProductIds = collect($cart)->pluck('product_id')->filter()->unique()->toArray();
                                        $totalCartQty = collect($cart)->sum('quantity');
                                    @endphp

                                    @if($assignedPriceList || ($generalTiers && $generalTiers->count() > 0) || ($productSpecificTiers && $productSpecificTiers->count() > 0))
                                        <div class="bg-indigo-50/70 border border-indigo-100 rounded-3xl p-6 space-y-5">
                                            <!-- Listino Header -->
                                            <div class="flex items-center justify-between border-b border-indigo-100 pb-3">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="text-xl">📋</span>
                                                    <div>
                                                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest block leading-none">Listino Prezzi Applicato</span>
                                                        <h4 class="font-black text-indigo-950 uppercase tracking-tight text-sm mt-0.5">
                                                            {{ $assignedPriceList ? $assignedPriceList->name : 'Condizioni Standard' }}
                                                        </h4>
                                                    </div>
                                                </div>
                                                @if($assignedPriceList)
                                                    <span class="bg-indigo-600 text-white text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                                        Attivo
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- 1. Fasce Generali -->
                                            @if($generalTiers && $generalTiers->count() > 0)
                                                <div>
                                                    <h5 class="font-black text-indigo-900 uppercase tracking-wider text-xs mb-2 flex items-center gap-1.5">
                                                        <span>📉</span> Sconti Quantità Generali (Catalogo)
                                                    </h5>
                                                    <div class="bg-white/90 rounded-2xl border border-indigo-100 p-3 shadow-2xs space-y-1.5">
                                                        @foreach($generalTiers as $tier)
                                                            <div class="flex justify-between items-center text-xs font-bold text-indigo-950">
                                                                <span class="text-gray-600">
                                                                    Da {{ $tier->min_quantity }} {{ $tier->max_quantity ? 'a ' . $tier->max_quantity : 'in poi' }} pz
                                                                </span>
                                                                <span class="font-black text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">
                                                                    @if($tier->discount_type === 'percentage')
                                                                        -{{ floatval($tier->discount_value) }}%{{ $tier->discount_2 > 0 ? ' +' . floatval($tier->discount_2) . '%' : '' }}{{ $tier->discount_3 > 0 ? ' +' . floatval($tier->discount_3) . '%' : '' }}
                                                                    @elseif($tier->discount_type === 'fixed_price')
                                                                        € {{ number_format($tier->discount_value, 2, ',', '.') }}{{ $tier->discount_2 > 0 ? ' -' . floatval($tier->discount_2) . '%' : '' }}{{ $tier->discount_3 > 0 ? ' -' . floatval($tier->discount_3) . '%' : '' }}
                                                                    @else
                                                                        -€ {{ number_format($tier->discount_value, 2, ',', '.') }}{{ $tier->discount_2 > 0 ? ' -' . floatval($tier->discount_2) . '%' : '' }}{{ $tier->discount_3 > 0 ? ' -' . floatval($tier->discount_3) . '%' : '' }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <p class="text-[10px] text-indigo-500 font-semibold mt-1.5 leading-tight">
                                                        Validi su tutti i prodotti standard non soggetti a listino personalizzato.
                                                    </p>
                                                </div>
                                            @endif

                                            <!-- 2. Listino Personalizzato / Eccezioni per Prodotto -->
                                            @if($productSpecificTiers && $productSpecificTiers->count() > 0)
                                                @php
                                                    $groupedExceptions = $productSpecificTiers->groupBy('b2b_product_id');
                                                @endphp
                                                <div>
                                                    <h5 class="font-black text-amber-900 uppercase tracking-wider text-xs mb-2 flex items-center gap-1.5">
                                                        <span>⭐</span> Listino Personalizzato Articoli (Eccezioni)
                                                    </h5>
                                                    <div class="space-y-2">
                                                        @foreach($groupedExceptions as $prodId => $pTiers)
                                                            @php
                                                                $exceptionProduct = $pTiers->first()->product;
                                                                $inCart = in_array($prodId, $cartProductIds);
                                                            @endphp
                                                            @if($exceptionProduct)
                                                                <div class="rounded-2xl border p-3 transition {{ $inCart ? 'bg-amber-50 border-amber-300 ring-2 ring-amber-200/60 shadow-sm' : 'bg-white/90 border-indigo-100' }}">
                                                                    <div class="flex items-start justify-between gap-2 mb-1.5">
                                                                        <div class="min-w-0">
                                                                            <p class="font-black text-xs text-gray-900 uppercase leading-tight truncate">
                                                                                {{ $exceptionProduct->code ? $exceptionProduct->code . ' - ' : '' }}{{ $exceptionProduct->name }}
                                                                            </p>
                                                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                                                                                Listino Base: € {{ number_format($exceptionProduct->price, 2, ',', '.') }}
                                                                            </p>
                                                                        </div>
                                                                        @if($inCart)
                                                                            <span class="inline-flex items-center gap-0.5 bg-amber-500 text-white text-[9px] font-black uppercase px-2 py-0.5 rounded-full shrink-0 shadow-xs">
                                                                                ✓ Nel Carrello
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                    
                                                                    <div class="space-y-1">
                                                                        @foreach($pTiers as $pTier)
                                                                            @php
                                                                                $calcPrice = $pTier->calculateUnitPrice($exceptionProduct->price);
                                                                            @endphp
                                                                            <div class="flex justify-between items-center text-[11px] font-bold bg-white px-2.5 py-1.5 rounded-lg border border-amber-100 shadow-2xs">
                                                                                <span class="text-gray-600">
                                                                                    @if($pTier->min_quantity > 1 || !empty($pTier->max_quantity))
                                                                                        Da {{ $pTier->min_quantity }} {{ $pTier->max_quantity ? 'a ' . $pTier->max_quantity : 'in poi' }} pz:
                                                                                    @else
                                                                                        Prezzo Riservato:
                                                                                    @endif
                                                                                </span>
                                                                                <div class="text-right">
                                                                                    <span class="text-indigo-900 font-black">
                                                                                        € {{ number_format($calcPrice, 2, ',', '.') }}
                                                                                    </span>
                                                                                    <span class="text-[9px] text-gray-400 font-medium block leading-none">
                                                                                        ({{ $pTier->rule_summary }})
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <p class="text-[10px] text-amber-800 font-semibold mt-1.5 leading-tight">
                                                        Prezzi netti e sconti dedicati applicati direttamente ai rispettivi articoli.
                                                    </p>
                                                </div>
                                            @endif
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
