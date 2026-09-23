<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestione Ordine B2B #') }}{{ $order->id }}
            </h2>
            <div class="flex flex-wrap items-center gap-3">
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
                <div class="mb-6 bg-slate-900 border-2 border-yellow-400 text-white p-4 rounded-xl shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🔒</span>
                        <div>
                            <h4 class="font-black text-yellow-400 text-sm uppercase tracking-wider">Ordine Confermato ed Inviato alla Sede</h4>
                            <p class="text-xs text-slate-300">Questo ordine è stato confermato definitivamente e trasmesso al sistema FTP. Le quantità e i prezzi non possono più essere modificati.</p>
                        </div>
                    </div>
                    <span class="bg-yellow-400 text-slate-950 font-black text-xs px-3 py-1.5 rounded-lg uppercase tracking-wider shadow shrink-0">
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
                                                        <a href="{{ route('agent.product', $item->b2b_product_id) }}" target="_blank" class="hover:text-indigo-600 font-bold">
                                                            {{ $item->product->name }}
                                                        </a>
                                                        @if(!empty($item->delivery_date))
                                                            <span class="block text-[10px] text-amber-700 font-bold uppercase">
                                                                📅 Consegna dal: {{ $item->delivery_date }}
                                                            </span>
                                                        @else
                                                            <span class="block text-[10px] text-emerald-700 font-bold uppercase">
                                                                ✓ Pronta Consegna
                                                            </span>
                                                        @endif
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Note Inserite dal Cliente -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6 text-gray-900">
                                    <h3 class="font-bold text-sm mb-3 uppercase text-slate-800 flex items-center gap-2 border-b pb-2">
                                        <span>📝 Note Inserite dal Cliente</span>
                                    </h3>
                                    @if($order->notes)
                                        <p class="text-sm text-gray-700 bg-amber-50/60 p-4 rounded-xl border border-amber-200/80 italic leading-relaxed whitespace-pre-line">
                                            {{ $order->notes }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-400 bg-gray-50 p-4 rounded-xl border border-gray-200 italic">
                                            Nessuna nota inserita dal cliente al momento dell'ordine.
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Messaggio / Note Sede & Agente -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6 text-gray-900">
                                    <div class="flex items-center justify-between border-b pb-2 mb-3">
                                        <h3 class="font-bold text-sm uppercase text-indigo-900 flex items-center gap-2">
                                            <span>🏢 Messaggio / Note Sede & Agente</span>
                                        </h3>
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Visibile a cliente, agente e PDF</span>
                                    </div>
                                    <textarea name="admin_notes" rows="4" {{ $isConfirmed ? 'disabled' : '' }} placeholder="Inserisci un messaggio o note della sede/agente per questo ordine..." class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $isConfirmed ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white text-gray-900' }}">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                                </div>
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
                                    <option value="revision_pending" {{ $order->status == 'revision_pending' ? 'selected' : '' }}>ATTESA CLIENTE (MODIFICATO)</option>
                                    <option value="customer_approved" {{ $order->status == 'customer_approved' ? 'selected' : '' }}>APPROVATO DA CLIENTE</option>
                                    <option value="customer_rejected" {{ $order->status == 'customer_rejected' ? 'selected' : '' }}>RIFIUTATO DA CLIENTE</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>CONFERMATO</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>ANNULLATO</option>
                                </select>

                                <div>
                                    <label class="text-[10px] text-gray-500 uppercase font-black mb-1 block">Riferimento Ordine Interno</label>
                                    <input type="text" name="internal_reference" value="{{ old('internal_reference', $order->internal_reference) }}" {{ $isConfirmed ? 'disabled' : '' }} placeholder="Es. ORD-2026-001..." class="w-full rounded-md border-gray-300 font-bold mb-3 text-sm {{ $isConfirmed ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white text-gray-900 focus:ring-indigo-500 focus:border-indigo-500' }}">
                                </div>

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

                    <!-- Invia Copia Ordine / Link Pagamento / Risposta -->
                    <div class="bg-amber-50/80 border border-amber-200 overflow-hidden shadow-sm sm:rounded-2xl p-6">
                        <h3 class="font-black text-sm mb-2 uppercase text-slate-900 flex items-center gap-2">
                            <span>✉️</span> Invia Riepilogo / Notifica Ordine
                        </h3>
                        <p class="text-xs text-slate-600 mb-4 font-medium">Invia via email il riepilogo dell'ordine con il riferimento interno, l'agente e le modalità di pagamento.</p>
                        
                        @php 
                            $settings = \App\Models\Setting::all()->pluck('value', 'key');
                            $custEmail = $order->customer?->user?->email ?: $order->customer?->email;
                        @endphp
                        
                        <form action="{{ route('admin.b2b.orders.send_copy', $order) }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Scelta Destinatario -->
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1">Destinatario Email</label>
                                <div class="space-y-1.5 bg-white p-2.5 rounded-xl border border-amber-200">
                                    <label class="flex items-center text-xs font-bold text-slate-800 cursor-pointer">
                                        <input type="radio" name="recipient" value="both" checked class="text-yellow-500 focus:ring-yellow-400 mr-2">
                                        <span>👥 Ad Entrambi (Cliente + Agente)</span>
                                    </label>
                                    @if($custEmail)
                                        <label class="flex items-center text-xs font-bold text-slate-800 cursor-pointer">
                                            <input type="radio" name="recipient" value="customer" class="text-yellow-500 focus:ring-yellow-400 mr-2">
                                            <span>🏢 Solo al Cliente ({{ $custEmail }})</span>
                                        </label>
                                    @endif
                                    @if($order->agent?->email)
                                        <label class="flex items-center text-xs font-bold text-slate-800 cursor-pointer">
                                            <input type="radio" name="recipient" value="agent" class="text-yellow-500 focus:ring-yellow-400 mr-2">
                                            <span>👤 Solo all'Agente ({{ $order->agent->email }})</span>
                                        </label>
                                    @endif
                                </div>
                            </div>

                            <!-- Scelta Modalità Pagamento -->
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1">Modalità di Pagamento da Includere</label>
                                <div class="space-y-1.5 bg-white p-2.5 rounded-xl border border-amber-200">
                                    <label class="flex items-center text-xs font-bold text-slate-800 cursor-pointer">
                                        <input type="radio" name="payment_method" value="none" {{ !$order->payment_method ? 'checked' : '' }} class="text-yellow-500 focus:ring-yellow-400 mr-2">
                                        <span>📄 Solo Riepilogo Ordine</span>
                                    </label>

                                    @if(isset($settings['b2b_payment_stripe_enabled']) && $settings['b2b_payment_stripe_enabled'] == '1')
                                    <label class="flex items-center text-xs font-bold text-slate-800 cursor-pointer">
                                        <input type="radio" name="payment_method" value="stripe" {{ $order->payment_method == 'stripe' ? 'checked' : '' }} class="text-yellow-500 focus:ring-yellow-400 mr-2">
                                        <span>💳 Link Pagamento Carta (Stripe)</span>
                                    </label>
                                    @endif

                                    @if(isset($settings['b2b_payment_paypal_enabled']) && $settings['b2b_payment_paypal_enabled'] == '1')
                                    <label class="flex items-center text-xs font-bold text-slate-800 cursor-pointer">
                                        <input type="radio" name="payment_method" value="paypal" {{ $order->payment_method == 'paypal' ? 'checked' : '' }} class="text-yellow-500 focus:ring-yellow-400 mr-2">
                                        <span>🅿️ Link Pagamento PayPal</span>
                                    </label>
                                    @endif

                                    @if(isset($settings['b2b_payment_bonifico_enabled']) && $settings['b2b_payment_bonifico_enabled'] == '1')
                                    <label class="flex items-center text-xs font-bold text-slate-800 cursor-pointer">
                                        <input type="radio" name="payment_method" value="bonifico" {{ $order->payment_method == 'bonifico' ? 'checked' : '' }} class="text-yellow-500 focus:ring-yellow-400 mr-2">
                                        <span>🏦 Coordinate Bancarie (Bonifico)</span>
                                    </label>
                                    @endif
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-slate-900 hover:bg-yellow-400 hover:text-slate-950 text-white font-black py-3 rounded-xl transition duration-200 shadow text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Invia Email Riepilogo Ordine
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
