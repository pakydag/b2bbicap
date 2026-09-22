<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dettaglio Ordine B2B #') }}{{ $order->id }}
            </h2>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.b2b.orders.pdf', $order) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black rounded-lg text-xs uppercase tracking-wider shadow transition duration-150">
                    <span>🖨️ STAMPA / SALVA PDF</span>
                </a>
                @if($order->status !== 'confirmed' && $order->status !== 'cancelled')
                    <form action="{{ route('admin.b2b.orders.update', $order) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" onclick="return confirm('Confermare definitivamente questo ordine per l\'invio su FTP?');" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-xs font-bold uppercase transition cursor-pointer">Conferma Ordine</button>
                    </form>
                    <form action="{{ route('admin.b2b.orders.update', $order) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" onclick="return confirm('Sei sicuro di voler annullare questo ordine? Verranno notificate entrambe le parti.');" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-xs font-bold uppercase transition cursor-pointer">Annulla Ordine</button>
                    </form>
                @endif
                <a href="{{ route('admin.b2b.orders.edit', $order) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-xs font-bold uppercase transition">Gestisci / Modifica</a>
                <a href="{{ route('admin.b2b.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-xs font-bold uppercase transition">Indietro</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Info Cliente -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="font-bold text-gray-700 uppercase text-xs mb-4 border-b pb-2">Dati Cliente</h3>
                    <p class="text-lg font-bold">{{ $order->customer->business_name }}</p>
                    <p class="text-sm text-gray-600">P.IVA: {{ $order->customer->vat_number }}</p>
                    <p class="text-sm text-gray-600">Ref: {{ $order->customer->contact_name }} {{ $order->customer->contact_surname }}</p>
                    <p class="text-sm text-gray-600">{{ $order->customer->email }}</p>
                </div>
                <!-- Info Agente -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="font-bold text-gray-700 uppercase text-xs mb-4 border-b pb-2">Agente Incaricato</h3>
                    <p class="text-lg font-bold">{{ $order->agent->name }} {{ $order->agent->surname }}</p>
                    <p class="text-sm text-gray-600">Email: {{ $order->agent->email }}</p>
                </div>
                <!-- Riepilogo -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="font-bold text-gray-700 uppercase text-xs mb-4 border-b pb-2">Stato & Pagamento</h3>
                    @if($order->internal_reference)
                        <div class="flex justify-between items-center mb-2">
                            <span>Rif. Interno:</span>
                            <span class="font-mono font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded">{{ $order->internal_reference }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center mb-2">
                        <span>Stato:</span>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                'revision_pending' => 'bg-orange-100 text-orange-800 border-orange-300',
                                'customer_approved' => 'bg-blue-100 text-blue-800 border-blue-300',
                                'customer_rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
                                'confirmed' => 'bg-green-100 text-green-800 border-green-300',
                                'cancelled' => 'bg-red-100 text-red-800 border-red-300',
                            ];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded text-xs uppercase font-bold border {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Pagamento:</span>
                        <span class="font-bold">{{ $order->payment_method ?? 'Trattativa privata' }}</span>
                    </div>
                </div>
            </div>

            <!-- Dettaglio Righe -->
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto min-w-0">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Prodotto</th>
                                <th class="px-6 py-3 text-center">Variante (Colore/Taglia)</th>
                                <th class="px-6 py-3 text-center">Quantità</th>
                                <th class="px-6 py-3 text-right">Prezzo Unit.</th>
                                <th class="px-6 py-3 text-right">Totale Riga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $item->product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->product->brand->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-600">
                                        {{ $item->variant->color }} / {{ $item->variant->size }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-bold">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right text-sm">€ {{ number_format($item->price, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-bold">€ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-bold uppercase text-xs">Totale Finale Ordine</td>
                                <td class="px-6 py-4 text-right text-xl font-black text-indigo-700">€ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($order->notes)
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="font-bold text-gray-700 uppercase text-xs mb-2">Note Agente</h3>
                    <p class="text-sm italic text-gray-600 bg-gray-50 p-3 rounded">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
