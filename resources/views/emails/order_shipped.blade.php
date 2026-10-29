<!DOCTYPE html>
<html lang="{{ ($isEn ?? false) ? 'en' : 'it' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($isEn ?? false) ? 'Order Shipped #' . $order->numero_ordine : 'Ordine Spedito #' . $order->numero_ordine }}</title>
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
        td {
            padding: 5px;
            vertical-align: top;
        }
        a {
            color: #000000;
            text-decoration: none;
        }
        .btn-tracking {
            display: inline-block;
            background-color: #FEDB00;
            color: #000000 !important;
            font-weight: 700;
            font-size: 15px;
            padding: 12px 28px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none !important;
            margin-top: 10px;
        }
    </style>
</head>
@php
    $isEn = $isEn ?? (($locale ?? app()->getLocale()) === 'en');
    $customerName = trim(($order->customer->nome ?? '') . ' ' . ($order->customer->cognome ?? ''));
    if (empty($customerName)) {
        $customerName = $order->spedizione_nome ?: ($isEn ? 'Customer' : 'Cliente');
    }
    $trackingLink = $trackingUrl ?? $order->effective_tracking_url;
@endphp
<body style="margin: 0; padding: 20px 0; background-color: #FFFFFF; font-family: 'Quicksand', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #000000;">
<div style="width: 100%; max-width: 600px; margin: auto;" border="0" align="center" bgcolor="#FFFFFF" id="mail">
    <center>
        <a href="https://www.bicap.shop" target="_blank">
            <img src="https://bicap.shop/images/bicap-logo-popup.png" width="200" alt="BICAP" style="width: 200px; max-width: 200px; border: 0; display: inline-block;">
        </a>
        <br><br>
        <div style="width: 100%; max-width: 600px; text-align: center; color: #000000; font-family: 'Quicksand', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
            <hr style="border: none; border-top: 1px solid #Fedb00; display: block; width: 100%; margin: 10px auto;">
            <br>
            <div style="font-size: 15px; color: #000000;">
                {{ $isEn ? 'Hello' : 'Ciao' }} &nbsp;<strong>{{ $customerName }}</strong>,
            </div>
            <br>
            <h2 style="font-size: 1.6em; font-weight: bold; margin: 0 0 10px 0; color: #000000; text-transform: uppercase;">
                {{ $isEn ? 'Your order is on its way!' : 'Il tuo ordine è in viaggio!' }}
            </h2>
            <div style="font-size: 14px; color: #444444; line-height: 22px; max-width: 520px; margin: 0 auto;">
                {{ $isEn 
                    ? 'Great news! We have shipped your order #' . $order->numero_ordine . '. It has been handed over to our courier and will be delivered to your address soon.' 
                    : 'Ottime notizie! Abbiamo spedito il tuo ordine #' . $order->numero_ordine . '. È stato affidato al corriere espresso ed è in fase di consegna verso il tuo indirizzo.' 
                }}
            </div>
            <br>

            <!-- BOX TRACKING SPEDIZIONE -->
            <div style="background-color: #FFFDF0; border: 2px solid #FEDB00; border-radius: 8px; padding: 22px; margin: 15px 0 25px 0; text-align: center;">
                <span style="font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #777;">
                    {{ $isEn ? 'Shipment Tracking Details' : 'Dettagli di Spedizione & Tracciamento' }}
                </span>
                <br><br>

                @if(!empty($order->corriere))
                    <div style="font-size: 14px; color: #000000; margin-bottom: 6px;">
                        <strong>{{ $isEn ? 'Courier:' : 'Corriere:' }}</strong> {{ $order->corriere }}
                    </div>
                @endif

                @if(!empty($order->tracking_number))
                    <div style="font-size: 15px; color: #000000; margin-bottom: 12px;">
                        <strong>{{ $isEn ? 'Tracking Number:' : 'Codice di Tracciamento:' }}</strong>
                        <span style="font-family: monospace; background: #f3f4f6; padding: 4px 10px; border-radius: 4px; font-size: 16px; font-weight: bold; color: #000000; letter-spacing: 1px;">
                            {{ $order->tracking_number }}
                        </span>
                    </div>
                @endif

                @if(!empty($trackingLink))
                    <div style="margin-top: 15px;">
                        <a href="{{ $trackingLink }}" target="_blank" class="btn-tracking" style="display: inline-block; background-color: #FEDB00; color: #000000 !important; font-weight: 700; font-size: 14px; padding: 12px 28px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; text-decoration: none !important;">
                            {{ $isEn ? 'Track Your Shipment ↗' : 'Traccia la tua Spedizione ↗' }}
                        </a>
                    </div>
                    <div style="font-size: 11px; color: #888888; margin-top: 10px;">
                        {{ $isEn 
                            ? 'Please note: it may take a few hours for the courier tracking system to update.' 
                            : 'Nota: potrebbero essere necessarie alcune ore affinché il sistema del corriere aggiorni lo stato.' 
                        }}
                    </div>
                @elseif(empty($order->tracking_number) && empty($order->corriere))
                    <div style="font-size: 13px; color: #555555;">
                        {{ $isEn 
                            ? 'Your package has been dispatched and is currently in transit.' 
                            : 'Il tuo pacco è stato regolarmente spedito ed è in transito.' 
                        }}
                    </div>
                @endif
            </div>

            <hr style="border: none; border-top: 1px solid #Fedb00; display: block; width: 100%; margin: 15px auto;">
            
            <!-- DATI ORDINE & INDIRIZZO -->
            <table style="text-align: left; width: 100%; max-width: 600px; line-height: 23px; border-collapse: collapse; font-family: 'Quicksand', Arial, sans-serif; font-size: 14px; color: #000000;" cellpadding="0" cellspacing="0">
                <tr style="vertical-align: top;">
                    <td width="50%" style="padding: 5px; vertical-align: top; border: 0;">
                        <h3 style="color: #Fedb00; margin: 0 0 5px 0; font-size: 15px; font-weight: bold; text-transform: uppercase;">
                            {{ $isEn ? 'Order Reference' : 'Riferimento Ordine' }}
                        </h3>
                        <b>#{{ $order->numero_ordine }}</b><br>
                        {{ $isEn ? 'Date:' : 'Data:' }} {{ $order->created_at ? $order->created_at->format('d/m/Y') : date('d/m/Y') }}<br>
                        {{ $isEn ? 'Payment:' : 'Pagamento:' }} {{ strtoupper($order->metodo_pagamento) }}
                    </td>
                    <td width="50%" style="padding: 5px; vertical-align: top; border: 0;">
                        <h3 style="color: #Fedb00; margin: 0 0 5px 0; font-size: 15px; font-weight: bold; text-transform: uppercase;">
                            {{ $isEn ? 'Delivery Address' : 'Indirizzo di Consegna' }}
                        </h3>
                        <strong>{{ $order->spedizione_nome }}</strong><br>
                        {{ $order->spedizione_indirizzo }}<br>
                        {{ $order->spedizione_cap }}&nbsp;{{ $order->spedizione_citta }}@if($order->spedizione_provincia)&nbsp;({{ $order->spedizione_provincia }})@endif<br>
                        <b>{{ $order->spedizione_nazione }}</b>
                    </td>
                </tr>
            </table>

            <br>
            <hr style="border: none; border-top: 1px solid #Fedb00; display: block; width: 100%; margin: 10px auto;">
            <h3 style="color: #Fedb00; margin: 0 0 10px 0; text-align: left; font-size: 16px; font-weight: bold; text-transform: uppercase;">
                {{ $isEn ? 'Shipped Items' : 'Articoli Spediti' }}
            </h3>

            <!-- TABELLA ARTICOLI -->
            <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse: collapse; text-align: left; font-size: 13px; border: 1px solid #dfdfdf;">
                <thead>
                    <tr style="background-color: #f7f7f7; border-bottom: 2px solid #FEDB00;">
                        <th style="padding: 8px 10px; font-weight: bold;">{{ $isEn ? 'Product' : 'Prodotto' }}</th>
                        <th style="padding: 8px 10px; font-weight: bold; text-align: center;">{{ $isEn ? 'Size' : 'Taglia' }}</th>
                        <th style="padding: 8px 10px; font-weight: bold; text-align: center;">{{ $isEn ? 'Qty' : 'Q.tà' }}</th>
                        <th style="padding: 8px 10px; font-weight: bold; text-align: right;">{{ $isEn ? 'Total' : 'Totale' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr style="border-bottom: 1px solid #efefef;">
                            <td style="padding: 8px 10px;">
                                <strong>{{ $item->nome_prodotto }}</strong>
                                @if(!empty($item->sku))
                                    <div style="font-size: 11px; color: #888;">SKU: {{ $item->sku }}</div>
                                @endif
                            </td>
                            <td style="padding: 8px 10px; text-align: center;">
                                {{ $item->taglia ?: '-' }}
                            </td>
                            <td style="padding: 8px 10px; text-align: center; font-weight: bold;">
                                {{ $item->quantita }}
                            </td>
                            <td style="padding: 8px 10px; text-align: right; font-weight: bold;">
                                € {{ number_format($item->subtotale, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #fafafa; font-weight: bold;">
                        <td colspan="3" style="padding: 8px 10px; text-align: right;">
                            {{ $isEn ? 'Total Order Amount:' : 'Totale Ordine:' }}
                        </td>
                        <td style="padding: 8px 10px; text-align: right; color: #000000; font-size: 14px;">
                            € {{ number_format($order->totale_ordine, 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            <br>
            <hr style="border: none; border-top: 1px solid #Fedb00; display: block; width: 100%; margin: 20px auto 15px auto;">

            <!-- ASSISTENZA CLIENTI -->
            <div style="background-color: #000000; color: #ffffff; padding: 18px 20px; border-radius: 6px; text-align: left; margin: 15px 0;">
                <div style="font-size: 13px; font-weight: bold; color: #FEDB00; text-transform: uppercase; margin-bottom: 6px;">
                    {{ $isEn ? 'Need Assistance?' : 'Hai bisogno di Assistenza?' }}
                </div>
                <div style="font-size: 12px; color: #cccccc; line-height: 19px;">
                    {{ $isEn 
                        ? 'For any questions about your delivery, feel free to reply to this email or reach us on WhatsApp at ' 
                        : 'Per qualsiasi informazione sulla consegna puoi rispondere a questa email o contattarci su WhatsApp al ' 
                    }}
                    <a href="https://wa.me/393518581006" target="_blank" style="color: #FEDB00 !important; font-weight: bold; text-decoration: underline;">+39 351 858 1006</a>.
                </div>
            </div>

            <div style="font-size: 11px; color: #999999; margin-top: 25px; line-height: 16px;">
                CALZATURIFICIO 5BI S.R.L. - Via Trani 94-100, 76121 Barletta (BT) Italy<br>
                {{ $isEn ? 'BICAP - Absolutely Safe • Official Online Store' : 'BICAP - Absolutely Safe • Negozio Ufficiale Online' }}
            </div>
        </div>
    </center>
</div>
</body>
</html>

