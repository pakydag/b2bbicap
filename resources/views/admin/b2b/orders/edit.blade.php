<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestione Ordine B2B #') }}{{ $order->id }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.b2b.orders.pdf', $order) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black rounded-lg text-xs uppercase tracking-wider shadow transition duration-150">
                    <span>🖨️ STAMPA / PDF</span>
                </a>
                <a href="{{ route('admin.b2b.orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium ml-2">← Torna all'elenco</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php $isConfirmed = ($order->status === 'confirmed'); @endphp

            @if($isConfirmed)
                <div class="mb-6 bg-slate-900 border-2 border-yellow-400 text-white p-4 rounded-xl shadow-lg flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🔒</span>
                        <div>
                            <h4 class="font-black text-yellow-400 text-sm uppercase tracking-wider">Ordine Confermato ed Inviato alla Sede</h4>
                            <p class="text-xs text-slate-300">Questo ordine è stato confermato definitivamente e trasmesso al sistema FTP. Le quantità e i prezzi non possono più essere modificati.</p>
                        </div>
                    </div>
                    <span class="bg-yellow-400 text-slate-950 font-black text-xs px-3 py-1.5 rounded-lg uppercase tracking-wider shadow">
                        BLOCCATO
                    </span>
                </div>
            @endif

            <form action="{{ route('admin.b2b.orders.update', $order) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Articoli Ordine -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="font-bold text-lg mb-4 border-b pb-2 uppercase text-indigo-900">Articoli in Ordine</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Taglia / Colore</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Quantità</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">P. Unitario (€)</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotale</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($order->items as $index => $item)
                                                <tr>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                        {{ $item->product->name }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                                        {{ $item->variant->size }} / {{ $item->variant->color ?? 'Unico' }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="0" {{ $isConfirmed ? 'disabled' : '' }} class="w-20 text-sm border-gray-300 rounded-md shadow-sm {{ $isConfirmed ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-right">
                                                        <input type="number" step="0.01" name="items[{{ $index }}][price]" value="{{ $item->price }}" {{ $isConfirmed ? 'disabled' : '' }} class="w-28 text-sm border-gray-300 rounded-md shadow-sm text-right {{ $isConfirmed ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-bold text-indigo-900">
                                                        € {{ number_format($item->quantity * $item->price, 2, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50 font-bold">
                                            <tr>
                                                <td colspan="4" class="px-4 py-4 text-right text-sm uppercase">Totale Ordine</td>
                                                <td class="px-4 py-4 text-right text-lg text-indigo-900">
                                                    € {{ number_format($order->total_amount, 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="font-bold text-lg mb-4 border-b pb-2 uppercase text-indigo-900">Note Ordine</h3>
                                <p class="text-sm text-gray-600 bg-gray-50 p-4 rounded-md border italic">
                                    {{ $order->notes ?: 'Nessuna nota inserita dall\'agente.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Stato e Cliente -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-600">
                            <div class="p-6">
                                <h3 class="font-bold text-md mb-4 border-b pb-2 uppercase">Dati Cliente & Agente</h3>
                                <div class="space-y-4 text-sm">
                                    <div>
                                        <p class="text-gray-500 font-bold uppercase text-[10px]">Cliente B2B</p>
                                        <p class="font-black text-gray-900">{{ $order->customer->business_name }}</p>
                                        <p class="text-gray-400 text-xs">{{ $order->customer->vat_number }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 font-bold uppercase text-[10px]">Agente di Riferimento</p>
                                        <p class="font-black text-gray-900">{{ $order->agent->name }} {{ $order->agent->surname }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 font-bold uppercase text-[10px]">Data Invio</p>
                                        <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="font-bold text-md mb-4 border-b border-gray-200 pb-2 uppercase text-gray-900">Stato Ordine</h3>
                            <div class="space-y-4">
                                <select name="status" {{ $isConfirmed ? 'disabled' : '' }} class="w-full rounded-md border-gray-300 font-bold mb-2 {{ $isConfirmed ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white text-gray-900 focus:ring-indigo-500 focus:border-indigo-500' }}">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>IN ATTESA</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>CONFERMATO</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>ANNULLATO</option>
                                </select>

                                <div class="mb-2">
                                    <label class="text-[10px] text-gray-400 uppercase font-black mb-1 block">Metodo Pagamento Ordine</label>
                                    <select name="payment_method" {{ $isConfirmed ? 'disabled' : '' }} class="w-full rounded-md border-gray-300 font-bold {{ $isConfirmed ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white text-gray-900 focus:ring-indigo-500 focus:border-indigo-500' }}">
                                        <option value="" {{ !$order->payment_method ? 'selected' : '' }}>NESSUNO / DA DEFINIRE</option>
                                        <option value="stripe" {{ $order->payment_method == 'stripe' ? 'selected' : '' }}>STRIPE (CARTA)</option>
                                        <option value="paypal" {{ $order->payment_method == 'paypal' ? 'selected' : '' }}>PAYPAL</option>
                                        <option value="bonifico" {{ $order->payment_method == 'bonifico' ? 'selected' : '' }}>BONIFICO</option>
                                    </select>
                                </div>
                                
                                @if(!$isConfirmed)
                                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg transition duration-300 shadow text-xs uppercase tracking-widest">
                                        Salva Modifiche
                                    </button>
                                    <p class="text-[10px] text-gray-400 text-center uppercase tracking-tighter">Attenzione: l'aggiornamento ricalcolerà il totale in base a quantità e prezzi inseriti.</p>
                                @else
                                    <div class="bg-slate-100 border border-slate-200 text-slate-600 font-black py-3 px-4 rounded-lg text-center text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                                        🔒 Modifiche Disabilitate (Ordine Confermato)
                                    </div>
                                @endif

                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <a href="{{ route('admin.b2b.orders.pdf', $order) }}" target="_blank" class="w-full bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black py-3.5 px-4 rounded-xl shadow-md transition duration-200 text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                                        <span>🖨️ STAMPA / SALVA PDF ORDINE</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- NEW: Invia Copia Ordine / Link Pagamento -->
                    <div class="bg-indigo-50 border border-indigo-100 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-bold text-md mb-4 border-b border-indigo-200 pb-2 uppercase text-indigo-900">Invia all'Agente</h3>
                        <p class="text-xs text-indigo-700 mb-4 font-medium italic">Invia una copia del riepilogo ordine all'agente ({{ $order->agent->email }}), con o senza link di pagamento.</p>
                        
                        @php $settings = \App\Models\Setting::all()->pluck('value', 'key'); @endphp
                        
                        <form action="{{ route('admin.b2b.orders.send_copy', $order) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="space-y-2">
                                <label class="flex items-center p-2 rounded-md hover:bg-white transition cursor-pointer border border-transparent hover:border-indigo-200">
                                    <input type="radio" name="payment_method" value="none" {{ !$order->payment_method ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500 mr-3">
                                    <span class="text-sm font-bold text-indigo-900">📄 Solo Riepilogo (Nessun link)</span>
                                </label>

                                @if(isset($settings['b2b_payment_stripe_enabled']) && $settings['b2b_payment_stripe_enabled'] == '1')
                                <label class="flex items-center p-2 rounded-md hover:bg-white transition cursor-pointer border border-transparent hover:border-indigo-200">
                                    <input type="radio" name="payment_method" value="stripe" {{ $order->payment_method == 'stripe' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500 mr-3">
                                    <span class="text-sm font-bold text-indigo-900">💳 Link Carta (Stripe)</span>
                                </label>
                                @endif

                                @if(isset($settings['b2b_payment_paypal_enabled']) && $settings['b2b_payment_paypal_enabled'] == '1')
                                <label class="flex items-center p-2 rounded-md hover:bg-white transition cursor-pointer border border-transparent hover:border-indigo-200">
                                    <input type="radio" name="payment_method" value="paypal" {{ $order->payment_method == 'paypal' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500 mr-3">
                                    <span class="text-sm font-bold text-indigo-900">🅿️ Link PayPal</span>
                                </label>
                                @endif

                                @if(isset($settings['b2b_payment_bonifico_enabled']) && $settings['b2b_payment_bonifico_enabled'] == '1')
                                <label class="flex items-center p-2 rounded-md hover:bg-white transition cursor-pointer border border-transparent hover:border-indigo-200">
                                    <input type="radio" name="payment_method" value="bonifico" {{ $order->payment_method == 'bonifico' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500 mr-3">
                                    <span class="text-sm font-bold text-indigo-900">🏦 Coordinate Bonifico</span>
                                </label>
                                @endif
                            </div>

                            <button type="submit" class="w-full bg-white border border-indigo-200 hover:bg-indigo-600 hover:text-white text-indigo-700 font-bold py-3 rounded-lg transition duration-300 shadow-sm text-[10px] uppercase tracking-wider flex items-center justify-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Invia Email all'Agente
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
