<!DOCTYPE html>
<html lang="{{ ($isEn ?? false) ? 'en' : 'it' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($isEn ?? false) ? 'Order Confirmation #' . $order->numero_ordine : 'Conferma Ordine #' . $order->numero_ordine }}</title>
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,600,700" rel="stylesheet">
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
            font-family: 'Quicksand', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #000000;
        }
        .pagebreak {
            page-break-before: always;
        }
        .testoMail {
            font-family: 'Quicksand', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #000000;
        }
        td {
            padding: 5px;
            border: solid 1px #ffffff;
            vertical-align: top;
        }
        .ordine-tab {
            border-color: #ffffff;
            border-collapse: collapse;
        }
        .ordine-tab td {
            border: solid 1px #dfdfdf;
            border-collapse: collapse;
            padding: 10px 20px;
        }
        .ordine-tab .site td {
            border: 0;
        }
        a {
            color: #000000;
            text-decoration: none;
        }
    </style>
</head>
@php
    $isEn = $isEn ?? (($locale ?? app()->getLocale()) === 'en');
    $customerName = trim(($order->customer->nome ?? '') . ' ' . ($order->customer->cognome ?? ''));
    if (empty($customerName)) {
        $customerName = $order->spedizione_nome ?: 'Cliente';
    }
@endphp
<body style="margin: 0; padding: 20px 0; background-color: #FFFFFF; font-family: 'Quicksand', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #000000;">
<div class="pagebreak" style="width: 100%; max-width: 600px; margin: auto;" border="0" align="center" bgcolor="#FFFFFF" id="mail">
    <br><br>
    <center>
        <a href="https://www.bicap.shop" target="_blank">
            <img src="https://bicap.shop/images/bicap-logo-popup.png" width="200" alt="BICAP" style="width: 200px; max-width: 200px; border: 0; display: inline-block;">
        </a>
        <br><br>
        <div style="width: 100%; max-width: 600px; text-align: center; color: #000000; font-family: 'Quicksand', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
            <hr style="border: none; border-top: 1px solid #Fedb00; display: block; width: 100%; margin: 10px auto;">
            <br>
            <div style="font-size: 15px; color: #000000;">
                {{ $isEn ? 'Hello' : 'Ciao' }} &nbsp;{{ $customerName }}
            </div>
            <br>
            <h2 style="font-size: 1.5em; font-weight: bold; margin: 0 0 10px 0; color: #000000;">
                {{ $isEn ? 'Thank you for your order!' : 'Grazie per il tuo ordine!' }}
            </h2>
            <br>
            <div style="font-size: 14px; color: #000000; line-height: 22px;">
                {{ $isEn ? 'You will receive an email as soon as the courier takes delivery of your order.' : 'Riceverai una mail non appena il corriere prenderà in consegna il tuo ordine.' }}
            </div>
            <br><br>
            <hr style="border: none; border-top: 1px solid #Fedb00; display: block; width: 100%; margin: 10px auto;">
            
            <table style="text-align: left; width: 100%; max-width: 600px; line-height: 23px; border-collapse: collapse; font-family: 'Quicksand', Arial, sans-serif; font-size: 14px; color: #000000;" cellpadding="0" cellspacing="0">
                <tr id="numero ordine data" style="vertical-align: top;">
                    <td width="50%" style="padding: 5px; vertical-align: top; border: 0;">
                        <h3 style="color: #Fedb00; margin: 0 0 5px 0; font-size: 16px; font-weight: bold;">
                            {{ $isEn ? 'Order Number' : 'Ordine Numero' }}
                        </h3>
                        #{{ $order->numero_ordine }}
                    </td>
                    <td width="50%" style="padding: 5px; vertical-align: top; border: 0;">
                        <h3 style="color: #Fedb00; margin: 0 0 5px 0; font-size: 16px; font-weight: bold;">
                            {{ $isEn ? 'Order Date' : 'Data ordine' }}
                        </h3>
                        <b>{{ $order->created_at ? $order->created_at->format('d/m/Y') : date('d/m/Y') }}</b>&nbsp;&nbsp;{{ $order->created_at ? $order->created_at->format('H:i') : date('H:i') }}
                        <br><br>
                    </td>
                </tr>
                <tr style="vertical-align: top;">
                    <td width="50%" style="padding: 5px; vertical-align: top; border: 0;">
                        <h3 style="color: #Fedb00; margin: 0 0 5px 0; font-size: 16px; font-weight: bold;">
                            {{ $isEn ? 'Shipping Address' : 'Indirizzo spedizione' }}
                        </h3>
                        {{ $order->spedizione_nome }}<br>
                        {{ $order->spedizione_indirizzo }}<br>
                        {{ $order->spedizione_cap }}&nbsp;{{ $order->spedizione_citta }}@if($order->spedizione_provincia)&nbsp;({{ $order->spedizione_provincia }})@endif&nbsp;-&nbsp;{{ $order->spedizione_nazione }}<br>
                        @if(!empty($order->customer?->telefono) || !empty($order->customer?->cellulare))
                            {{ $order->customer->telefono ?? $order->customer->cellulare }}<br>
                        @endif
                        {{ $order->customer?->email ?? '' }}
                    </td>
                    <td width="50%" style="padding: 5px; vertical-align: top; border: 0;">
                        <h3 style="color: #Fedb00; margin: 0 0 5px 0; font-size: 16px; font-weight: bold;">
                            {{ $isEn ? 'Billing Address' : 'Indirizzo di fatturazione' }}
                        </h3>
                        @if(!empty($order->customer?->ragione_sociale))
                            {{ $order->customer->ragione_sociale }}<br>
                        @else
                            {{ $order->customer?->nome ?? $order->spedizione_nome }}&nbsp;{{ $order->customer?->cognome }}<br>
                        @endif
                        {{ $order->customer?->indirizzo ?? $order->spedizione_indirizzo }}<br>
                        {{ $order->customer?->cap ?? $order->spedizione_cap }}&nbsp;{{ $order->customer?->citta ?? $order->spedizione_citta }}@if($order->customer?->provincia ?? $order->spedizione_provincia)&nbsp;({{ $order->customer?->provincia ?? $order->spedizione_provincia }})@endif&nbsp;-&nbsp;{{ $order->customer?->nazione ?? $order->spedizione_nazione }}<br>
                        @if(!empty($order->customer?->partita_iva))
                            {{ $isEn ? 'VAT:' : 'P.IVA:' }} {{ $order->customer->partita_iva }}<br>
                        @endif
                        @if(!empty($order->customer?->codice_fiscale))
                            {{ $isEn ? 'Tax Code:' : 'C.F.:' }} {{ $order->customer->codice_fiscale }}<br>
                        @endif
                        @if(!empty($order->customer?->sdi))
                            SDI: {{ $order->customer->sdi }}<br>
                        @endif
                        @if(!empty($order->customer?->pec))
                            PEC: {{ $order->customer->pec }}<br>
                        @endif
                    </td>
                </tr>
            </table>

            <br>
            <hr style="border: none; border-top: 1px solid #Fedb00; display: block; width: 100%; margin: 10px auto;">
            <h3 style="color: #Fedb00; margin: 0; text-align: left; font-size: 17px; font-weight: bold;">
                {{ $isEn ? 'Your order' : 'Il tuo ordine' }}
            </h3>
            <hr style="border: none; border-top: 1px solid #Fedb00; margin: 0; display: block; width: 100%; margin: 10px auto;">

            <table style="text-align: left; width: 100%; max-width: 600px; line-height: 23px; border-collapse: collapse; font-family: 'Quicksand', Arial, sans-serif; font-size: 14px; color: #000000;" cellpadding="0" cellspacing="0">
                @foreach($order->items as $item)
                    <tr id="cosa hai ordinato" style="vertical-align: top;">
                        <td width="40%" style="vertical-align: top; padding: 12px 10px 12px 0; border: 0;">
                            @if($item->image)
                                <img src="{{ $item->image }}" width="100%" style="max-width: 220px; width: 100%; height: auto; display: block; border: 0;" alt="{{ $item->nome_prodotto }}">
                            @else
                                <div style="width: 100%; max-width: 220px; height: 130px; background-color: #f7f7f7; text-align: center; line-height: 130px; color: #aaa; font-weight: bold; border: 1px solid #eee;">
                                    BICAP
                                </div>
                            @endif
                        </td>
                        <td width="60%" style="vertical-align: top; padding: 12px 0 12px 10px; border: 0;">
                            <strong>{{ $isEn ? 'Name :' : 'Nome :' }}</strong> {{ $item->nome_prodotto }}<br>
                            @if(!empty($item->sku))
                                <strong>{{ $isEn ? 'Code :' : 'Codice :' }}</strong> {{ $item->sku }}<br>
                            @endif
                            @if(!empty($item->taglia))
                                <strong>{{ $isEn ? 'Size :' : 'Taglia :' }}</strong> {{ $item->taglia }}<br>
                            @endif
                            @if(!empty($item->colore))
                                <strong>{{ $isEn ? 'Color :' : 'Colore :' }}</strong> {{ $item->colore }}<br>
                            @endif
                            <strong>{{ $isEn ? 'Quantity :' : 'Quantità :' }}</strong> {{ $item->quantita }}<br>
                            <strong>{{ $isEn ? 'Price :' : 'Prezzo :' }}</strong> &euro; {{ number_format($item->prezzo_unitario, 2, ',', '.') }}<br>
                            <strong>{{ $isEn ? 'Total :' : 'Totale :' }}</strong> &euro; {{ number_format($item->subtotale ?: ($item->prezzo_unitario * $item->quantita), 2, ',', '.') }}
                        </td>
                    </tr>
                    @if(!$loop->last)
                        <tr>
                            <td colspan="2" style="padding: 5px 0; border: 0;">
                                <hr style="border: none; border-top: 1px dashed #dfdfdf; margin: 5px 0;">
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>

            <br>
            <hr style="border: none; border-top: 1px solid #Fedb00; width: 100%; margin: 10px auto; display: block;">

            <div style="margin: 20px 0 10px; text-align: right; color: #000000; font-family: 'Quicksand', Arial, sans-serif;">
                <div style="font-weight: normal; line-height: 25px; font-size: 15px;">
                    <strong>{{ $isEn ? 'Shipping :' : 'Spedizione :' }}</strong> 
                    @if($order->costo_spedizione > 0)
                        &euro; {{ number_format($order->costo_spedizione, 2, ',', '.') }}
                    @else
                        {{ $isEn ? 'Free' : 'Gratis' }}
                    @endif
                    <br>
                    <strong>{{ $isEn ? 'Items total :' : 'Totale articoli :' }}</strong> &euro; {{ number_format($order->totale_imponibile, 2, ',', '.') }}<br>
                    @if($order->metodo_pagamento === 'contrassegno')
                        <strong>{{ $isEn ? 'Cash on delivery :' : 'Contrassegno :' }}</strong> &euro; {{ number_format($order->costo_contrassegno > 0 ? $order->costo_contrassegno : 10, 2, ',', '.') }}<br>
                    @endif
                </div>

                <h2 style="text-align: right; font-size: 1.5em; font-weight: bold; margin: 15px 0 10px 0; color: #000000;">
                    <br>
                    <strong>{{ $isEn ? 'Total VAT included :' : 'Totale iva inclusa :' }}</strong> &euro; {{ number_format($order->totale_ordine, 2, ',', '.') }}
                </h2>
                
                <br>
                <div style="text-align: right; line-height: 22px; font-size: 14px;">
                    <strong>{{ $isEn ? 'Payment method' : 'Modalità di pagamento' }}</strong>&nbsp;
                    @if($order->metodo_pagamento === 'contrassegno')
                        {{ $isEn ? 'Cash on Delivery' : 'Contrassegno' }}<br><br>
                        <span style="color: #333333;">
                            {{ $isEn ? 'Added € 10.00 in addition to shipping costs' : 'Aggiunti € 10,00 oltre alle spese di spedizione' }}
                        </span>
                        <br><br>
                    @elseif($order->metodo_pagamento === 'bonifico')
                        {{ $isEn ? 'Bank Transfer' : 'Bonifico Bancario' }}<br><br>
                    @elseif($order->metodo_pagamento === 'stripe')
                        {{ $isEn ? 'Credit Card' : 'Carta di Credito' }}<br><br>
                    @elseif($order->metodo_pagamento === 'paypal')
                        PayPal<br><br>
                    @else
                        {{ ucfirst($order->metodo_pagamento) }}<br><br>
                    @endif
                </div>
            </div>

            @if($order->metodo_pagamento === 'bonifico')
                @php
                    $bonificoIntestazione = \App\Models\Setting::where('key', 'bonifico_intestazione')->value('value');
                    $bonificoBanca = \App\Models\Setting::where('key', 'bonifico_banca')->value('value');
                    $bonificoIban = \App\Models\Setting::where('key', 'bonifico_iban')->value('value');
                @endphp
                <div style="text-align: left; margin: 25px 0 20px 0; padding: 18px; background-color: #fffdf0; border: 1px solid #Fedb00; border-radius: 6px; font-size: 14px; line-height: 22px;">
                    <h3 style="margin: 0 0 10px 0; color: #000000; font-size: 16px; font-weight: bold;">
                        {{ $isEn ? 'Bank Details for Payment' : 'Coordinate Bancarie per il Pagamento' }}
                    </h3>
                    <p style="margin: 0 0 12px 0; color: #333333;">
                        {{ $isEn ? 'To complete your order, please make a bank transfer using the following coordinates:' : 'Per completare il tuo ordine, effettua un bonifico bancario utilizzando le coordinate seguenti:' }}<br>
                        <strong>{{ $isEn ? 'Reason / Reference:' : 'Causale:' }}</strong> {{ $isEn ? 'Order Number #' : 'Numero Ordine #' }}{{ $order->numero_ordine }}
                    </p>
                    <table style="width: 100%; font-size: 14px; line-height: 22px; border-collapse: collapse;" cellpadding="0" cellspacing="0">
                        @if($bonificoIntestazione)
                            <tr>
                                <td style="padding: 4px 0; width: 130px; color: #555555; border: 0;">{{ $isEn ? 'Beneficiary:' : 'Intestato a:' }}</td>
                                <td style="padding: 4px 0; font-weight: bold; border: 0;">{{ $bonificoIntestazione }}</td>
                            </tr>
                        @endif
                        @if($bonificoBanca)
                            <tr>
                                <td style="padding: 4px 0; color: #555555; border: 0;">{{ $isEn ? 'Bank:' : 'Banca:' }}</td>
                                <td style="padding: 4px 0; font-weight: bold; border: 0;">{{ $bonificoBanca }}</td>
                            </tr>
                        @endif
                        @if($bonificoIban)
                            <tr>
                                <td style="padding: 4px 0; color: #555555; border: 0;">IBAN:</td>
                                <td style="padding: 4px 0; font-weight: bold; font-family: monospace; font-size: 15px; border: 0;">{{ $bonificoIban }}</td>
                            </tr>
                        @endif
                    </table>
                    <p style="margin: 12px 0 0 0; font-size: 12px; color: #666666; font-style: italic;">
                        {{ $isEn ? 'Your order will be processed once payment is received.' : "L'ordine verrà elaborato una volta ricevuto l'accredito." }}
                    </p>
                </div>
            @endif

            @if(!empty($order->note_cliente))
                <div style="text-align: left; margin: 20px 0; padding: 12px 15px; background-color: #fbfbfb; border-left: 3px solid #Fedb00; font-size: 13px; line-height: 20px;">
                    <strong>{{ $isEn ? 'Order Notes:' : "Note dell'ordine:" }}</strong><br>
                    {{ $order->note_cliente }}
                </div>
            @endif

            <br><br>
            <div style="font-size: 15px; color: #000000;">
                {{ $isEn ? 'Thank you for your order from' : 'Grazie per il tuo ordine da' }} <strong>BICAP</strong>
            </div>
            <br><br>
            <hr style="border: none; border-top: 1px solid #Fedb00; display: block; width: 100%; margin: 10px auto;">
        </div>

        <em style="font-size: 11px; color: #666666; display: block; margin: 15px 0 10px 0;">
            {{ $isEn ? 'You receive this email because you gave consent to receive our communications.' : 'Ricevi questa email perché hai dato il consenso a ricevere le nostre comunicazioni.' }}
        </em>

        <div style="margin: 10px 0;">
            <a href="https://www.linkedin.com/company/bicap-calzaturificio-5-bi/" target="_blank" style="display: inline-block; margin: 5px 8px;">
                <img src="https://bicap.shop/images/link.jpg" style="width: 20px; height: 20px; border: 0;" alt="LinkedIn">
            </a>
            <a href="https://www.facebook.com/bicap.absolutelysafe" target="_blank" style="display: inline-block; margin: 5px 8px;">
                <img src="https://bicap.shop/images/fb.jpg" style="width: 19px; height: 19px; border: 0;" alt="Facebook">
            </a>
            <a href="https://www.instagram.com/bicap.absolutelysafe/" target="_blank" style="display: inline-block; margin: 5px 8px;">
                <img src="https://bicap.shop/images/insta.jpg" style="width: 18px; height: 18px; border: 0;" alt="Instagram">
            </a>
            <a href="https://www.youtube.com/channel/UCzRKy7yaDRhdAnnEhoA-T-g" target="_blank" style="display: inline-block; margin: 5px 8px;">
                <img src="https://bicap.shop/images/you.jpg" style="width: 22px; height: 22px; border: 0;" alt="YouTube">
            </a>
        </div>
        <br><br>
    </center>
</div>
<span class="pagebreak"> </span>
</body>
</html>
