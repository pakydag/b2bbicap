<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordine B2B #{{ $order->id }} - BICAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-container { shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important; width: 100% !important; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans text-gray-800 antialiased p-4 sm:p-8">

    <!-- Action Bar (hidden when printing) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex justify-between items-center bg-slate-900 text-white p-4 rounded-2xl shadow-xl">
        <div class="flex items-center gap-3">
            <span class="text-xl">📄</span>
            <div>
                <h1 class="font-black text-sm uppercase tracking-wider text-yellow-400">Anteprima Stampa Ordine #{{ $order->id }}</h1>
                <p class="text-xs text-gray-300">Puoi stampare il documento o salvarlo direttamente in PDF dal browser.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black uppercase text-xs px-6 py-2.5 rounded-xl shadow-lg transition duration-200 cursor-pointer flex items-center gap-2">
                <span>🖨️ STAMPA / SALVA IN PDF</span>
            </button>
            <button onclick="window.history.back()" class="bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition">
                Chiudi
            </button>
        </div>
    </div>

    <!-- Printable Paper Container -->
    <div class="print-container max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-lg border border-gray-200 text-slate-900">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-gray-200 pb-6 mb-8">
            <div>
                <div class="flex items-center gap-3">
                    <img src="{{ asset('storage/logo/logo-bicap.png') }}" alt="BICAP" class="h-10 w-auto object-contain" onerror="this.onerror=null; this.src='https://bicap.it/assets/images/logo.png';">
                    <span class="font-black tracking-widest text-xs uppercase text-zinc-400 border-l border-gray-300 pl-3">B2B PORTAL</span>
                </div>
                <p class="text-xs text-gray-500 font-medium mt-3">Calzaturificio 5Bi S.r.l. - Calzature di Sicurezza</p>
                <p class="text-[10px] text-gray-400">Zona Industriale Via Trani - Barletta (BT) Italia</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-yellow-400 text-slate-950 font-black text-xs uppercase tracking-widest rounded-lg mb-2">
                    CONFERMA D'ORDINE B2B
                </span>
                <h2 class="text-3xl font-black text-slate-900">#{{ $order->id }}</h2>
                <p class="text-xs text-gray-500 font-bold mt-1">Data: <span class="text-slate-900">{{ $order->created_at->format('d/m/Y H:i') }}</span></p>
                <p class="text-xs text-gray-500 font-bold mt-0.5">Stato: <span class="uppercase text-indigo-600 font-black">{{ $order->status_label }}</span></p>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <!-- Dati Cliente -->
            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">INTESTATO A (CLIENTE B2B)</p>
                <h3 class="text-base font-black text-slate-900 uppercase">{{ $order->customer->business_name }}</h3>
                <div class="mt-2 space-y-1 text-xs text-gray-700">
                    @if($order->customer->vat_number)
                        <p><span class="font-bold text-gray-400 uppercase">P.IVA / C.F.:</span> {{ $order->customer->vat_number }}</p>
                    @endif
                    @if($order->customer->contact_name || $order->customer->contact_surname)
                        <p><span class="font-bold text-gray-400 uppercase">REFERENTE:</span> {{ $order->customer->contact_name }} {{ $order->customer->contact_surname }}</p>
                    @endif
                    @if($order->customer->phone)
                        <p><span class="font-bold text-gray-400 uppercase">TEL:</span> {{ $order->customer->phone }}</p>
                    @endif
                    @if($order->customer->email)
                        <p><span class="font-bold text-gray-400 uppercase">EMAIL:</span> {{ $order->customer->email }}</p>
                    @endif
                </div>
            </div>

            <!-- Dati Agente e Pagamento -->
            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">AGENTE DI RIFERIMENTO & METODO</p>
                <h3 class="text-base font-black text-slate-900 uppercase">{{ $order->agent->name }} {{ $order->agent->surname }}</h3>
                <div class="mt-2 space-y-1 text-xs text-gray-700">
                    <p><span class="font-bold text-gray-400 uppercase">EMAIL AGENTE:</span> {{ $order->agent->email }}</p>
                    <p class="pt-2"><span class="font-bold text-gray-400 uppercase">CONDIZIONI PAGAMENTO:</span></p>
                    <p class="font-bold text-indigo-700 uppercase">{{ $order->payment_method ?: ($order->customer->paymentCondition->name ?? 'Da concordare / Standard') }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-900 text-white text-[11px] font-black uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Codice</th>
                        <th class="py-3 px-4">Articolo / Descrizione</th>
                        <th class="py-3 px-4 text-center">Taglia</th>
                        <th class="py-3 px-4 text-center">Q.tà</th>
                        <th class="py-3 px-4 text-right">Prezzo Unit.</th>
                        <th class="py-3 px-4 text-right">Subtotale</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-xs">
                    @forelse($order->items as $index => $item)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                            <td class="py-3.5 px-4 font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">{{ $item->product->code ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-black text-slate-900 uppercase">
                                {{ $item->product->name ?? 'Prodotto N.D.' }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                {{ $item->variant ? $item->variant->size : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-black text-slate-900 bg-gray-100/50">
                                {{ $item->quantity }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium text-gray-700">
                                € {{ number_format($item->price, 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                € {{ number_format($item->quantity * $item->price, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500 font-bold">Nessun articolo in questo ordine.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals Card -->
        <div class="flex justify-between items-start gap-6 border-t border-gray-200 pt-6">
            <div class="max-w-xs text-xs text-gray-500">
                @if($order->notes)
                    <p class="font-bold uppercase text-gray-400 mb-1">Note Ordine:</p>
                    <p class="italic bg-gray-50 p-3 rounded-xl border border-gray-200 text-gray-700">{{ $order->notes }}</p>
                @endif
            </div>

            <div class="w-72 space-y-2 bg-gray-50 p-5 rounded-2xl border border-gray-200 text-right">
                <div class="flex justify-between text-xs text-gray-600">
                    <span>Totale Capi / Paia:</span>
                    <span class="font-bold text-slate-900">{{ $order->items->sum('quantity') }} pz</span>
                </div>
                <div class="flex justify-between text-xs text-gray-600 border-b border-gray-200 pb-2">
                    <span>Imponibile Merce:</span>
                    <span class="font-bold text-slate-900">€ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-base font-black text-slate-900 pt-1">
                    <span class="uppercase">TOTALE ORDINE:</span>
                    <span class="text-indigo-600">€ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 pt-6 border-t border-gray-200 flex justify-between items-center text-[10px] text-gray-400">
            <p>Documento generato automaticamente dal Portale Agenti B2B BICAP - {{ date('d/m/Y H:i') }}</p>
            <p class="font-bold uppercase">www.bicap.it</p>
        </div>
    </div>

</body>
</html>
