<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dettaglio Ordine B2B #{{ $ordine->numero_ordine }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="p-4 bg-gray-50 rounded border flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-lg mb-2 border-b pb-2">Dati Cliente e Spedizione</h3>
                                <p><strong>Cliente:</strong> {{ $ordine->customer->ragione_sociale ?? ($ordine->customer->nome . ' ' . $ordine->customer->cognome) ?? 'Cliente Rimosso' }}</p>
                                <p><strong>Email:</strong> <a href="mailto:{{ $ordine->customer->email ?? '' }}" class="text-indigo-600 hover:underline">{{ $ordine->customer->email ?? '-' }}</a></p>
                            </div>
                            <div class="mt-4 pt-4 border-t">
                                <p class="font-semibold text-gray-700 mb-1">Indirizzo Spedizione (Storicizzato):</p>
                                <p>{{ $ordine->spedizione_nome }}</p>
                                <p>{{ $ordine->spedizione_indirizzo }}</p>
                                <p>{{ $ordine->spedizione_cap }} {{ $ordine->spedizione_citta }} ({{ $ordine->spedizione_provincia }})</p>
                                <p>{{ $ordine->spedizione_nazione }}</p>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 rounded border flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-lg mb-2 border-b pb-2">Riepilogo Importi</h3>
                                <div class="flex justify-between mb-1"><span class="text-gray-600">Imponibile (Articoli):</span> <span>€ {{ number_format($ordine->totale_imponibile, 2, ',', '.') }}</span></div>
                                @if(!empty($ordine->totale_sconto) && $ordine->totale_sconto > 0)
                                    <div class="flex justify-between mb-1 text-emerald-700 font-semibold">
                                        <span>Sconto Coupon ({{ $ordine->coupon_codice ?: 'PROMO' }}):</span> 
                                        <span>- € {{ number_format($ordine->totale_sconto, 2, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between mb-1"><span class="text-gray-600">Spedizione:</span> <span>€ {{ number_format($ordine->costo_spedizione, 2, ',', '.') }}</span></div>
                                @if($ordine->metodo_pagamento === 'contrassegno')
                                    <div class="flex justify-between mb-1"><span class="text-gray-600">Contrassegno:</span> <span>€ {{ number_format($ordine->costo_contrassegno > 0 ? $ordine->costo_contrassegno : 10, 2, ',', '.') }}</span></div>
                                @endif
                                <div class="flex justify-between mb-1"><span class="text-gray-600">IVA/Imposte:</span> <span>€ {{ number_format($ordine->totale_iva, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between mt-2 pt-2 border-t font-bold text-base"><span>Totale Ordine:</span> <span class="text-indigo-600">€ {{ number_format($ordine->totale_ordine, 2, ',', '.') }}</span></div>
                            </div>
                            <div>
                                </div>
                                <div class="mt-4 flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-600">Metodo di Pagamento:</span>
                                    @if($ordine->metodo_pagamento === 'stripe')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            Carta di Credito (Stripe)
                                        </span>
                                    @elseif($ordine->metodo_pagamento === 'bonifico')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Bonifico Bancario
                                        </span>
                                    @elseif($ordine->metodo_pagamento === 'contrassegno')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Contrassegno (Contanti)
                                        </span>
                                    @elseif($ordine->metodo_pagamento === 'paypal')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            PayPal Checkout
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ strtoupper($ordine->metodo_pagamento) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3 class="font-bold text-xl mb-4 text-gray-800">Prodotti Acquistati</h3>
                    <div class="overflow-x-auto mb-8 border rounded shadow-sm">
                        <table class="min-w-full bg-white text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Articolo (Storicizzato)</th>
                                    <th class="py-2 px-4 border-b text-left">SKU Variante</th>
                                    <th class="py-2 px-4 border-b text-center">Taglia / Colore</th>
                                    <th class="py-2 px-4 border-b text-center border-l w-24">Q.tà</th>
                                    <th class="py-2 px-4 border-b text-right w-32">Prezzo Cad. €</th>
                                    <th class="py-2 px-4 border-b text-right border-l w-32">Subtotale €</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ordine->items as $item)
                                    <tr>
                                        <td class="py-2 px-4 border-b font-medium text-gray-800">
                                            {{ $item->nome_prodotto }}
                                            @if($item->variant && $item->variant->product)
                                                <div class="text-xs text-indigo-500 mt-1"><a href="{{ route('admin.shop.prodotti.edit', $item->variant->product->id) }}" target="_blank">Vedi Prodotto in Anagrafica -></a></div>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b text-gray-600">{{ $item->sku ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            @if($item->taglia || $item->colore)
                                                <span class="bg-gray-100 px-2 py-1 rounded text-xs border">{{ $item->taglia ?? '-' }} / {{ $item->colore ?? '-' }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b text-center font-bold text-lg border-l">{{ $item->quantita }}</td>
                                        <td class="py-2 px-4 border-b text-right text-gray-600">{{ number_format($item->prezzo_unitario, 2, ',', '.') }}</td>
                                        <td class="py-2 px-4 border-b text-right font-semibold border-l bg-gray-50">{{ number_format($item->subtotale, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <form action="{{ route('admin.shop.ordini.update', $ordine) }}" method="POST" class="bg-indigo-50 p-6 rounded border border-indigo-100 shadow-sm">
                        @csrf
                        @method('PUT')
                        <h3 class="font-bold text-lg mb-4 text-indigo-900 border-b border-indigo-200 pb-2">Gestione Stato Ordine e Appunti Segreti</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Stato Avanzamento Ordine</label>
                                <select name="stato" class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-white font-semibold">
                                    <option value="nuovo" {{ $ordine->stato == 'nuovo' ? 'selected' : '' }}>Nuovo (Da lavorare)</option>
                                    <option value="in_lavorazione" {{ $ordine->stato == 'in_lavorazione' ? 'selected' : '' }}>In Lavorazione / Magazzino</option>
                                    <option value="spedito" {{ $ordine->stato == 'spedito' ? 'selected' : '' }}>Spedito / Concluso</option>
                                    <option value="annullato" {{ $ordine->stato == 'annullato' ? 'selected' : '' }}>Annullato</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Stato Pagamento</label>
                                <select name="stato_pagamento" class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-white font-semibold">
                                    <option value="attesa" {{ $ordine->stato_pagamento == 'attesa' ? 'selected' : '' }}>In Attesa ({!! strtoupper($ordine->metodo_pagamento) !!})</option>
                                    <option value="pagato" {{ $ordine->stato_pagamento == 'pagato' ? 'selected' : '' }}>Pagato Ricevuto</option>
                                    <option value="rimborsato" {{ $ordine->stato_pagamento == 'rimborsato' ? 'selected' : '' }}>Rimborsato / Errore</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-3 bg-white border rounded">
                                <label class="block text-gray-700 font-bold mb-2">Note Cliente (sola lettura)</label>
                                <div class="text-gray-700 italic">
                                    {{ $ordine->note_cliente ?: 'Nessuna nota lasciata dal cliente in fase di checkout.' }}
                                </div>
                            </div>

                            <div class="p-3 bg-white border rounded">
                                <label class="block text-gray-700 font-bold mb-2">Appunti Privati Staff (non visibili al cliente)</label>
                                <textarea name="note_admin" class="shadow-inner border rounded w-full py-2 px-3 text-gray-700 bg-yellow-50" rows="3" placeholder="Scrivi qui eventuali note su spedizione, fornitore, pagamenti...">{{ old('note_admin', $ordine->note_admin) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-indigo-200 flex justify-between items-center">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded text-lg shadow-md transition">➥ Salva e Aggiorna Stato</button>
                            <a href="{{ route('admin.shop.ordini.index') }}" class="font-bold text-gray-500 hover:text-gray-800">Torna alla lista ordini</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
