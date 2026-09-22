<x-agent-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
            {{ __('I Tuoi Ordini Inviati') }}
        </h2>
    </x-slot>

    <div class="bg-white rounded-3xl sm:rounded-[40px] shadow-sm border border-gray-100 overflow-hidden min-w-0">
        
        <!-- Vista Mobile / Schermi Piccoli (< md) -->
        <div class="md:hidden divide-y divide-gray-100">
            @forelse($orders as $order)
                @php
                    $statusClasses = [
                        'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
                        'revision_pending' => 'bg-orange-100 text-orange-800 border-orange-300',
                        'customer_approved' => 'bg-blue-100 text-blue-800 border-blue-300',
                        'customer_rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
                        'confirmed' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                        'cancelled' => 'bg-slate-100 text-slate-700 border-slate-300',
                    ];
                    $statusLabels = [
                        'pending' => 'In Attesa',
                        'revision_pending' => 'In Attesa Cliente',
                        'customer_approved' => 'Approvato',
                        'customer_rejected' => 'Rifiutato',
                        'confirmed' => 'Confermato',
                        'cancelled' => 'Annullato',
                    ];
                @endphp
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-base font-black text-gray-900">#{{ $order->id }}</span>
                                @if($order->is_modified)
                                    <span class="text-[9px] bg-amber-100 text-amber-900 border border-amber-300 px-1.5 py-0.5 rounded-full font-black uppercase">
                                        ✏️ Modificato
                                    </span>
                                @endif
                                <span class="text-xs text-gray-400 font-bold">• {{ $order->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($order->internal_reference)
                                <div class="mt-0.5">
                                    <span class="inline-flex items-center gap-1 text-[10px] bg-slate-100 text-slate-800 border border-slate-200 px-1.5 py-0.5 rounded font-mono font-black">
                                        🏷️ Rif: {{ $order->internal_reference }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <span class="text-[10px] px-2.5 py-0.5 rounded-full font-black uppercase tracking-wider border {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </div>

                    <div class="flex justify-between items-baseline">
                        <div class="min-w-0 pr-2">
                            <p class="font-black text-slate-900 text-sm uppercase truncate">{{ $order->customer->business_name }}</p>
                            <p class="text-[11px] text-gray-400 font-bold uppercase tracking-tight">{{ $order->customer->vat_number }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-base font-black text-gray-900">€ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <a href="{{ route('agent.orders.pdf', $order) }}" target="_blank" class="flex-1 bg-yellow-400 border border-yellow-300 text-slate-950 py-2 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-yellow-300 shadow-sm transition duration-200 text-center">
                            🖨️ PDF
                        </a>
                        <a href="{{ route('agent.order_detail', $order) }}" class="flex-1 bg-black border border-zinc-950 text-white py-2 rounded-xl text-xs font-black uppercase tracking-wider hover:border-yellow-400 hover:text-yellow-400 shadow-md transition duration-200 text-center">
                            Dettaglio B2B
                        </a>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500 uppercase tracking-widest text-xs font-bold">
                    Non hai ancora inviato alcun ordine al sistema.
                </div>
            @endforelse
        </div>

        <!-- Vista Desktop / Tablet (>= md) -->
        <div class="hidden md:block overflow-x-auto w-full min-w-0">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr class="text-[11px] lg:text-xs font-black text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-3 lg:px-5 py-3.5 whitespace-nowrap">ID Ordine / Rif. Interno</th>
                        <th class="px-3 lg:px-5 py-3.5">Cliente B2B</th>
                        <th class="px-2 lg:px-4 py-3.5 text-center whitespace-nowrap">Data</th>
                        <th class="px-2 lg:px-4 py-3.5 text-center whitespace-nowrap">Stato</th>
                        <th class="px-3 lg:px-4 py-3.5 text-right whitespace-nowrap">Totale</th>
                        <th class="px-3 lg:px-5 py-3.5 text-right whitespace-nowrap">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        @php
                            $statusClasses = [
                                'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
                                'revision_pending' => 'bg-orange-100 text-orange-800 border-orange-300',
                                'customer_approved' => 'bg-blue-100 text-blue-800 border-blue-300',
                                'customer_rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
                                'confirmed' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                'cancelled' => 'bg-slate-100 text-slate-700 border-slate-300',
                            ];
                            $statusLabels = [
                                'pending' => 'In Attesa',
                                'revision_pending' => 'In Attesa Cliente',
                                'customer_approved' => 'Approvato',
                                'customer_rejected' => 'Rifiutato',
                                'confirmed' => 'Confermato',
                                'cancelled' => 'Annullato',
                            ];
                        @endphp
                        <tr class="group hover:bg-gray-50/60 transition">
                            <td class="px-3 lg:px-5 py-3.5 text-sm font-black text-gray-900 uppercase leading-none whitespace-nowrap">
                                <div>#{{ $order->id }}</div>
                                @if($order->internal_reference)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 text-[10px] bg-slate-100 text-slate-800 border border-slate-200 px-1.5 py-0.5 rounded font-mono font-bold" title="Riferimento Ordine Interno">
                                            🏷️ {{ $order->internal_reference }}
                                        </span>
                                    </div>
                                @endif
                                @if($order->is_modified)
                                    <span class="inline-block mt-1 text-[9px] bg-amber-100 text-amber-900 border border-amber-300 px-1.5 py-0.5 rounded-full font-black uppercase tracking-wider" title="Ordine modificato dall'agente">
                                        ✏️ Mod
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 lg:px-5 py-3.5 min-w-0 max-w-[140px] md:max-w-[180px] lg:max-w-none">
                                <p class="font-black text-slate-900 uppercase text-xs lg:text-sm leading-tight truncate" title="{{ $order->customer->business_name }}">{{ $order->customer->business_name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $order->customer->vat_number }}</p>
                            </td>
                            <td class="px-2 lg:px-4 py-3.5 text-center whitespace-nowrap">
                                <span class="text-xs font-bold text-gray-500">{{ $order->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-2 lg:px-4 py-3.5 text-center whitespace-nowrap">
                                <span class="text-[10px] lg:text-xs px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider border {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-3 lg:px-4 py-3.5 text-right whitespace-nowrap">
                                <span class="font-black text-gray-900 text-xs lg:text-sm">€ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                            </td>
                            <td class="px-3 lg:px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <a href="{{ route('agent.orders.pdf', $order) }}" target="_blank" class="inline-flex items-center gap-1 bg-yellow-400 border border-yellow-300 text-slate-950 px-2.5 py-1.5 rounded-xl text-xs font-black uppercase hover:bg-yellow-300 shadow-sm transition duration-200" title="Scarica PDF Ordine">
                                        <span>🖨️</span>
                                        <span class="hidden xl:inline">PDF</span>
                                    </a>
                                    <a href="{{ route('agent.order_detail', $order) }}" class="inline-flex items-center bg-black border border-zinc-950 text-white px-2.5 lg:px-3 py-1.5 rounded-xl text-xs font-black uppercase hover:border-yellow-400 hover:text-yellow-400 shadow-sm transition duration-200">
                                        Dettaglio
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500 uppercase tracking-widest text-xs font-bold">
                                Non hai ancora inviato alcun ordine al sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-agent-layout>
