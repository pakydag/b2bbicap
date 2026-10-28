<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ricezione Ordine #{{ $order->numero_ordine }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-w-xl mx-auto p-4 border rounded shadow-sm">
        <h2 style="color: #4F46E5;">Conferma Ricezione Ordine</h2>
        <p>Gentile <strong>{{ $order->customer->nome }} {{ $order->customer->cognome }}</strong>,</p>
        <p>Ti confermiamo di aver ricevuto correttamente il tuo ordine <strong>#{{ $order->numero_ordine }}</strong> del {{ $order->created_at->format('d/m/Y H:i') }}.</p>
        
        <h3 style="border-bottom: 2px solid #eee; padding-bottom: 8px;">Dettaglio Ordine</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f9f9f9;">
                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #ddd;">Prodotto</th>
                    <th style="padding: 10px; text-align: center; border-bottom: 1px solid #ddd;">Q.tà</th>
                    <th style="padding: 10px; text-align: right; border-bottom: 1px solid #ddd;">Prezzo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        {{ $item->nome_prodotto }}<br>
                        <small style="color: #666;">
                            @if($item->colore) {{ $item->colore }} @endif
                            @if($item->taglia) / {{ $item->taglia }} @endif
                        </small>
                    </td>
                    <td style="padding: 10px; text-align: center; border-bottom: 1px solid #eee;">{{ $item->quantita }}</td>
                    <td style="padding: 10px; text-align: right; border-bottom: 1px solid #eee;">€ {{ number_format($item->prezzo_unitario, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Subtotale Prodotti:</td>
                    <td style="padding: 10px; text-align: right;">€ {{ number_format($order->totale_imponibile, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Spedizione:</td>
                    <td style="padding: 10px; text-align: right;">
                        @if($order->costo_spedizione > 0)
                            € {{ number_format($order->costo_spedizione, 2, ',', '.') }}
                        @else
                            Gratis
                        @endif
                    </td>
                </tr>
                @if(($order->costo_contrassegno ?? 0) > 0)
                <tr>
                    <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Supplemento Contrassegno:</td>
                    <td style="padding: 10px; text-align: right;">+ € {{ number_format($order->costo_contrassegno, 2, ',', '.') }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold; font-size: 1.1em; border-top: 2px solid #ddd;">Totale Ordine:</td>
                    <td style="padding: 10px; text-align: right; font-weight: bold; font-size: 1.1em; border-top: 2px solid #ddd;">€ {{ number_format($order->totale_ordine, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <h3 style="border-bottom: 2px solid #eee; padding-bottom: 8px;">Dati di Spedizione</h3>
        <p>
            {{ $order->spedizione_nome }}<br>
            {{ $order->spedizione_indirizzo }}<br>
            {{ $order->spedizione_cap }} {{ $order->spedizione_citta }} ({{ $order->spedizione_provincia }})<br>
            {{ $order->spedizione_nazione }}
        </p>

        @if($order->metodo_pagamento === 'bonifico')
            <div style="margin-top: 30px; padding: 20px; background-color: #EEF2FF; border: 1px solid #C7D2FE; border-radius: 8px;">
                <h3 style="margin-top: 0; color: #4F46E5;">Coordinate Bancarie per il Pagamento</h3>
                <p style="font-size: 0.95em; color: #374151;">
                    Per completare il tuo ordine, effettua un bonifico bancario utilizzando le coordinate seguenti.<br>
                    <strong>Causale:</strong> Numero Ordine #{{ $order->numero_ordine }}
                </p>
                <table style="width: 100%; font-size: 0.95em;">
                    <tr>
                        <td style="padding: 5px 0; color: #6B7280; width: 120px;">Intestato a:</td>
                        <td style="padding: 5px 0; font-weight: bold;">{{ \App\Models\Setting::where('key', 'bonifico_intestazione')->value('value') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #6B7280;">Banca:</td>
                        <td style="padding: 5px 0; font-weight: bold;">{{ \App\Models\Setting::where('key', 'bonifico_banca')->value('value') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #6B7280;">IBAN:</td>
                        <td style="padding: 5px 0; font-weight: bold; font-family: monospace; font-size: 1.1em;">{{ \App\Models\Setting::where('key', 'bonifico_iban')->value('value') }}</td>
                    </tr>
                </table>
                <p style="margin-bottom: 0; font-size: 0.85em; color: #6B7280; font-style: italic; margin-top: 15px;">
                    L'ordine verrà elaborato una volta ricevuto l'accredito.
                </p>
            </div>
        @endif
        
        <p style="margin-top: 40px; font-size: 0.9em; color: #777;">Questo è un messaggio automatico, si prega di non rispondere a questa email.</p>
    </div>
</body>
</html>
