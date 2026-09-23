@php
    $siteLogo = \App\Models\Setting::where('key', 'site_logo')->value('value');
    $logoFile = storage_path('app/public/logo-bicap.png');
    if (!file_exists($logoFile) && $siteLogo) {
        $logoFile = public_path(ltrim($siteLogo, '/'));
    }
    if (!file_exists($logoFile)) {
        $logoFile = public_path('storage/logo-bicap.png');
    }
    $logoDataUri = file_exists($logoFile) 
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoFile)) 
        : ($siteLogo ? asset($siteLogo) : asset('storage/logo-bicap.png'));
@endphp
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $order->status === 'confirmed' ? "Conferma d'Ordine" : "Proposta d'Ordine (In Attesa)" }} #{{ $order->id }} - BICAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; font-size: 11px !important; }
            .print-container { box-shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important; width: 100% !important; padding: 0 !important; }
            .page-break-inside-avoid { break-inside: avoid !important; page-break-inside: avoid !important; }
            @page { margin: 1.2cm; size: auto; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans text-gray-800 antialiased p-4 sm:p-8">

    <!-- Action Bar (hidden when printing) -->
    <div class="no-print max-w-5xl mx-auto mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-900 text-white p-4 rounded-2xl shadow-xl">
        <div class="flex items-center gap-3">
            <span class="text-xl">📄</span>
            <div>
                <h1 class="font-black text-sm uppercase tracking-wider text-yellow-400">
                    Anteprima Stampa Ordine #{{ $order->id }}
                    @if($order->status !== 'confirmed')
                        <span class="text-amber-300 text-xs font-bold lowercase">({{ $order->status_label }} - Non Confermato)</span>
                    @endif
                </h1>
                <p class="text-xs text-gray-300">Puoi stampare il documento o salvarlo direttamente in PDF dal browser con le condizioni di listino applicate.</p>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
            <button onclick="window.print()" class="bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black uppercase text-xs px-6 py-2.5 rounded-xl shadow-lg transition duration-200 cursor-pointer flex items-center gap-2">
                <span>🖨️ STAMPA / SALVA IN PDF</span>
            </button>
            <button onclick="window.history.back()" class="bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition cursor-pointer">
                Chiudi
            </button>
        </div>
    </div>

    <!-- Printable Paper Container -->
    <div class="print-container max-w-5xl mx-auto bg-white p-5 sm:p-8 md:p-12 rounded-2xl sm:rounded-3xl shadow-lg border border-gray-200 text-slate-900 min-w-0">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 border-b border-gray-200 pb-6 mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="bg-black px-3.5 py-2 rounded-xl inline-flex items-center shadow-xs">
                        <img src="{{ $logoDataUri }}" alt="BICAP" class="h-7 w-auto object-contain">
                    </div>
                    <span class="font-black tracking-widest text-xs uppercase text-zinc-400 border-l border-gray-300 pl-3">B2B PORTAL</span>
                </div>
                <p class="text-xs text-gray-500 font-medium mt-3">Calzaturificio 5Bi S.r.l. - Calzature di Sicurezza</p>
                <p class="text-[10px] text-gray-400">Zona Industriale Via Trani - Barletta (BT) Italia - www.bicap.it</p>
            </div>
            <div class="text-left sm:text-right">
                @if($order->status === 'confirmed')
                    <span class="inline-block px-3.5 py-1.5 bg-emerald-600 text-white font-black text-xs uppercase tracking-wider rounded-lg mb-2 shadow-xs">
                        ✓ CONFERMA D'ORDINE B2B
                    </span>
                @elseif($order->status === 'revision_pending')
                    <span class="inline-block px-3.5 py-1.5 bg-amber-500 text-white font-black text-xs uppercase tracking-wider rounded-lg mb-2 shadow-xs">
                        ✏️ RETTIFICA ORDINE — IN ATTESA CLIENTE
                    </span>
                @elseif($order->status === 'customer_approved')
                    <span class="inline-block px-3.5 py-1.5 bg-blue-600 text-white font-black text-xs uppercase tracking-wider rounded-lg mb-2 shadow-xs">
                        ✓ APPROVATO DA CLIENTE — IN ATTESA OK FINALE
                    </span>
                @elseif($order->status === 'customer_rejected')
                    <span class="inline-block px-3.5 py-1.5 bg-rose-600 text-white font-black text-xs uppercase tracking-wider rounded-lg mb-2 shadow-xs">
                        ✕ MODIFICHE RIFIUTATE DAL CLIENTE
                    </span>
                @elseif($order->status === 'cancelled')
                    <span class="inline-block px-3.5 py-1.5 bg-rose-600 text-white font-black text-xs uppercase tracking-wider rounded-lg mb-2 shadow-xs">
                        ✕ ORDINE ANNULLATO
                    </span>
                @else
                    <span class="inline-block px-3.5 py-1.5 bg-yellow-400 text-slate-950 font-black text-xs uppercase tracking-wider rounded-lg mb-2 shadow-xs border border-yellow-300">
                        ⏳ PROPOSTA D'ORDINE — IN ATTESA CONFERMA AGENTE
                    </span>
                @endif
                <h2 class="text-3xl font-black text-slate-900">#{{ $order->id }}</h2>
                @if($order->internal_reference)
                    <p class="text-xs text-indigo-700 font-black mt-1">Rif. Ordine Interno: <span class="bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-md font-mono text-slate-900">{{ $order->internal_reference }}</span></p>
                @endif
                <p class="text-xs text-gray-500 font-bold mt-1">Data: <span class="text-slate-900">{{ $order->created_at->format('d/m/Y H:i') }}</span></p>
                <p class="text-xs text-gray-500 font-bold mt-0.5">
                    Stato: 
                    @if($order->status === 'confirmed')
                        <span class="uppercase text-emerald-700 font-black">✓ CONFERMATO</span>
                    @elseif($order->status === 'customer_approved')
                        <span class="uppercase text-blue-700 font-black">✓ APPROVATO DA CLIENTE (ATTESA CONFERMA FINALE)</span>
                    @elseif($order->status === 'revision_pending')
                        <span class="uppercase text-amber-700 font-black">⏳ IN ATTESA DI APPROVAZIONE CLIENTE</span>
                    @elseif($order->status === 'customer_rejected' || $order->status === 'cancelled')
                        <span class="uppercase text-rose-700 font-black">✕ {{ strtoupper($order->status_label) }}</span>
                    @else
                        <span class="uppercase text-amber-600 font-black">⏳ IN ATTESA DI CONFERMA DALL'AGENTE</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Box Avviso Stato Ordine -->
        @if($order->status === 'confirmed')
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-300 rounded-2xl flex items-center gap-3 print:bg-emerald-50 print:border-emerald-300 page-break-inside-avoid">
                <span class="text-2xl shrink-0">✅</span>
                <div class="text-xs text-emerald-900 leading-relaxed">
                    <p class="font-black uppercase tracking-wider text-emerald-950 text-xs">
                        ✓ Conferma d'Ordine Definitiva
                    </p>
                    <p class="mt-0.5">
                        Questo ordine è stato <strong>validato e confermato definitivamente dall'agente commerciale / amministrazione</strong> ed è attualmente in lavorazione per la preparazione ed evasione.
                    </p>
                </div>
            </div>
        @elseif($order->status === 'revision_pending')
            <div class="mb-8 p-4 bg-amber-50 border-2 border-amber-300 rounded-2xl flex items-start gap-3 print:bg-amber-50 print:border-amber-400 page-break-inside-avoid">
                <span class="text-2xl shrink-0">✏️</span>
                <div class="text-xs text-amber-900 leading-relaxed">
                    <p class="font-black uppercase tracking-wider text-amber-950 text-xs">
                        Rettifica Ordine — In Attesa di Approvazione da parte del Cliente
                    </p>
                    <p class="mt-0.5">
                        L'agente commerciale ha apportato alcune modifiche/rettifiche a quantità o prezzi per questo ordine. Il documento è in attesa di formale approvazione da parte dell'azienda cliente.
                    </p>
                </div>
            </div>
        @elseif($order->status === 'customer_approved')
            <div class="mb-8 p-4 bg-blue-50 border-2 border-blue-300 rounded-2xl flex items-start gap-3 print:bg-blue-50 print:border-blue-400 page-break-inside-avoid">
                <span class="text-2xl shrink-0">⏳</span>
                <div class="text-xs text-blue-900 leading-relaxed">
                    <p class="font-black uppercase tracking-wider text-blue-950 text-xs">
                        Modifiche Accettate dal Cliente — In Attesa di OK Finale Agente / Amministrazione
                    </p>
                    <p class="mt-0.5">
                        L'azienda cliente ha accettato le modifiche dell'ordine. Il documento è ora in attesa della conferma definitiva finale da parte dell'agente commerciale per l'inoltro alla sede.
                    </p>
                </div>
            </div>
        @elseif($order->status === 'customer_rejected')
            <div class="mb-8 p-4 bg-rose-50 border-2 border-rose-300 rounded-2xl flex items-start gap-3 print:bg-rose-50 print:border-rose-400 page-break-inside-avoid">
                <span class="text-2xl shrink-0">✕</span>
                <div class="text-xs text-rose-900 leading-relaxed">
                    <p class="font-black uppercase tracking-wider text-rose-950 text-xs">
                        Modifiche Rifiutate dal Cliente
                    </p>
                    <p class="mt-0.5">
                        L'azienda cliente non ha accettato le modifiche proposte per questo ordine.
                    </p>
                </div>
            </div>
        @elseif($order->status === 'cancelled')
            <div class="mb-8 p-4 bg-rose-50 border-2 border-rose-300 rounded-2xl flex items-start gap-3 print:bg-rose-50 print:border-rose-400 page-break-inside-avoid">
                <span class="text-2xl shrink-0">✕</span>
                <div class="text-xs text-rose-900 leading-relaxed">
                    <p class="font-black uppercase tracking-wider text-rose-950 text-xs">
                        Ordine Annullato
                    </p>
                    <p class="mt-0.5">
                        Il presente ordine risulta annullato.
                    </p>
                </div>
            </div>
        @else
            <div class="mb-8 p-4 bg-amber-50 border-2 border-amber-400 rounded-2xl flex items-start gap-3 print:bg-amber-50 print:border-amber-400 page-break-inside-avoid">
                <span class="text-2xl shrink-0">⚠️</span>
                <div class="text-xs text-amber-950 leading-relaxed">
                    <p class="font-black uppercase tracking-wider text-amber-950 text-xs">
                        Documento Non Confermato — Proposta d'Ordine in Attesa di Conferma dall'Agente
                    </p>
                    <p class="mt-0.5">
                        Il presente documento costituisce una <strong>copia commissione / proposta d'ordine</strong> e <strong>NON costituisce conferma definitiva d'ordine</strong>. L'ordine è attualmente in attesa di verifica disponibilità magazzino, tempi di consegna e conferma definitiva da parte dell'agente commerciale / amministrazione.
                    </p>
                </div>
            </div>
        @endif

        @php
            $assignedPriceList = $order->customer ? $order->customer->priceList : null;
        @endphp

        <!-- Details Grid (3 Colonne) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-8">
            <!-- 1. Dati Cliente -->
            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">INTESTATO A (CLIENTE B2B)</p>
                <h3 class="text-base font-black text-slate-900 uppercase">{{ $order->customer->business_name ?? 'Cliente N.D.' }}</h3>
                <div class="mt-2 space-y-1 text-xs text-gray-700">
                    @if($order->customer && $order->customer->vat_number)
                        <p><span class="font-bold text-gray-400 uppercase">P.IVA / C.F.:</span> {{ $order->customer->vat_number }}</p>
                    @endif
                    @if($order->customer && ($order->customer->contact_name || $order->customer->contact_surname))
                        <p><span class="font-bold text-gray-400 uppercase">REFERENTE:</span> {{ $order->customer->contact_name }} {{ $order->customer->contact_surname }}</p>
                    @endif
                    @if($order->customer && $order->customer->phone)
                        <p><span class="font-bold text-gray-400 uppercase">TEL:</span> {{ $order->customer->phone }}</p>
                    @endif
                    @if($order->customer && $order->customer->email)
                        <p><span class="font-bold text-gray-400 uppercase">EMAIL:</span> {{ $order->customer->email }}</p>
                    @endif
                </div>
            </div>

            <!-- 2. Dati Agente e Pagamento -->
            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">AGENTE DI RIFERIMENTO & METODO</p>
                <h3 class="text-base font-black text-slate-900 uppercase">
                    {{ $order->agent ? $order->agent->name . ' ' . $order->agent->surname : 'Assegnazione Diretta' }}
                </h3>
                <div class="mt-2 space-y-1 text-xs text-gray-700">
                    @if($order->agent && $order->agent->email)
                        <p><span class="font-bold text-gray-400 uppercase">EMAIL AGENTE:</span> {{ $order->agent->email }}</p>
                    @endif
                    <p class="pt-2"><span class="font-bold text-gray-400 uppercase">CONDIZIONI PAGAMENTO:</span></p>
                    <p class="font-bold text-indigo-700 uppercase">{{ $order->payment_method ?: ($order->customer->paymentCondition->name ?? 'Da concordare / Standard') }}</p>
                </div>
            </div>

            <!-- 3. Condizioni Listino Prezzi -->
            <div class="bg-indigo-50/50 p-5 rounded-2xl border border-indigo-100">
                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2">LISTINO PREZZI APPLICATO</p>
                <h3 class="text-base font-black text-indigo-950 uppercase">
                    {{ $assignedPriceList ? $assignedPriceList->name : 'Listino Prezzi Standard' }}
                </h3>
                <div class="mt-2 space-y-1 text-xs text-gray-700">
                    @if($assignedPriceList && $assignedPriceList->general_discount_percent > 0)
                        <p><span class="font-bold text-indigo-500 uppercase">SCONTO GENERALE:</span> <span class="font-black text-indigo-900">-{{ floatval($assignedPriceList->general_discount_percent) }}%</span></p>
                    @else
                        <p><span class="font-bold text-gray-400 uppercase">TIPO LISTINO:</span> <span class="font-semibold text-gray-800">Prezzi & Sconti Dedicati B2B</span></p>
                    @endif
                    <p class="pt-1 text-[11px] text-gray-500 font-medium">Condizioni commerciali riservate per <span class="font-bold text-slate-900 uppercase">{{ $order->customer->business_name ?? 'Cliente' }}</span></p>
                </div>
            </div>
        </div>

        @php
            $totalGross = 0;
            foreach ($order->items as $item) {
                $baseP = $item->product ? (float)$item->product->price : (float)$item->price;
                $totalGross += $baseP * $item->quantity;
            }
            $totalSavings = max(0, $totalGross - $order->total_amount);
        @endphp

        <!-- Items Table -->
        <div class="mb-8 overflow-x-auto min-w-0 rounded-2xl border border-gray-200">
            <table class="w-full text-left border-collapse min-w-[650px] sm:min-w-full">
                <thead class="bg-slate-900 text-white text-[11px] font-black uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-3 sm:px-4 w-10">#</th>
                        <th class="py-3 px-3 sm:px-4">Codice</th>
                        <th class="py-3 px-3 sm:px-4">Articolo / Descrizione & Listino Applicato</th>
                        <th class="py-3 px-3 sm:px-4 text-center">Taglia</th>
                        <th class="py-3 px-3 sm:px-4 text-center">Q.tà</th>
                        <th class="py-3 px-3 sm:px-4 text-right">Prezzo Base</th>
                        <th class="py-3 px-3 sm:px-4 text-right">Prezzo Unit. Netto</th>
                        <th class="py-3 px-3 sm:px-4 text-right">Subtotale</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-xs">
                    @forelse($order->items as $index => $item)
                        @php
                            $product = $item->product;
                            $customer = $order->customer;
                            $basePrice = $product ? (float)$product->price : (float)$item->price;
                            $priceDetails = $product ? $product->getPriceDetailsForCustomer($customer, $item->quantity) : null;
                            
                            $isProductException = !empty($priceDetails['is_product_exception']);
                            $isFixedPrice = ($priceDetails['discount_type'] ?? '') === 'fixed_price';
                            $hasTier = !empty($priceDetails['tier']);
                            
                            $extraDiscounts = [];
                            if (!empty($priceDetails['discount_2']) && $priceDetails['discount_2'] > 0) {
                                $extraDiscounts[] = '-' . floatval($priceDetails['discount_2']) . '%';
                            }
                            if (!empty($priceDetails['discount_3']) && $priceDetails['discount_3'] > 0) {
                                $extraDiscounts[] = '-' . floatval($priceDetails['discount_3']) . '%';
                            }
                            $extraStr = !empty($extraDiscounts) ? ' (' . implode(' ', $extraDiscounts) . ')' : '';
                        @endphp
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }} page-break-inside-avoid">
                            <td class="py-3.5 px-3 sm:px-4 font-bold text-gray-400 align-top">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-3 sm:px-4 font-mono font-bold text-slate-800 align-top whitespace-nowrap">{{ $item->product->code ?? '-' }}</td>
                            <td class="py-3.5 px-3 sm:px-4 align-top">
                                <div class="font-black text-slate-900 uppercase leading-snug">
                                    {{ $item->product->name ?? 'Prodotto N.D.' }}
                                </div>
                                @if($item->product && $item->product->brand)
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">
                                        {{ $item->product->brand->name }}
                                    </div>
                                @endif

                                <!-- Badge Regola Listino Applicata (come nel dettaglio prodotto) -->
                                <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                    @if($isProductException)
                                        <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-900 border border-amber-300 text-[9px] font-black uppercase px-2 py-0.5 rounded shadow-2xs">
                                            ⭐ Listino Personalizzato: @if($isFixedPrice) Prezzo Netto € {{ number_format($priceDetails['discount_value'], 2, ',', '.') }}{{ $extraStr }} @else {{ $priceDetails['rule_summary'] ?? 'Prezzo Dedicato' }} @endif
                                        </span>
                                    @elseif($isFixedPrice)
                                        <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 border border-amber-200 text-[9px] font-black uppercase px-2 py-0.5 rounded">
                                            ⭐ Prezzo Netto Riservato (€ {{ number_format($priceDetails['discount_value'], 2, ',', '.') }}{{ $extraStr }})
                                        </span>
                                    @elseif($hasTier || ($priceDetails['discount_type'] ?? '') === 'percentage')
                                        <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-800 border border-indigo-200 text-[9px] font-black uppercase px-2 py-0.5 rounded">
                                            📉 Sconto Quantità: {{ $priceDetails['rule_summary'] ?? ('-' . floatval($priceDetails['discount_value']) . '%') }}
                                        </span>
                                    @elseif($customer && $customer->priceList && $customer->priceList->general_discount_percent > 0)
                                        <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-800 border border-indigo-200 text-[9px] font-black uppercase px-2 py-0.5 rounded">
                                            📉 Sconto Listino (-{{ floatval($customer->priceList->general_discount_percent) }}%)
                                        </span>
                                    @elseif($item->is_modified || ($item->original_price && abs($item->original_price - $item->price) >= 0.01))
                                        <span class="inline-flex items-center gap-1 bg-orange-100 text-orange-900 border border-orange-300 text-[9px] font-black uppercase px-2 py-0.5 rounded">
                                            ✏️ Prezzo Concordato Agente
                                        </span>
                                    @elseif($basePrice > (float)$item->price)
                                        <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-800 border border-indigo-200 text-[9px] font-black uppercase px-2 py-0.5 rounded">
                                            📉 Prezzo Riservato (-{{ number_format((1 - ($item->price / max(0.01, $basePrice))) * 100, 1) }}%)
                                        </span>
                                    @endif

                                    @if(!empty($item->delivery_date))
                                        <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 border border-amber-200 text-[9px] font-black uppercase px-1.5 py-0.5 rounded">
                                            📅 Consegna dal: {{ $item->delivery_date }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-3 sm:px-4 text-center font-bold text-slate-700 align-top whitespace-nowrap">
                                {{ $item->variant ? $item->variant->size : '-' }}
                                @if($item->variant && $item->variant->color && $item->variant->color !== 'UNICO')
                                    <span class="block text-[10px] text-gray-400 font-normal uppercase">{{ $item->variant->color }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 sm:px-4 text-center font-black text-slate-900 bg-gray-100/50 align-top">
                                {{ $item->quantity }}
                            </td>
                            <td class="py-3.5 px-3 sm:px-4 text-right font-medium text-gray-500 align-top whitespace-nowrap">
                                € {{ number_format($basePrice, 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3 sm:px-4 text-right align-top whitespace-nowrap">
                                @if($basePrice > (float)$item->price)
                                    <span class="line-through text-gray-400 text-[10px] block leading-none mb-0.5">€ {{ number_format($basePrice, 2, ',', '.') }}</span>
                                    <span class="font-black text-indigo-700 text-xs">€ {{ number_format($item->price, 2, ',', '.') }}</span>
                                @else
                                    <span class="font-black text-slate-900 text-xs">€ {{ number_format($item->price, 2, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 sm:px-4 text-right font-black text-slate-900 align-top whitespace-nowrap">
                                € {{ number_format($item->quantity * $item->price, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-gray-500 font-bold">Nessun articolo in questo ordine.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php
            $uniqueProducts = $order->items->pluck('product')->filter()->unique('id');
        @endphp

        <!-- Card Dettaglio Listino Prezzi & Fasce per Articoli in Ordine (Identica alla vista prodotto) -->
        @if($assignedPriceList && $uniqueProducts->isNotEmpty())
            <div class="mb-8 bg-gradient-to-br from-indigo-50/70 via-white to-amber-50/40 border border-indigo-100 rounded-2xl p-5 sm:p-6 print:border-gray-300 print:bg-white page-break-inside-avoid">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-indigo-100/70 pb-3 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="text-2xl">📋</span>
                        <div>
                            <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest block leading-none">Condizioni Listino Riservate</span>
                            <h4 class="font-black text-indigo-950 uppercase tracking-tight text-sm mt-0.5">
                                {{ $assignedPriceList->name }}
                                @if($order->customer)
                                    <span class="text-xs font-bold text-gray-400 lowercase">per</span> <span class="text-xs font-black text-slate-900 uppercase">{{ $order->customer->business_name }}</span>
                                @endif
                            </h4>
                        </div>
                    </div>
                    @if($assignedPriceList->general_discount_percent > 0)
                        <span class="inline-flex items-center gap-1.5 bg-indigo-100 text-indigo-900 border border-indigo-200 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full self-start sm:self-auto">
                            Sconto Generale Listino: -{{ floatval($assignedPriceList->general_discount_percent) }}%
                        </span>
                    @endif
                </div>

                <div class="space-y-4">
                    @foreach($uniqueProducts as $uProd)
                        @php
                            $specificTiers = \App\Models\B2bPriceListItem::where('b2b_price_list_id', $assignedPriceList->id)
                                ->where('b2b_product_id', $uProd->id)
                                ->orderBy('min_quantity', 'asc')
                                ->get();

                            $generalTiers = $specificTiers->isEmpty()
                                ? \App\Models\B2bPriceListItem::where('b2b_price_list_id', $assignedPriceList->id)
                                    ->whereNull('b2b_product_id')
                                    ->orderBy('min_quantity', 'asc')
                                    ->get()
                                : collect();
                        @endphp

                        @if($specificTiers->isNotEmpty() || $generalTiers->isNotEmpty())
                            <div class="bg-white rounded-xl border border-gray-200 p-3.5 shadow-2xs">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2.5 border-b border-gray-100 pb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-slate-900 text-xs uppercase">{{ $uProd->name }}</span>
                                        @if($uProd->code)
                                            <span class="text-[10px] font-mono font-bold text-gray-400">({{ $uProd->code }})</span>
                                        @endif
                                        @if($uProd->brand)
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">- {{ $uProd->brand->name }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-gray-500 font-bold uppercase">Prezzo Base di Listino: € {{ number_format($uProd->price, 2, ',', '.') }}</span>
                                        @if($specificTiers->isNotEmpty())
                                            <span class="text-[9px] font-black bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded-full">
                                                ⭐ Regola Personalizzata Articolo
                                            </span>
                                        @elseif($generalTiers->isNotEmpty())
                                            <span class="text-[9px] font-black bg-indigo-100 text-indigo-900 border border-indigo-200 px-2 py-0.5 rounded-full">
                                                📉 Fasce Sconto Quantità
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($specificTiers->isNotEmpty())
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                        @foreach($specificTiers as $sTier)
                                            @php
                                                $tierPrice = $sTier->calculateUnitPrice($uProd->price);
                                            @endphp
                                            <div class="bg-amber-50/50 rounded-xl border border-amber-200 p-2.5 flex flex-col justify-between">
                                                <div class="flex justify-between items-start mb-1">
                                                    <span class="text-[10px] font-black text-gray-600 uppercase">
                                                        @if($sTier->min_quantity > 1 || !empty($sTier->max_quantity))
                                                            Da {{ $sTier->min_quantity }} {{ $sTier->max_quantity ? 'a ' . $sTier->max_quantity : 'in poi' }} pz
                                                        @else
                                                            Tutte le Quantità (1+ pz)
                                                        @endif
                                                    </span>
                                                    <span class="text-[9px] font-black text-amber-800 bg-amber-100/80 px-1.5 py-0.5 rounded border border-amber-300">
                                                        {{ $sTier->rule_summary }}
                                                    </span>
                                                </div>
                                                <div class="flex items-baseline justify-between mt-1 pt-1 border-t border-amber-200/50">
                                                    <span class="text-[10px] text-gray-500 font-bold">Prezzo Riservato:</span>
                                                    <span class="text-xs font-black text-indigo-700">€ {{ number_format($tierPrice, 2, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($generalTiers->isNotEmpty())
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                        @foreach($generalTiers as $gTier)
                                            @php
                                                $gTierPrice = $gTier->calculateUnitPrice($uProd->price);
                                            @endphp
                                            <div class="bg-indigo-50/40 rounded-xl border border-indigo-100 p-2.5 flex flex-col justify-between">
                                                <div class="flex justify-between items-start mb-1">
                                                    <span class="text-[10px] font-black text-gray-600 uppercase">
                                                        {{ $gTier->min_quantity }}{{ $gTier->max_quantity ? '-' . $gTier->max_quantity : '+' }} pz
                                                    </span>
                                                    <span class="text-[9px] font-black text-indigo-700 bg-indigo-100/80 px-1.5 py-0.5 rounded border border-indigo-200">
                                                        {{ $gTier->rule_summary }}
                                                    </span>
                                                </div>
                                                <div class="flex items-baseline justify-between mt-1 pt-1 border-t border-indigo-100/60">
                                                    <span class="text-[10px] text-gray-400 font-bold">Prezzo:</span>
                                                    <span class="text-xs font-black text-indigo-700">€ {{ number_format($gTierPrice, 2, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Totals Card -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 border-t border-gray-200 pt-6 page-break-inside-avoid">
            <div class="w-full sm:max-w-sm text-xs text-gray-500 space-y-3">
                @if($order->notes)
                    <div>
                        <p class="font-black uppercase text-gray-400 text-[10px] mb-1">📝 Note del Cliente:</p>
                        <p class="italic bg-amber-50/50 p-3 rounded-xl border border-amber-200/80 text-gray-800 leading-relaxed whitespace-pre-line">{{ $order->notes }}</p>
                    </div>
                @endif
                @if($order->admin_notes)
                    <div>
                        <p class="font-black uppercase text-indigo-700 text-[10px] mb-1">🏢 Note / Comunicazioni Sede & Agente:</p>
                        <p class="font-medium bg-indigo-50/70 p-3 rounded-xl border border-indigo-100 text-indigo-950 leading-relaxed whitespace-pre-line">{{ $order->admin_notes }}</p>
                    </div>
                @endif
            </div>

            <div class="w-full sm:w-80 space-y-2 bg-gray-50 p-5 rounded-2xl border border-gray-200 text-right">
                <div class="flex justify-between text-xs text-gray-600">
                    <span>Totale Capi / Paia:</span>
                    <span class="font-bold text-slate-900">{{ $order->items->sum('quantity') }} pz</span>
                </div>
                @if($totalSavings > 0)
                    <div class="flex justify-between text-xs text-gray-600">
                        <span>Valore Base di Listino:</span>
                        <span class="font-bold text-gray-500 line-through">€ {{ number_format($totalGross, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-emerald-700 font-bold border-b border-gray-200 pb-2">
                        <span>Sconto Riservato Applicato:</span>
                        <span>- € {{ number_format($totalSavings, 2, ',', '.') }} ({{ number_format(($totalSavings / max(0.01, $totalGross)) * 100, 1) }}%)</span>
                    </div>
                @else
                    <div class="flex justify-between text-xs text-gray-600 border-b border-gray-200 pb-2">
                        <span>Imponibile Merce:</span>
                        <span class="font-bold text-slate-900">€ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-base font-black text-slate-900 pt-1">
                    <span class="uppercase">TOTALE ORDINE:</span>
                    <span class="text-indigo-600">€ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-2 text-[10px] text-gray-400 page-break-inside-avoid">
            <p>
                Documento generato dal Portale B2B BICAP - {{ date('d/m/Y H:i') }} 
                @if($order->status === 'confirmed')
                    • <span class="text-emerald-700 font-bold uppercase">ORDINE CONFERMATO</span>
                @else
                    • <span class="text-amber-700 font-bold uppercase">STATO: {{ strtoupper($order->status_label) }} (NON CONFERMATO)</span>
                @endif
            </p>
            <p class="font-bold uppercase">Calzaturificio 5Bi S.r.l. - Barletta (BT) - www.bicap.it</p>
        </div>
    </div>

</body>
</html>
