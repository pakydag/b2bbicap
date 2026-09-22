<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Riepilogo Ordine B2B' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background-color: #f1f5f9; }
        .wrapper { max-width: 650px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { background: #000000; color: #ffffff; padding: 28px 24px; text-align: center; border-bottom: 4px solid #facc15; }
        .header img { max-height: 48px; max-width: 220px; width: auto; object-fit: contain; margin: 0 auto; display: block; }
        .header p { margin: 12px 0 0; font-size: 12px; color: #cbd5e1; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; }
        .content { padding: 32px 28px; }
        .greeting { font-size: 16px; font-weight: 700; color: #000000; margin-bottom: 8px; }
        .text-lead { font-size: 14px; color: #475569; margin-bottom: 20px; }
        
        .order-banner { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 24px; }
        .order-banner-grid { width: 100%; border-collapse: collapse; }
        .order-banner-grid td { padding: 6px 10px; font-size: 12px; vertical-align: top; }
        .order-banner-label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 800; }
        .order-banner-val { font-weight: 800; color: #0f172a; }
        
        .badge-ref { display: inline-block; background: #fef08a; color: #713f12; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; font-family: monospace; border: 1px solid #fde047; }
        .badge-status { display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
        .status-confirmed { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .status-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .status-cancelled { background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }

        .card-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .card-box h3 { margin: 0 0 12px; font-size: 13px; font-weight: 800; text-transform: uppercase; color: #000000; letter-spacing: 0.5px; }

        .table-wrap { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 12px; }
        .table-wrap th { background: #f1f5f9; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 800; border-bottom: 1px solid #cbd5e1; }
        .table-wrap td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .table-wrap tr:last-child td { border-bottom: none; }
        .total-box { background: #f8fafc; border-top: 2px solid #e2e8f0; padding: 14px 12px; font-weight: 800; font-size: 15px; color: #0f172a; text-align: right; }

        .btn-container { text-align: center; margin: 24px 0; }
        .btn { display: inline-block; padding: 14px 28px; background: #000000; color: #ffffff !important; text-decoration: none; border-radius: 10px; font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 0.8px; }

        .payment-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 18px; margin-top: 24px; }
        .payment-box h4 { margin: 0 0 10px; font-size: 13px; font-weight: 800; text-transform: uppercase; color: #1e3a8a; }
        .bonifico-grid { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 8px; }
        .bonifico-grid td { padding: 4px 8px; }

        .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        @php
            $logoLocalPath = public_path('storage/logo-bicap.png');
            if (!file_exists($logoLocalPath)) {
                $logoLocalPath = storage_path('app/public/logo-bicap.png');
            }
            $hasLocalLogo = file_exists($logoLocalPath);
            $agent = $order->agent ?: ($order->customer && $order->customer->agents ? $order->customer->agents->first() : null);
            $isAgentRecipient = str_contains($title ?? '', 'Cliente') || str_contains($title ?? '', 'Agente') || str_contains($title ?? '', 'Registrato');
        @endphp
        <div class="header">
            @if($hasLocalLogo && isset($message))
                <img src="{{ $message->embed($logoLocalPath) }}" alt="BICAP">
            @elseif($hasLocalLogo)
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoLocalPath)) }}" alt="BICAP">
            @endif
            <p>{{ $title ?? 'Riepilogo Ordine B2B' }}</p>
        </div>
        
        <div class="content">
            @if($isAgentRecipient && $agent)
                <p class="greeting">Gentile <strong>{{ $agent->name }} {{ $agent->surname }}</strong>,</p>
                <p class="text-lead">
                    Di seguito trovi i dettagli dell'aggiornamento per l'<strong>Ordine B2B #{{ $order->id }}</strong> del cliente <strong>{{ $order->customer->business_name ?? 'Cliente' }}</strong> su <strong>{{ $companyName }}</strong>.
                </p>
            @else
                <p class="greeting">Gentile <strong>{{ $order->customer->business_name ?? 'Cliente' }}</strong>,</p>
                <p class="text-lead">
                    Di seguito trovi il riepilogo dettagliato del tuo <strong>Ordine B2B #{{ $order->id }}</strong> trasmesso sul portale <strong>{{ $companyName }}</strong>.
                </p>
            @endif

            <!-- Box Avviso Stato e Conferma Agente -->
            @if($order->status === 'confirmed')
                <div style="margin-bottom: 22px; padding: 16px 18px; background-color: #f0fdf4; border: 1px solid #bbf7d0; border-left: 5px solid #16a34a; border-radius: 10px;">
                    <h4 style="margin: 0 0 6px; font-size: 13px; font-weight: 800; color: #166534; text-transform: uppercase; letter-spacing: 0.5px;">
                        ✓ Ordine Confermato Definitivamente
                    </h4>
                    <p style="margin: 0; font-size: 12.5px; color: #14532d; line-height: 1.55;">
                        Il tuo ordine è stato <strong>verificato e confermato dall'agente commerciale / amministrazione</strong> ed è attualmente in lavorazione per la preparazione ed evasione.
                    </p>
                </div>
            @elseif($order->status === 'revision_pending')
                <div style="margin-bottom: 22px; padding: 16px 18px; background-color: #fffbeb; border: 1px solid #fde68a; border-left: 5px solid #d97706; border-radius: 10px;">
                    <h4 style="margin: 0 0 6px; font-size: 13px; font-weight: 800; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px;">
                        ✏️ Rettifica Ordine dall'Agente — Richiesta Approvazione
                    </h4>
                    <p style="margin: 0; font-size: 12.5px; color: #78350f; line-height: 1.55;">
                        L'agente commerciale ha apportato alcune modifiche/rettifiche (quantità, disponibilità o prezzi) a questo ordine. Ti invitiamo ad accedere al Portale B2B per visualizzare ed approvare le modifiche.
                    </p>
                </div>
            @elseif($order->status === 'customer_approved')
                <div style="margin-bottom: 22px; padding: 16px 18px; background-color: #eff6ff; border: 1px solid #bfdbfe; border-left: 5px solid #2563eb; border-radius: 10px;">
                    <h4 style="margin: 0 0 6px; font-size: 13px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">
                        ✓ Modifiche Ordine Accettate dal Cliente
                    </h4>
                    <p style="margin: 0; font-size: 12.5px; color: #1e3a8a; line-height: 1.55;">
                        Le modifiche all'ordine sono state confermate ed accettate dal cliente. L'ordine è ora in attesa dell'approvazione finale di conferma da parte dell'agente commerciale / amministrazione per l'inoltro alla sede.
                    </p>
                </div>
            @elseif($order->status === 'customer_rejected')
                <div style="margin-bottom: 22px; padding: 16px 18px; background-color: #fef2f2; border: 1px solid #fecaca; border-left: 5px solid #dc2626; border-radius: 10px;">
                    <h4 style="margin: 0 0 6px; font-size: 13px; font-weight: 800; color: #991b1b; text-transform: uppercase; letter-spacing: 0.5px;">
                        ✕ Modifiche Rifiutate dal Cliente
                    </h4>
                    <p style="margin: 0; font-size: 12.5px; color: #7f1d1d; line-height: 1.55;">
                        Le modifiche all'ordine sono state rifiutate dal cliente. L'agente commerciale provvederà a verificare la richiesta e a ricontattare il cliente.
                    </p>
                </div>
            @elseif($order->status === 'cancelled')
                <div style="margin-bottom: 22px; padding: 16px 18px; background-color: #fef2f2; border: 1px solid #fecaca; border-left: 5px solid #dc2626; border-radius: 10px;">
                    <h4 style="margin: 0 0 6px; font-size: 13px; font-weight: 800; color: #991b1b; text-transform: uppercase; letter-spacing: 0.5px;">
                        ✕ Ordine Annullato
                    </h4>
                    <p style="margin: 0; font-size: 12.5px; color: #7f1d1d; line-height: 1.55;">
                        Questo ordine è stato annullato. Per ulteriori informazioni puoi fare riferimento al tuo agente commerciale.
                    </p>
                </div>
            @else
                <div style="margin-bottom: 22px; padding: 16px 18px; background-color: #fffdf0; border: 1px solid #fef08a; border-left: 5px solid #eab308; border-radius: 10px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="vertical-align: top; width: 28px; font-size: 20px; line-height: 1; padding-right: 10px;">
                                ⚠️
                            </td>
                            <td style="vertical-align: top;">
                                <h4 style="margin: 0 0 6px; font-size: 13px; font-weight: 800; color: #854d0e; text-transform: uppercase; letter-spacing: 0.5px;">
                                    In Attesa di Conferma e Rettifica dall'Agente
                                </h4>
                                <p style="margin: 0 0 6px; font-size: 12.5px; color: #713f12; line-height: 1.55;">
                                    La presente email costituisce una <strong>copia riepilogativa della richiesta d'ordine</strong> trasmessa.
                                </p>
                                <p style="margin: 0; font-size: 12.5px; color: #713f12; line-height: 1.55;">
                                    Ti ricordiamo che l'ordine <strong>deve essere confermato dal tuo agente commerciale di riferimento</strong> ed è attualmente <strong>in attesa di eventuale modifica o rettifica</strong> (in base alle disponibilità di magazzino, tempi di produzione o condizioni concordate). Riceverai la notifica di conferma definitiva non appena l'ordine sarà validato.
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            @endif

            <!-- Banner Dati Principali Ordine -->
            <div class="order-banner">
                <table class="order-banner-grid">
                    <tr>
                        <td style="width: 50%;">
                            <span class="order-banner-label">Numero Ordine:</span><br>
                            <span class="order-banner-val" style="font-size: 15px; color: #000;">#{{ $order->id }}</span>
                        </td>
                        <td style="width: 50%;">
                            <span class="order-banner-label">Data e Ora:</span><br>
                            <span class="order-banner-val">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="order-banner-label">Riferimento Ordine Interno:</span><br>
                            @if($order->internal_reference)
                                <span class="badge-ref">{{ $order->internal_reference }}</span>
                            @else
                                <span style="color: #94a3b8; font-style: italic; font-size: 11px;">Non specificato</span>
                            @endif
                        </td>
                        <td>
                            <span class="order-banner-label">Stato Ordine:</span><br>
                            @if($order->status === 'confirmed')
                                <span class="badge-status status-confirmed">✓ Confermato</span>
                            @elseif($order->status === 'cancelled')
                                <span class="badge-status status-cancelled">✕ Annullato</span>
                            @elseif($order->status === 'revision_pending')
                                <span class="badge-status status-pending" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a;">⏳ In Attesa Approvazione Modifiche</span>
                            @elseif($order->status === 'customer_approved')
                                <span class="badge-status status-confirmed" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe;">✓ Modifiche Accettate</span>
                            @elseif($order->status === 'customer_rejected')
                                <span class="badge-status status-cancelled">✕ Modifiche Rifiutate</span>
                            @else
                                <span class="badge-status status-pending">⏳ In Attesa di Conferma Agente</span>
                            @endif
                        </td>
                    </tr>
                    @if($order->customer)
                        <tr>
                            <td>
                                <span class="order-banner-label">Cliente / Ragione Sociale:</span><br>
                                <span class="order-banner-val">{{ $order->customer->business_name }}</span>
                            </td>
                            <td>
                                <span class="order-banner-label">Partita IVA / CF:</span><br>
                                <span class="order-banner-val" style="font-family: monospace;">{{ $order->customer->vat_number ?? 'N.D.' }}</span>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>

            <!-- Box Agente di Riferimento -->
            @php
                $agent = $order->agent ?: ($order->customer && $order->customer->agents ? $order->customer->agents->first() : null);
            @endphp
            @if($agent)
                <div class="card-box" style="border-left: 4px solid #000000; background: #fffdf5; border-color: #fef08a;">
                    <h3 style="color: #000000; margin-bottom: 8px;">👤 Agente Commerciale di Riferimento</h3>
                    <div style="background: #ffffff; padding: 12px 14px; border-radius: 8px; border: 1px solid #fde047;">
                        <p style="margin: 0 0 4px; font-size: 14px; font-weight: 800; color: #000000;">
                            {{ $agent->name }} {{ $agent->surname }}
                        </p>
                        <p style="margin: 2px 0; font-size: 12px; color: #475569;">
                            <strong>Email:</strong> <a href="mailto:{{ $agent->email }}" style="color: #2563eb; text-decoration: none; font-weight: bold;">{{ $agent->email }}</a>
                        </p>
                        @if($agent->phone)
                            <p style="margin: 2px 0; font-size: 12px; color: #475569;">
                                <strong>Telefono:</strong> <a href="tel:{{ $agent->phone }}" style="color: #1e293b; text-decoration: none; font-weight: bold;">{{ $agent->phone }}</a>
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Tabella Prodotti Ordinati -->
            <div class="card-box">
                <h3>📦 Articoli Ordinati</h3>
                <table class="table-wrap">
                    <thead>
                        <tr>
                            <th>Prodotto & Variante</th>
                            <th style="text-align: center;">Consegna</th>
                            <th style="text-align: center;">Qtà</th>
                            <th style="text-align: right;">Prezzo</th>
                            <th style="text-align: right;">Totale</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            @php
                                $qtyChanged = $item->original_quantity && $item->original_quantity != $item->quantity;
                                $priceChanged = $item->original_price && abs($item->original_price - $item->price) >= 0.01;
                                $isItemModified = $item->is_modified || $qtyChanged || $priceChanged;
                            @endphp
                            <tr style="{{ $isItemModified ? 'background-color: #fffdf0;' : '' }}">
                                <td>
                                    <strong>{{ $item->product->name ?? 'Articolo' }}</strong>
                                    @if($item->product && $item->product->brand)
                                        <span style="font-size: 10px; text-transform: uppercase; color: #64748b;">({{ $item->product->brand->name }})</span>
                                    @endif
                                    @if($isItemModified)
                                        <span style="display: inline-block; background: #fef08a; color: #854d0e; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; margin-left: 4px; border: 1px solid #fde047;">Modificato</span>
                                    @endif
                                    <br>
                                    <small style="color: #64748b;">
                                        Taglia: <strong>{{ $item->variant->size ?? 'N/D' }}</strong>
                                        @if($item->variant && $item->variant->color)
                                            • Colore: {{ $item->variant->color }}
                                        @endif
                                    </small>
                                </td>
                                <td style="text-align: center; font-size: 11px; white-space: nowrap;">
                                    @if(empty($item->delivery_date) || $item->delivery_date === 'immediate')
                                        <span style="color: #166534; font-weight: bold;">⚡ Immediata</span>
                                    @else
                                        @php
                                            $delivDateStr = $item->delivery_date;
                                            if (str_contains($delivDateStr, '-')) {
                                                try {
                                                    $delivDateStr = \Carbon\Carbon::parse($delivDateStr)->format('d/m/Y');
                                                } catch (\Throwable $e) {}
                                            }
                                        @endphp
                                        <span style="color: #1e40af; font-weight: bold;">📅 {{ $delivDateStr }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center; font-weight: bold; font-size: 13px;">
                                    {{ $item->quantity }}
                                    @if($qtyChanged)
                                        <br><span style="font-size: 10px; color: #b45309; text-decoration: line-through; font-weight: normal;">(Iniz: {{ $item->original_quantity }})</span>
                                    @endif
                                </td>
                                <td style="text-align: right; font-family: monospace;">
                                    € {{ number_format($item->price, 2, ',', '.') }}
                                    @if($priceChanged)
                                        <br><span style="font-size: 10px; color: #b45309; text-decoration: line-through; font-weight: normal;">(Iniz: € {{ number_format($item->original_price, 2, ',', '.') }})</span>
                                    @endif
                                </td>
                                <td style="text-align: right; font-weight: bold; font-family: monospace;">€ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="total-box">
                    TOTALE ORDINE: <span style="color: #000000; font-size: 18px; margin-left: 8px;">€ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                </div>
            </div>

            <!-- Modalità di Pagamento -->
            @if(!empty($paymentMethod) && $paymentMethod !== 'none')
                <div class="payment-box">
                    @if($paymentMethod === 'stripe')
                        <h4>💳 Pagamento con Carta di Credito (Stripe)</h4>
                        <p style="margin: 0 0 12px; font-size: 13px; color: #1e3a8a;">Puoi completare il saldo dell'ordine in sicurezza tramite carta:</p>
                        <div style="text-align: center;">
                            <a href="{{ $paymentLink }}" class="btn" style="background: #2563eb; color: #ffffff !important;">Paga Ora con Stripe →</a>
                        </div>
                    @elseif($paymentMethod === 'paypal')
                        <h4>🅿️ Pagamento tramite PayPal</h4>
                        <p style="margin: 0 0 12px; font-size: 13px; color: #1e3a8a;">Puoi completare il pagamento tramite il tuo account PayPal:</p>
                        <div style="text-align: center;">
                            <a href="{{ $paymentLink }}" class="btn" style="background: #0284c7; color: #ffffff !important;">Paga Ora con PayPal →</a>
                        </div>
                    @elseif($paymentMethod === 'bonifico')
                        <h4>🏦 Coordinate Bancarie per Bonifico</h4>
                        <p style="margin: 0 0 10px; font-size: 12px; color: #1e3a8a;">
                            Effettua il bonifico indicando la causale riportata di seguito:
                        </p>
                        @php $settings = \App\Models\Setting::all()->pluck('value', 'key'); @endphp
                        <table class="bonifico-grid">
                            <tr>
                                <td style="width: 30%; font-weight: bold; color: #475569;">Intestato a:</td>
                                <td style="font-weight: bold; color: #0f172a;">{{ $settings['bonifico_intestazione'] ?? 'Cedma Srl' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; color: #475569;">Banca:</td>
                                <td style="font-weight: bold; color: #0f172a;">{{ $settings['bonifico_banca'] ?? 'N.D.' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; color: #475569;">IBAN:</td>
                                <td style="font-weight: bold; font-family: monospace; font-size: 14px; color: #000;">{{ $settings['bonifico_iban'] ?? 'N.D.' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; color: #475569;">Causale:</td>
                                <td style="font-weight: bold; color: #0f172a;">Saldo Ordine B2B #{{ $order->id }}{{ $order->internal_reference ? ' - Rif: ' . $order->internal_reference : '' }}</td>
                            </tr>
                        </table>
                    @endif
                </div>
            @endif

            <!-- Note Ordine -->
            @if($order->notes)
                <div class="card-box" style="margin-top: 20px; background: #fffbeb; border-color: #fde68a;">
                    <h3 style="color: #92400e; margin-bottom: 6px;">📝 Note dell'Ordine</h3>
                    <p style="margin: 0; font-size: 13px; color: #78350f; font-style: italic;">
                        {{ $order->notes }}
                    </p>
                </div>
            @endif

            <div class="btn-container">
                @if($order->status === 'revision_pending')
                    <a href="{{ $portalUrl ?? route('login') }}" class="btn" style="background: #d97706; color:#ffffff !important;">
                        🔍 Visualizza e Approva Rettifica Ordine →
                    </a>
                @elseif($order->status === 'customer_approved')
                    <a href="{{ $portalUrl ?? route('login') }}" class="btn" style="background: #2563eb; color:#ffffff !important;">
                        🚀 Accedi al Portale B2B per Conferma Ordine →
                    </a>
                @else
                    <a href="{{ $portalUrl ?? route('login') }}" class="btn" style="color:#ffffff;">Accedi al Portale B2B →</a>
                @endif
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0 0 6px; font-size: 11px; color: #94a3b8;">
                Per qualsiasi informazione o modifica relativa a questo ordine, contatta direttamente il tuo agente commerciale di riferimento indicato sopra.
            </p>
            &copy; Cedma srl - Tutti i diritti riservati.
        </div>
    </div>
</body>
</html>
