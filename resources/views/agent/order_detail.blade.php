@php
    $isEn = app()->getLocale() === 'en';
@endphp
<x-agent-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('agent.orders') }}" class="bg-white border border-gray-200 text-gray-500 p-2 rounded-xl hover:bg-gray-50 transition">
                    <span class="text-xl leading-none">←</span>
                </a>
                <div>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <h2 class="text-xl sm:text-2xl font-black text-gray-800 tracking-tight uppercase">
                            {{ $isEn ? 'Order Detail' : 'Dettaglio Ordine' }} #{{ $order->id }}
                        </h2>
                        @if($order->is_modified || $order->items->contains('is_modified', true))
                            <span class="bg-amber-100 border border-amber-300 text-amber-900 text-[10px] sm:text-xs px-2.5 sm:px-3 py-1 rounded-full font-black uppercase tracking-widest flex items-center gap-1 shadow-sm">
                                ✏️ {{ $isEn ? 'Order Modified' : 'Ordine Modificato' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('agent.orders.pdf', $order) }}" target="_blank" class="bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black uppercase text-xs px-5 py-2.5 rounded-xl shadow transition duration-200 flex items-center gap-2">
                    <span>🖨️ {{ $isEn ? 'PRINT / PDF' : 'STAMPA / PDF' }}</span>
                </a>
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2 rounded-xl text-xs font-bold shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- BLOCCO AZIONI APPROVAZIONE CLIENTE -->
        @if(Auth::user()->role === 'customer' && $order->status === 'revision_pending')
            <div class="bg-slate-900 border-2 border-yellow-400 rounded-3xl p-8 text-white shadow-2xl flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 rounded-2xl bg-yellow-400 text-slate-950 flex items-center justify-center text-3xl font-black shrink-0 shadow-lg">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-white">
                            {{ $isEn ? 'The Agent has modified this order' : "L'Agente ha apportato modifiche a questo ordine" }}
                        </h3>
                        <p class="text-xs font-bold text-gray-300 mt-1 max-w-xl leading-relaxed">
                            {{ $isEn ? 'Quantities and prices have been updated. Your approval is required to proceed with order fulfillment.' : "Le quantità ed i prezzi sono stati aggiornati. Per procedere con l'evasione dell'ordine è richiesta la tua approvazione." }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    <form action="{{ route('agent.orders.accept', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black uppercase text-sm px-8 py-4 rounded-2xl shadow-xl transition duration-300 hover:scale-105 flex items-center gap-2 border-2 border-yellow-300 cursor-pointer">
                            <span>✅ {{ $isEn ? 'ACCEPT ORDER MODIFICATIONS' : 'ACCETTA MODIFICHE ORDINE' }}</span>
                        </button>
                    </form>
                    <form action="{{ route('agent.orders.reject', $order) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('{{ $isEn ? 'Are you sure you want to reject the order modifications?' : 'Sei sicuro di voler rifiutare le modifiche dell\'ordine?' }}');" class="bg-rose-600 hover:bg-rose-700 text-white font-black uppercase text-xs px-6 py-4 rounded-2xl shadow-md transition hover:scale-105 border border-rose-500 cursor-pointer">
                            ✕ {{ $isEn ? 'REJECT' : 'RIFIUTA' }}
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if(Auth::user()->role === 'customer' && $order->status === 'customer_approved')
            <div class="bg-blue-50 border border-blue-200 rounded-3xl p-6 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-bold shrink-0">
                        ⏳
                    </div>
                    <div>
                        <h4 class="text-blue-900 font-black uppercase text-sm tracking-wider">{{ $isEn ? 'Modifications Accepted by Customer' : "Modifiche Accettate dall'Azienda" }}</h4>
                        <p class="text-xs text-blue-700 mt-0.5">{{ $isEn ? 'You accepted the order modifications. Waiting for final confirmation from the agent / administration.' : "Hai accettato le modifiche dell'ordine. In attesa dell'OK finale di conferma dall'agente / amministrazione." }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(Auth::user()->role === 'customer' && $order->status === 'customer_rejected')
            <div class="bg-rose-50 border border-rose-200 rounded-3xl p-6 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl font-bold shrink-0">
                        ✕
                    </div>
                    <div>
                        <h4 class="text-rose-900 font-black uppercase text-sm tracking-wider">{{ $isEn ? 'Modifications Rejected' : 'Modifiche Rifiutate' }}</h4>
                        <p class="text-xs text-rose-700 mt-0.5">{{ $isEn ? 'You rejected the order modifications. Waiting for a new proposal or adjustment from the agent / administration.' : "Hai rifiutato le modifiche dell'ordine. In attesa di una nuova proposta o rettifica da parte dell'agente / amministrazione." }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(Auth::user()->role === 'customer' && $order->status === 'cancelled')
            <div class="bg-rose-50 border border-rose-200 rounded-3xl p-6 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl font-bold shrink-0">
                        ✕
                    </div>
                    <div>
                        <h4 class="text-rose-900 font-black uppercase text-sm tracking-wider">{{ $isEn ? 'Order Cancelled' : 'Ordine Annullato' }}</h4>
                        <p class="text-xs text-rose-700 mt-0.5">{{ $isEn ? 'This order has been cancelled. No further action is required.' : 'Questo ordine è stato annullato. Non è richiesta alcuna ulteriore azione.' }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- BLOCCO CONFERMA / ANNULLAMENTO ORDINE AGENTE -->
        @if(Auth::user()->role === 'agent' || Auth::user()->role === 'admin')
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-emerald-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <p class="text-xs font-black text-gray-500 uppercase tracking-widest mb-1">{{ $isEn ? 'Order Workflow Status' : 'Stato Flusso Ordine' }}</p>
                    @if($order->status === 'customer_approved')
                        <p class="text-emerald-700 font-black text-sm uppercase">✓ {{ $isEn ? 'The customer accepted the order modifications!' : "L'azienda cliente ha accettato le modifiche dell'ordine!" }}</p>
                    @elseif($order->status === 'revision_pending')
                        <p class="text-amber-700 font-black text-sm uppercase">⏳ {{ $isEn ? 'Waiting for customer acceptance of the proposed modifications.' : "In attesa dell'accettazione dell'azienda per le modifiche apportate." }}</p>
                    @elseif($order->status === 'customer_rejected')
                        <p class="text-rose-700 font-black text-sm uppercase">✕ {{ $isEn ? 'The customer rejected the proposed modifications. You can adjust it again below.' : 'Il cliente ha rifiutato le modifiche proposte. Puoi apportare una nuova rettifica nella tabella sottostante.' }}</p>
                    @elseif($order->status === 'confirmed')
                        <p class="text-emerald-600 font-black text-sm uppercase">✓ {{ $isEn ? 'Order Confirmed and Sent to Head Office.' : 'Ordine Confermato ed Inviato alla Sede.' }}</p>
                    @elseif($order->status === 'cancelled')
                        <p class="text-rose-700 font-black text-sm uppercase">✕ {{ $isEn ? 'Order Cancelled (Inactive).' : 'Ordine Annullato (Non attivo).' }}</p>
                    @else
                        <p class="text-slate-800 font-black text-sm uppercase">{{ $isEn ? 'Waiting for order confirmation by the agent.' : "In attesa di conferma ordine da parte dell'agente." }}</p>
                    @endif
                </div>
                @if($order->status !== 'confirmed' && $order->status !== 'cancelled')
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form action="{{ route('agent.orders.cancel', $order) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('{{ $isEn ? 'Are you sure you want to permanently cancel Order #' . $order->id . '?' : 'Sei sicuro di voler annullare definitivamente l\'Ordine #' . $order->id . '?' }}');" class="w-full sm:w-auto bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 hover:border-rose-600 font-black uppercase text-xs px-5 py-3.5 rounded-xl shadow-sm hover:shadow transition duration-200 tracking-wide flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>✕ {{ $isEn ? 'Cancel Order' : 'Annulla Ordine' }}</span>
                            </button>
                        </form>

                        <form action="{{ route('agent.orders.confirm', $order) }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            @csrf
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none bg-emerald-50/70 border border-emerald-200/80 px-3.5 py-2.5 rounded-xl hover:bg-emerald-100/70 transition">
                                <input type="checkbox" name="send_email_to_customer" value="1" checked class="w-4 h-4 text-emerald-600 bg-white border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 cursor-pointer">
                                <span class="text-xs font-bold text-slate-700">✉️ {{ $isEn ? 'Send confirmation email to customer' : 'Invia email di conferma al cliente' }}</span>
                            </label>
                            <button type="submit" onclick="return confirm('{{ $isEn ? 'Confirm order to send to head office?' : 'Confermare l\'ordine per l\'invio alla sede?' }}');" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase text-xs px-8 py-3.5 rounded-xl shadow-lg transition tracking-wide flex items-center justify-center gap-2 cursor-pointer">
                                <span>🚀 {{ $isEn ? 'Confirm order' : 'Conferma ordine' }}</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endif

        <!-- Order Header Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <p class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">{{ $isEn ? 'Customer Information' : 'Informazioni Cliente' }}</p>
                <p class="text-lg font-black text-slate-900 uppercase tracking-tight">{{ $order->customer->business_name }}</p>
                <div class="mt-4 space-y-2 text-sm text-gray-700">
                    <p><strong class="uppercase text-[10px] text-gray-400">{{ $isEn ? 'VAT / TAX ID:' : 'P.IVA:' }}</strong> {{ $order->customer->vat_number }}</p>
                    <p><strong class="uppercase text-[10px] text-gray-400">REF:</strong> {{ $order->customer->contact_name }} {{ $order->customer->contact_surname }}</p>
                    <p><strong class="uppercase text-[10px] text-gray-400">{{ $isEn ? 'TEL:' : 'TEL:' }}</strong> {{ $order->customer->phone }}</p>
                    <p><strong class="uppercase text-[10px] text-gray-400">{{ $isEn ? 'EMAIL:' : 'MAIL:' }}</strong> {{ $order->customer->email }}</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <p class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">{{ $isEn ? 'Order Summary' : 'Riepilogo Ordine' }}</p>
                <div class="space-y-4">
                    <div class="flex justify-between border-b border-gray-50 pb-2 items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase">{{ $isEn ? 'Current Status' : 'Stato Attuale' }}</span>
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
                                'pending' => $isEn ? 'Pending' : 'In Attesa',
                                'revision_pending' => $isEn ? 'Modified (Pending Customer)' : 'Modificato (Attesa Cliente)',
                                'customer_approved' => $isEn ? 'Approved by Customer' : 'Approvato da Cliente',
                                'customer_rejected' => $isEn ? 'Rejected by Customer' : 'Rifiutato da Cliente',
                                'confirmed' => $isEn ? 'Confirmed' : 'Confermato',
                                'cancelled' => $isEn ? 'Cancelled' : 'Annullato',
                            ];
                        @endphp
                        <span class="text-xs px-3 py-1 rounded-full font-black uppercase tracking-widest border {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-xs font-bold text-gray-400 uppercase">{{ $isEn ? 'Submission Date' : 'Data Invio' }}</span>
                        <span class="text-xs font-black uppercase text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($order->internal_reference)
                    <div class="flex justify-between border-b border-gray-50 pb-2 items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase">{{ $isEn ? 'Internal Ref.' : 'Rif. Interno' }}</span>
                        <span class="text-xs font-black font-mono uppercase bg-slate-100 text-slate-800 border border-slate-200 px-2 py-0.5 rounded-md">{{ $order->internal_reference }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-xs font-bold text-gray-400 uppercase">{{ $isEn ? 'Total Items' : 'Totale Pezzi' }}</span>
                        <span class="text-xs font-black uppercase text-gray-900">{{ $order->items->sum('quantity') }}</span>
                    </div>

                    <a href="{{ route('agent.orders.pdf', $order) }}" target="_blank" class="mt-4 w-full bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black uppercase text-xs py-3 px-4 rounded-2xl shadow transition duration-200 flex items-center justify-center gap-2">
                        <span>🖨️ {{ $isEn ? 'PRINT / SAVE ORDER PDF' : 'STAMPA / SALVA PDF ORDINE' }}</span>
                    </a>
                </div>
            </div>

            <!-- Note Inserite dal Cliente -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-base">📝</span>
                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ $isEn ? 'Customer Notes' : 'Note del Cliente' }}</p>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed bg-amber-50/50 p-4 rounded-2xl border border-amber-200/70 italic whitespace-pre-line">
                        {{ $order->notes ?: ($isEn ? 'No special notes entered at the time of order.' : 'Nessuna nota speciale inserita al momento dell\'ordine.') }}
                    </p>
                </div>
                @if($order->payment_method)
                    <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-400 uppercase text-[10px]">{{ $isEn ? 'Payment Method:' : 'Metodo Pagamento:' }}</span>
                        <span class="font-black text-slate-800 uppercase">{{ $order->payment_method }}</span>
                    </div>
                @endif
            </div>
        </div>

        @if(Auth::user()->role === 'customer')
            <!-- Visualizzazione Messaggio per il Cliente -->
            @if($order->admin_notes)
                <div class="rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row items-start sm:items-center gap-6" style="background-color: #fffdf5; border: 2px solid #f59e0b;">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl font-black shrink-0 shadow-sm" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                        🏢
                    </div>
                    <div class="flex-1 w-full">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-xs font-black uppercase tracking-widest" style="color: #92400e;">{{ $isEn ? 'Message / Notes from Head Office & Agent' : 'Messaggio / Note dalla Sede & Agente' }}</h3>
                        </div>
                        <div class="text-sm font-bold leading-relaxed whitespace-pre-line p-4 rounded-2xl shadow-sm" style="color: #0f172a; background-color: #ffffff; border: 1.5px solid #fde68a;">
                            {{ $order->admin_notes }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tabella Articoli Cliente (Solo Lettura) -->
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 sm:px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
                    <h3 class="font-black text-gray-800 uppercase text-xs tracking-wider">{{ $isEn ? 'Order Items' : 'Articoli in Ordine' }}</h3>
                    @if($order->status === 'confirmed')
                        <span class="bg-emerald-100 border border-emerald-300 text-emerald-900 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">
                            🔒 {{ $isEn ? 'Order Confirmed and Sent to Head Office (Locked)' : 'Ordine Confermato ed Inviato alla Sede (Bloccato)' }}
                        </span>
                    @elseif($order->status === 'cancelled')
                        <span class="bg-rose-100 border border-rose-300 text-rose-900 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">
                            ✕ {{ $isEn ? 'Order Cancelled' : 'Ordine Annullato' }}
                        </span>
                    @elseif($order->is_modified || $order->items->contains('is_modified', true))
                        <span class="bg-amber-100 border border-amber-200 text-amber-900 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">
                            ✏️ {{ $isEn ? 'Order Revised by Agent' : "Ordine Revisionato dall'Agente" }}
                        </span>
                    @endif
                </div>
                <div class="overflow-x-auto min-w-0">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/20">
                            <tr class="text-xs font-black text-gray-500 uppercase tracking-widest border-b border-gray-100">
                                <th class="px-6 sm:px-8 py-5">{{ $isEn ? 'Product' : 'Prodotto' }}</th>
                                <th class="px-4 sm:px-8 py-5 text-center">{{ $isEn ? 'Color' : 'Colore' }}</th>
                                <th class="px-4 sm:px-8 py-5 text-center">{{ $isEn ? 'Size' : 'Taglia' }}</th>
                                <th class="px-4 sm:px-8 py-5 text-center">{{ $isEn ? 'Quantity' : 'Quantità' }}</th>
                                <th class="px-4 sm:px-8 py-5 text-right">{{ $isEn ? 'Unit Price' : 'Prezzo Unit.' }}</th>
                                <th class="px-6 sm:px-8 py-5 text-right">{{ $isEn ? 'Subtotal' : 'Subtotale' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($order->items as $item)
                                @php
                                    $qtyChanged = $item->original_quantity && $item->original_quantity != $item->quantity;
                                    $priceChanged = $item->original_price && abs($item->original_price - $item->price) >= 0.01;
                                    $itemProd = $item->product;
                                    $itemImg = $itemProd && $itemProd->image ? (\Illuminate\Support\Str::startsWith($itemProd->image, ['http://', 'https://']) ? $itemProd->image : asset('storage/' . $itemProd->image)) : null;
                                    $productName = $itemProd ? ($itemProd->localized_description ?: $itemProd->name) : 'N/A';
                                @endphp
                                <tr class="hover:bg-gray-50 transition {{ $item->is_modified || $qtyChanged || $priceChanged ? 'bg-amber-50/40' : '' }}">
                                    <td class="px-6 sm:px-8 py-6">
                                        <a href="{{ route('agent.product', $item->b2b_product_id) }}" class="flex items-center space-x-4 group/item">
                                            <div class="w-14 h-14 bg-gray-50 border border-gray-200 rounded-2xl flex items-center justify-center p-1.5 overflow-hidden shrink-0 group-hover/item:border-indigo-500 group-hover/item:shadow-md transition">
                                                @if($itemImg)
                                                    <img src="{{ $itemImg }}" alt="{{ $productName }}" class="max-w-full max-h-full object-contain group-hover/item:scale-105 transition duration-300">
                                                @else
                                                    <span class="text-xl">👕</span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900 group-hover/item:text-indigo-600 transition uppercase text-sm leading-tight">{{ $productName }}</p>
                                                <p class="text-xs text-slate-400 font-bold uppercase tracking-tight mb-1">{{ $item->product->brand->name }}</p>
                                                @if(!empty($item->delivery_date))
                                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 border border-amber-100 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">
                                                        📅 {{ $isEn ? 'Delivery from:' : 'Consegna dal:' }} {{ $item->delivery_date }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 border border-emerald-100 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">
                                                        ✓ {{ $isEn ? 'Immediate Disp.' : 'Disp. Immediata' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </a>
                                    </td>
                                    <td class="px-4 sm:px-8 py-6 text-center text-sm font-bold uppercase text-gray-600">{{ (!empty($item->variant->color) && strtoupper($item->variant->color) !== 'UNICO') ? $item->variant->color : ($isEn ? 'Unique' : 'Unico') }}</td>
                                    <td class="px-4 sm:px-8 py-6 text-center text-sm font-black text-slate-900">{{ $item->variant->size }}</td>
                                    <td class="px-4 sm:px-8 py-6 text-center">
                                        @if($qtyChanged)
                                            <span class="text-xs text-gray-400 line-through font-bold block">{{ $isEn ? 'Initial:' : 'Iniziale:' }} {{ $item->original_quantity }}</span>
                                            <span class="font-black text-indigo-700 text-base block">{{ $item->quantity }}</span>
                                            <span class="inline-block text-[9px] font-black bg-amber-100 text-amber-800 border border-amber-200 px-1.5 py-0.5 rounded uppercase mt-0.5">{{ $isEn ? 'Modified' : 'Modificata' }}</span>
                                        @else
                                            <span class="font-black text-gray-900 text-base">{{ $item->quantity }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-8 py-6 text-right">
                                        @if($priceChanged)
                                            <span class="text-xs text-gray-400 line-through font-bold block">{{ $isEn ? 'Initial:' : 'Iniziale:' }} € {{ number_format($item->original_price, 2, ',', '.') }}</span>
                                            <span class="font-black text-indigo-700 text-sm block">€ {{ number_format($item->price, 2, ',', '.') }}</span>
                                            <span class="inline-block text-[9px] font-black bg-amber-100 text-amber-800 border border-amber-200 px-1.5 py-0.5 rounded uppercase mt-0.5">{{ $isEn ? 'Modified' : 'Modificato' }}</span>
                                        @else
                                            <span class="text-sm font-bold text-gray-600">€ {{ number_format($item->price, 2, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 sm:px-8 py-6 text-right font-black text-slate-900 text-base">
                                        € {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/50">
                            <tr>
                                <td colspan="5" class="px-6 sm:px-8 py-8 text-right text-xs font-black text-gray-500 uppercase tracking-widest">{{ $isEn ? 'Total Order Value' : 'Valore Totale Ordine' }}</td>
                                <td class="px-6 sm:px-8 py-8 text-right text-2xl sm:text-3xl font-black text-slate-950">€ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @elseif($order->status === 'confirmed' || $order->status === 'cancelled')
            <!-- ORDINE CONFERMATO O ANNULLATO PER AGENTE/ADMIN (Solo Lettura) -->
            @if($order->admin_notes)
                <div class="rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row items-start sm:items-center gap-6" style="background-color: #fffdf5; border: 2px solid #f59e0b;">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl font-black shrink-0 shadow-sm" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                        🏢
                    </div>
                    <div class="flex-1 w-full">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-xs font-black uppercase tracking-widest" style="color: #92400e;">{{ $isEn ? 'Message / Notes Head Office & Agent' : 'Messaggio / Note Sede & Agente' }}</h3>
                        </div>
                        <div class="text-sm font-bold leading-relaxed whitespace-pre-line p-4 rounded-2xl shadow-sm" style="color: #0f172a; background-color: #ffffff; border: 1.5px solid #fde68a;">
                            {{ $order->admin_notes }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 sm:px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
                    <h3 class="font-black text-gray-800 uppercase text-xs tracking-wider">{{ $isEn ? 'Order Items' : 'Articoli in Ordine' }}</h3>
                    @if($order->status === 'confirmed')
                        <span class="bg-emerald-100 border border-emerald-300 text-emerald-900 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">
                            🔒 {{ $isEn ? 'Order Confirmed and Sent to Head Office (Locked)' : 'Ordine Confermato ed Inviato alla Sede (Bloccato)' }}
                        </span>
                    @elseif($order->status === 'cancelled')
                        <span class="bg-rose-100 border border-rose-300 text-rose-900 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">
                            ✕ {{ $isEn ? 'Order Cancelled' : 'Ordine Annullato' }}
                        </span>
                    @endif
                </div>
                <div class="overflow-x-auto min-w-0">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/20">
                            <tr class="text-xs font-black text-gray-500 uppercase tracking-widest border-b border-gray-100">
                                <th class="px-6 sm:px-8 py-5">{{ $isEn ? 'Product' : 'Prodotto' }}</th>
                                <th class="px-4 sm:px-8 py-5 text-center">{{ $isEn ? 'Color' : 'Colore' }}</th>
                                <th class="px-4 sm:px-8 py-5 text-center">{{ $isEn ? 'Size' : 'Taglia' }}</th>
                                <th class="px-4 sm:px-8 py-5 text-center">{{ $isEn ? 'Quantity' : 'Quantità' }}</th>
                                <th class="px-4 sm:px-8 py-5 text-right">{{ $isEn ? 'Unit Price' : 'Prezzo Unit.' }}</th>
                                <th class="px-6 sm:px-8 py-5 text-right">{{ $isEn ? 'Subtotal' : 'Subtotale' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($order->items as $item)
                                @php
                                    $qtyChanged = $item->original_quantity && $item->original_quantity != $item->quantity;
                                    $priceChanged = $item->original_price && abs($item->original_price - $item->price) >= 0.01;
                                    $itemProd = $item->product;
                                    $itemImg = $itemProd && $itemProd->image ? (\Illuminate\Support\Str::startsWith($itemProd->image, ['http://', 'https://']) ? $itemProd->image : asset('storage/' . $itemProd->image)) : null;
                                    $productName = $itemProd ? ($itemProd->localized_description ?: $itemProd->name) : 'N/A';
                                @endphp
                                <tr class="hover:bg-gray-50 transition {{ $item->is_modified || $qtyChanged || $priceChanged ? 'bg-amber-50/40' : '' }}">
                                    <td class="px-6 sm:px-8 py-6">
                                        <a href="{{ route('agent.product', $item->b2b_product_id) }}" class="flex items-center space-x-4 group/item">
                                            <div class="w-14 h-14 bg-gray-50 border border-gray-200 rounded-2xl flex items-center justify-center p-1.5 overflow-hidden shrink-0 group-hover/item:border-indigo-500 group-hover/item:shadow-md transition">
                                                @if($itemImg)
                                                    <img src="{{ $itemImg }}" alt="{{ $productName }}" class="max-w-full max-h-full object-contain group-hover/item:scale-105 transition duration-300">
                                                @else
                                                    <span class="text-xl">👕</span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900 group-hover/item:text-indigo-600 transition uppercase text-sm leading-tight">{{ $productName }}</p>
                                                <p class="text-xs text-slate-400 font-bold uppercase tracking-tight mb-1">{{ $item->product->brand->name }}</p>
                                                @if(!empty($item->delivery_date))
                                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 border border-amber-100 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">
                                                        📅 {{ $isEn ? 'Delivery from:' : 'Consegna dal:' }} {{ $item->delivery_date }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 border border-emerald-100 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">
                                                        ✓ {{ $isEn ? 'Immediate Disp.' : 'Disp. Immediata' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </a>
                                    </td>
                                    <td class="px-4 sm:px-8 py-6 text-center text-sm font-bold uppercase text-gray-600">{{ (!empty($item->variant->color) && strtoupper($item->variant->color) !== 'UNICO') ? $item->variant->color : ($isEn ? 'Unique' : 'Unico') }}</td>
                                    <td class="px-4 sm:px-8 py-6 text-center text-sm font-black text-slate-900">{{ $item->variant->size }}</td>
                                    <td class="px-4 sm:px-8 py-6 text-center">
                                        @if($qtyChanged)
                                            <span class="text-xs text-gray-400 line-through font-bold block">{{ $isEn ? 'Initial:' : 'Iniziale:' }} {{ $item->original_quantity }}</span>
                                            <span class="font-black text-indigo-700 text-base block">{{ $item->quantity }}</span>
                                            <span class="inline-block text-[9px] font-black bg-amber-100 text-amber-800 border border-amber-200 px-1.5 py-0.5 rounded uppercase mt-0.5">{{ $isEn ? 'Modified' : 'Modificata' }}</span>
                                        @else
                                            <span class="font-black text-gray-900 text-base">{{ $item->quantity }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-8 py-6 text-right">
                                        @if($priceChanged)
                                            <span class="text-xs text-gray-400 line-through font-bold block">{{ $isEn ? 'Initial:' : 'Iniziale:' }} € {{ number_format($item->original_price, 2, ',', '.') }}</span>
                                            <span class="font-black text-indigo-700 text-sm block">€ {{ number_format($item->price, 2, ',', '.') }}</span>
                                            <span class="inline-block text-[9px] font-black bg-amber-100 text-amber-800 border border-amber-200 px-1.5 py-0.5 rounded uppercase mt-0.5">{{ $isEn ? 'Modified' : 'Modificato' }}</span>
                                        @else
                                            <span class="text-sm font-bold text-gray-600">€ {{ number_format($item->price, 2, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 sm:px-8 py-6 text-right font-black text-slate-900 text-base">
                                        € {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/50">
                            <tr>
                                <td colspan="5" class="px-6 sm:px-8 py-8 text-right text-xs font-black text-gray-500 uppercase tracking-widest">{{ $isEn ? 'Total Order Value' : 'Valore Totale Ordine' }}</td>
                                <td class="px-6 sm:px-8 py-8 text-right text-2xl sm:text-3xl font-black text-slate-950">€ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @else
            <!-- FORM UNIFICATO PER AGENTE / ADMIN (Modifica Ordine & Note Sede) -->
            <form action="{{ route('agent.orders.update_items', $order) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Card Messaggio / Note Sede & Agente -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-indigo-100">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4 border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🏢</span>
                            <div>
                                <h3 class="text-xs font-black text-indigo-950 uppercase tracking-widest">{{ $isEn ? 'Head Office & Agent Notes / Message' : 'Messaggio / Note Sede & Agente' }}</h3>
                                <p class="text-[11px] text-gray-400 font-medium">{{ $isEn ? 'This message is visible to the customer in their order details and appears on the PDF summary.' : 'Questo messaggio è visibile al cliente nel suo dettaglio ordine e compare nel PDF di riepilogo.' }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <textarea name="admin_notes" rows="3" placeholder="{{ $isEn ? 'Enter notices, delivery instructions, or special notes for the customer...' : 'Inserisci comunicazioni, istruzioni di consegna o note speciali per il cliente...' }}" class="w-full border-gray-200 rounded-2xl text-sm focus:ring-yellow-400 focus:border-yellow-400 p-4 bg-gray-50/60 shadow-inner leading-relaxed">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                    </div>
                    <div class="flex justify-end pt-3">
                        <button type="submit" class="bg-slate-950 hover:bg-slate-800 text-yellow-400 hover:text-yellow-300 font-black text-xs uppercase px-6 py-3 rounded-xl shadow transition duration-200 flex items-center gap-2 cursor-pointer">
                            <span>💾 {{ $isEn ? 'Save Notes / Message' : 'Salva Messaggio / Note Sede' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Card Tabella Articoli in Ordine -->
                <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 sm:px-8 py-6 border-b border-gray-50 bg-gray-50/30">
                        <h3 class="font-black text-gray-800 uppercase text-xs tracking-wider">{{ $isEn ? 'Order Items (Editable by Agent)' : "Articoli in Ordine (Modificabili dall'Agente)" }}</h3>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $isEn ? 'Modifications to notes or items will save the order and place it awaiting customer approval.' : "Le modifiche a note o articoli salveranno l'ordine ponendolo in attesa dell'accettazione del cliente." }}</p>
                    </div>
                    
                    <div class="overflow-x-auto min-w-0">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/20">
                                <tr class="text-xs font-black text-gray-500 uppercase tracking-widest border-b border-gray-100">
                                    <th class="px-6 sm:px-8 py-5">{{ $isEn ? 'Product' : 'Prodotto' }}</th>
                                    <th class="px-4 sm:px-8 py-5 text-center">{{ $isEn ? 'Color' : 'Colore' }}</th>
                                    <th class="px-4 sm:px-8 py-5 text-center">{{ $isEn ? 'Size' : 'Taglia' }}</th>
                                    <th class="px-4 sm:px-8 py-5 text-center">{{ $isEn ? 'Quantity' : 'Quantità' }}</th>
                                    <th class="px-4 sm:px-8 py-5 text-right">{{ $isEn ? 'Unit Price (€)' : 'Prezzo Unit. (€)' }}</th>
                                    <th class="px-6 sm:px-8 py-5 text-right">{{ $isEn ? 'Subtotal' : 'Subtotale' }}</th>
                                    <th class="px-4 py-5 text-center">{{ $isEn ? 'Action' : 'Azione' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($order->items as $item)
                                    @php
                                        $qtyChanged = $item->original_quantity && $item->original_quantity != $item->quantity;
                                        $priceChanged = $item->original_price && abs($item->original_price - $item->price) >= 0.01;
                                        $itemProd = $item->product;
                                        $itemImg = $itemProd && $itemProd->image ? (\Illuminate\Support\Str::startsWith($itemProd->image, ['http://', 'https://']) ? $itemProd->image : asset('storage/' . $itemProd->image)) : null;
                                        $productName = $itemProd ? ($itemProd->localized_description ?: $itemProd->name) : 'N/A';
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 transition {{ $item->is_modified || $qtyChanged || $priceChanged ? 'bg-amber-50/30' : '' }}" 
                                        x-data="{
                                            qty: {{ $item->quantity }},
                                            price: {{ number_format($item->price, 2, '.', '') }}
                                        }">
                                        <td class="px-6 sm:px-8 py-6">
                                            <a href="{{ route('agent.product', $item->b2b_product_id) }}" class="flex items-center space-x-4 group/item">
                                                <div class="w-14 h-14 bg-gray-50 border border-gray-200 rounded-2xl flex items-center justify-center p-1.5 overflow-hidden shrink-0 group-hover/item:border-indigo-500 group-hover/item:shadow-md transition">
                                                    @if($itemImg)
                                                        <img src="{{ $itemImg }}" alt="{{ $productName }}" class="max-w-full max-h-full object-contain group-hover/item:scale-105 transition duration-300">
                                                    @else
                                                        <span class="text-xl">👕</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-black text-gray-900 group-hover/item:text-indigo-600 transition uppercase text-sm leading-tight">{{ $productName }}</p>
                                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-tight mb-1">{{ $item->product->brand->name }}</p>
                                                    @if(!empty($item->delivery_date))
                                                        <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 border border-amber-100 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">
                                                            📅 {{ $isEn ? 'Delivery from:' : 'Consegna dal:' }} {{ $item->delivery_date }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 border border-emerald-100 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">
                                                            ✓ {{ $isEn ? 'Immediate Disp.' : 'Disp. Immediata' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </a>
                                        </td>
                                        <td class="px-4 sm:px-8 py-6 text-center text-sm font-bold uppercase text-gray-600">{{ (!empty($item->variant->color) && strtoupper($item->variant->color) !== 'UNICO') ? $item->variant->color : ($isEn ? 'Unique' : 'Unico') }}</td>
                                        <td class="px-4 sm:px-8 py-6 text-center text-sm font-black text-slate-900">{{ $item->variant->size }}</td>
                                        <td class="px-4 sm:px-8 py-6 text-center">
                                            <input type="number" name="items[{{ $item->id }}][quantity]" x-model.number="qty" min="1" required 
                                                   class="w-20 text-center rounded-xl border-gray-200 p-2 font-black text-sm text-slate-900 bg-gray-50 focus:border-indigo-500">
                                            @if($qtyChanged)
                                                <span class="block text-[10px] font-black text-amber-700 line-through mt-1" title="{{ $isEn ? 'Initial Quantity before edit' : 'Quantità Iniziale prima della modifica' }}">
                                                    {{ $isEn ? 'Initial:' : 'Iniziale:' }} {{ $item->original_quantity }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 sm:px-8 py-6 text-right">
                                            <input type="number" step="0.01" min="0" name="items[{{ $item->id }}][price]" x-model.number="price" required 
                                                   class="w-28 text-right rounded-xl border-gray-200 p-2 font-black text-sm text-indigo-700 bg-gray-50 focus:border-indigo-500">
                                            @if($priceChanged)
                                                <span class="block text-[10px] font-black text-amber-700 line-through mt-1" title="{{ $isEn ? 'Initial Price before edit' : 'Prezzo Iniziale prima della modifica' }}">
                                                    {{ $isEn ? 'Initial:' : 'Iniziale:' }} € {{ number_format($item->original_price, 2, ',', '.') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 sm:px-8 py-6 text-right font-black text-slate-900 text-base">
                                            € <span x-text="(parseFloat(qty || 0) * parseFloat(price || 0)).toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                        </td>
                                        <td class="px-4 py-6 text-center">
                                            <button type="submit" name="delete_items[]" value="{{ $item->id }}" onclick="return confirm('{{ $isEn ? 'Remove this item from the order?' : 'Rimuovere questo articolo dall\'ordine?' }}');" 
                                                    class="text-rose-500 hover:text-rose-700 font-bold text-xs uppercase px-2 py-1 bg-rose-50 hover:bg-rose-100 rounded-lg transition" title="{{ $isEn ? 'Delete line' : 'Elimina riga' }}">
                                                ✕ {{ $isEn ? 'Remove' : 'Rimuovi' }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50/50">
                                <tr>
                                    <td colspan="5" class="px-6 sm:px-8 py-8 text-right text-xs font-black text-gray-500 uppercase tracking-widest">{{ $isEn ? 'Total Order Value' : 'Valore Totale Ordine' }}</td>
                                    <td colspan="2" class="px-6 sm:px-8 py-8 text-right text-2xl sm:text-3xl font-black text-slate-950">€ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="p-6 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none bg-white border border-gray-200 px-3.5 py-2 rounded-xl hover:bg-gray-100 transition shadow-sm">
                            <input type="checkbox" name="send_email_to_customer" value="1" checked class="w-4 h-4 text-indigo-600 bg-white border-gray-300 rounded focus:ring-indigo-500 focus:ring-2 cursor-pointer">
                            <span class="text-xs font-bold text-slate-700">✉️ {{ $isEn ? 'Send adjustment/modification notification to customer by email' : 'Invia notifica di rettifica/modifica al cliente via email' }}</span>
                        </label>
                        <button type="submit" class="px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg transition flex items-center gap-2 cursor-pointer">
                            <span>💾 {{ $isEn ? 'Save Order Modifications (Notes & Items)' : 'Salva Modifiche Ordine (Note & Articoli)' }}</span>
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</x-agent-layout>
