<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Ordini B2B Ricevuti') }}
            </h2>
            <span class="text-xs font-bold text-gray-500 uppercase">
                Totale Ordini: {{ $orders->count() }}
            </span>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Barra Filtri Ordini -->
        <div class="bg-white shadow-sm rounded-2xl p-5 border border-gray-100">
            <form method="GET" action="{{ route('admin.b2b.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <!-- Filtro Agente -->
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Filtra per Agente</label>
                    <select name="agent_id" onchange="this.form.submit()" class="w-full rounded-xl border-gray-200 shadow-sm text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400">
                        <option value="">-- Tutti gli Agenti --</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }} {{ $agent->surname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro Azienda/Cliente -->
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Filtra per Azienda</label>
                    <select name="customer_id" onchange="this.form.submit()" class="w-full rounded-xl border-gray-200 shadow-sm text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400">
                        <option value="">-- Tutte le Aziende --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->business_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro Stato -->
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Stato Ordine</label>
                    <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border-gray-200 shadow-sm text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400">
                        <option value="">-- Tutti gli Stati --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>In Attesa</option>
                        <option value="revision_pending" {{ request('status') == 'revision_pending' ? 'selected' : '' }}>Attesa Cliente</option>
                        <option value="customer_approved" {{ request('status') == 'customer_approved' ? 'selected' : '' }}>Approvato da Cliente</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confermato</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annullato</option>
                    </select>
                </div>

                <!-- Pulsanti Filtro e Reset -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black py-2.5 px-4 rounded-xl shadow transition duration-200 text-xs uppercase tracking-wider">
                        🔍 Filtra
                    </button>
                    @if(request()->hasAny(['agent_id', 'customer_id', 'status', 'search']))
                        <a href="{{ route('admin.b2b.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-black py-2.5 px-3 rounded-xl transition text-xs uppercase flex items-center justify-center">
                            ✖ Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Card Tabella & Lista Ordini -->
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden min-w-0">
            
            <!-- Vista Mobile (< sm) -->
            <div class="sm:hidden divide-y divide-gray-100">
                @forelse($orders as $order)
                    @php
                        $statusClasses = [
                            'pending' => 'bg-amber-100 text-amber-900 border-amber-300',
                            'revision_pending' => 'bg-orange-100 text-orange-900 border-orange-300',
                            'customer_approved' => 'bg-blue-100 text-blue-900 border-blue-300',
                            'customer_rejected' => 'bg-rose-100 text-rose-900 border-rose-300',
                            'confirmed' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                            'cancelled' => 'bg-slate-100 text-slate-800 border-slate-300',
                        ];
                    @endphp
                    <div class="p-4 space-y-3 relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $order->status == 'pending' ? 'bg-yellow-400' : ($order->status == 'confirmed' ? 'bg-emerald-500' : 'bg-slate-400') }}"></div>
                        
                        <div class="flex justify-between items-start pl-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-black text-gray-900">Ordine #{{ $order->id }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold">• {{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($order->internal_reference)
                                    <div class="mt-0.5">
                                        <span class="inline-flex items-center gap-1 text-[10px] bg-slate-100 text-slate-800 border border-slate-200 px-1.5 py-0.5 rounded font-mono font-black">
                                            🏷️ Rif: {{ $order->internal_reference }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-black border {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                {{ $order->status_label }}
                            </span>
                        </div>
                        
                        <div class="pl-2 space-y-1">
                            <p class="text-xs text-gray-900 font-bold uppercase truncate">{{ $order->customer->business_name }}</p>
                            <p class="text-[11px] text-gray-500 font-medium">Agente: <span class="font-bold text-slate-800">{{ $order->agent->name }} {{ $order->agent->surname }}</span></p>
                        </div>

                        <div class="pl-2 flex justify-between items-baseline pt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Totale Imponibile:</span>
                            <span class="text-base font-black text-slate-900">€ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center gap-2 pt-2 border-t border-gray-100 pl-2">
                            <a href="{{ route('admin.b2b.orders.pdf', $order) }}" target="_blank" class="flex-1 bg-yellow-400 hover:bg-yellow-300 text-slate-950 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider shadow-sm transition text-center flex items-center justify-center gap-1.5">
                                <span>🖨️</span>
                                <span>SCARICA PDF</span>
                            </a>
                            <a href="{{ route('admin.b2b.orders.edit', $order) }}" class="flex-1 bg-black hover:bg-zinc-800 text-white py-2.5 rounded-xl text-xs font-black uppercase tracking-wider shadow-sm transition text-center">
                                GESTISCI
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-gray-400 font-bold uppercase tracking-widest">
                        Nessun ordine B2B ricevuto.
                    </div>
                @endforelse
            </div>

            <!-- Vista Desktop & Tablet (>= sm) -->
            <div class="hidden sm:block w-full overflow-x-auto min-w-0">
                <table class="w-full divide-y divide-gray-100 text-left">
                    <thead class="bg-gray-50/80">
                        <tr class="text-[11px] font-black text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-3.5 whitespace-nowrap">Ordine # / Rif. Interno</th>
                            <th class="px-3 py-3.5 whitespace-nowrap">Data</th>
                            <th class="px-3 py-3.5">Agente</th>
                            <th class="px-3 py-3.5">Cliente B2B</th>
                            <th class="px-3 py-3.5 text-right whitespace-nowrap">Totale</th>
                            <th class="px-3 py-3.5 text-center whitespace-nowrap">Stato</th>
                            <th class="px-4 py-3.5 text-right whitespace-nowrap">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($orders as $order)
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-100 text-amber-900 border-amber-300',
                                    'revision_pending' => 'bg-orange-100 text-orange-900 border-orange-300',
                                    'customer_approved' => 'bg-blue-100 text-blue-900 border-blue-300',
                                    'customer_rejected' => 'bg-rose-100 text-rose-900 border-rose-300',
                                    'confirmed' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                                    'cancelled' => 'bg-slate-100 text-slate-800 border-slate-300',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-4 py-3.5 whitespace-nowrap text-sm font-black text-gray-900 border-l-4 {{ $order->status == 'pending' ? 'border-yellow-400' : ($order->status == 'confirmed' ? 'border-emerald-500' : 'border-slate-300') }}">
                                    <div>#{{ $order->id }}</div>
                                    @if($order->internal_reference)
                                        <div class="mt-0.5">
                                            <span class="inline-flex items-center gap-1 text-[10px] bg-slate-100 text-slate-800 border border-slate-200 px-1.5 py-0.5 rounded font-mono font-bold" title="Riferimento Ordine Interno">
                                                🏷️ {{ $order->internal_reference }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap text-xs text-gray-500 font-medium">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-3 py-3.5 text-xs text-gray-700 font-bold max-w-[120px] truncate" title="{{ $order->agent->name }} {{ $order->agent->surname }}">
                                    {{ $order->agent->name }} {{ $order->agent->surname }}
                                </td>
                                <td class="px-3 py-3.5 text-xs text-slate-900 font-black max-w-[180px] xl:max-w-[240px] truncate uppercase" title="{{ $order->customer->business_name }}">
                                    {{ $order->customer->business_name }}
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap text-right text-xs font-black text-gray-900">
                                    € {{ number_format($order->total_amount, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap text-center text-xs">
                                    <span class="px-2.5 py-0.5 rounded text-[10px] uppercase font-black border {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-right text-xs">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <a href="{{ route('admin.b2b.orders.pdf', $order) }}" target="_blank" class="inline-flex items-center gap-1 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black px-2.5 py-1.5 rounded-lg text-xs uppercase shadow-sm transition duration-150" title="Scarica / Stampa PDF Ordine">
                                            <span>🖨️</span>
                                            <span>PDF</span>
                                        </a>
                                        <a href="{{ route('admin.b2b.orders.edit', $order) }}" class="inline-flex items-center bg-black hover:bg-zinc-800 text-white font-black px-3 py-1.5 rounded-lg text-xs uppercase shadow-sm transition duration-150">
                                            Gestisci
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 whitespace-nowrap text-xs font-bold text-gray-400 text-center uppercase tracking-widest">
                                    Nessun ordine B2B ricevuto.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
