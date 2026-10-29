<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shop B2B - Ordini') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">N° Ordine</th>
                                    <th class="py-2 px-4 border-b text-left">Data</th>
                                    <th class="py-2 px-4 border-b text-left">Cliente B2B</th>
                                    <th class="py-2 px-4 border-b text-left">Totale</th>
                                    <th class="py-2 px-4 border-b text-left">Metodo Pagamento</th>
                                    <th class="py-2 px-4 border-b text-left">Stato Pagamento</th>
                                    <th class="py-2 px-4 border-b text-left">Stato Spedizione</th>
                                    <th class="py-2 px-4 border-b text-right">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ordini as $o)
                                    <tr>
                                        <td class="py-2 px-4 border-b font-bold">{{ $o->numero_ordine }}</td>
                                        <td class="py-2 px-4 border-b">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if($o->customer)
                                                {{ $o->customer->ragione_sociale ?? ($o->customer->nome . ' ' . $o->customer->cognome) }}
                                            @else
                                                <span class="text-red-500 italic">Cliente Eliminato</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b font-bold text-indigo-700">€ {{ number_format($o->totale_ordine, 2, ',', '.') }}</td>
                                        <td class="py-2 px-4 border-b text-gray-700 capitalize">
                                            @if($o->metodo_pagamento === 'stripe')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    Carta (Stripe)
                                                </span>
                                            @elseif($o->metodo_pagamento === 'bonifico')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Bonifico
                                                </span>
                                            @elseif($o->metodo_pagamento === 'contrassegno')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    Contrassegno
                                                </span>
                                            @elseif($o->metodo_pagamento === 'paypal')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    PayPal
                                                </span>
                                            @else
                                                {{ $o->metodo_pagamento }}
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            @if($o->stato_pagamento === 'attesa')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">In Attesa</span>
                                            @elseif($o->stato_pagamento === 'pagato')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Pagato</span>
                                            @elseif($o->stato_pagamento === 'rimborsato')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Rimborsato</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($o->stato_pagamento) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            @if($o->stato === 'nuovo')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Nuovo</span>
                                            @elseif($o->stato === 'in_lavorazione')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">In Lavorazione</span>
                                            @elseif($o->stato === 'spedito')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Spedito</span>
                                                @if($o->tracking_number)
                                                    <div class="text-[10px] text-gray-600 font-mono mt-0.5 font-bold">
                                                        {{ $o->corriere ? $o->corriere . ': ' : '' }}{{ $o->tracking_number }}
                                                    </div>
                                                @endif
                                            @elseif($o->stato === 'annullato')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Annullato</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($o->stato) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b text-right">
                                            <a href="{{ route('admin.shop.ordini.edit', $o) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 font-semibold">Gestisci Ordine</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="py-4 px-4 text-center text-gray-500">Nessun ordine presente.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
