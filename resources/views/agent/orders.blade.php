<x-agent-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
            {{ __('I Tuoi Ordini Inviati') }}
        </h2>
    </x-slot>

    <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50">
                <tr class="text-xs font-black text-gray-500 uppercase tracking-widest border-b border-gray-100">
                    <th class="px-8 py-5">ID Ordine</th>
                    <th class="px-8 py-5">Cliente B2B</th>
                    <th class="px-8 py-5 text-center">Data</th>
                    <th class="px-8 py-5 text-center">Stato</th>
                    <th class="px-8 py-5 text-right">Totale</th>
                    <th class="px-8 py-5 text-right">Azioni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                    <tr class="group hover:bg-gray-50 transition">
                        <td class="px-8 py-6 text-sm font-black text-gray-900 uppercase leading-none">
                            #{{ $order->id }}
                            @if($order->is_modified)
                                <span class="inline-block ml-1 text-[9px] bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded-full font-black uppercase tracking-wider" title="Ordine modificato dall'agente">
                                    ✏️ Modificato
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-black text-slate-900 uppercase text-sm">{{ $order->customer->business_name }}</p>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-tighter">{{ $order->customer->vat_number }}</p>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="text-xs font-bold text-gray-500">{{ $order->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
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
                                    'revision_pending' => 'Modificato (Attesa Cliente)',
                                    'customer_approved' => 'Approvato da Cliente',
                                    'customer_rejected' => 'Rifiutato da Cliente',
                                    'confirmed' => 'Confermato',
                                    'cancelled' => 'Annullato',
                                ];
                            @endphp
                            <span class="text-xs px-3 py-1 rounded-full font-black uppercase tracking-widest border {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <span class="font-black text-gray-900">€ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                        </td>
                        <td class="px-8 py-6 text-right space-x-2">
                            <a href="{{ route('agent.orders.pdf', $order) }}" target="_blank" class="inline-block bg-yellow-400 border border-yellow-300 text-slate-950 px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-yellow-300 shadow-md transition duration-300">
                                🖨️ PDF
                            </a>
                            <a href="{{ route('agent.order_detail', $order) }}" class="inline-block bg-black border border-zinc-950 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:border-yellow-400 hover:text-yellow-400 shadow-lg shadow-black/10 transition duration-300">
                                Dettaglio B2B
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-gray-500 uppercase tracking-widest text-xs font-bold">
                            Non hai ancora inviato alcun ordine al sistema.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-agent-layout>
