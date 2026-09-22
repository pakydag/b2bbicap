<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiornamento Autorizzazioni Agente B2B</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background-color: #f1f5f9; }
        .wrapper { max-width: 650px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { background: #000000; color: #ffffff; padding: 28px 24px; text-align: center; border-bottom: 4px solid #facc15; }
        .header img { max-height: 48px; max-width: 220px; width: auto; object-fit: contain; margin: 0 auto; display: block; }
        .header p { margin: 12px 0 0; font-size: 12px; color: #cbd5e1; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; }
        .content { padding: 32px 28px; }
        .greeting { font-size: 16px; font-weight: 700; color: #000000; margin-bottom: 12px; }
        .text-lead { font-size: 14px; color: #475569; margin-bottom: 24px; }
        .card-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .card-box h3 { margin: 0 0 12px; font-size: 13px; font-weight: 800; text-transform: uppercase; color: #000000; letter-spacing: 0.5px; }
        .btn-container { text-align: center; margin: 24px 0; }
        .btn { display: inline-block; padding: 14px 28px; background: #000000; color: #ffffff !important; text-decoration: none; border-radius: 10px; font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 0.8px; }
        .badge { display: inline-block; background: #fef08a; color: #713f12; padding: 5px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; border: 1px solid #fde047; }
        .table-wrap { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 12px; }
        .table-wrap th { background: #f1f5f9; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 800; border-bottom: 1px solid #cbd5e1; }
        .table-wrap td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .table-wrap tr:last-child td { border-bottom: none; }
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
        @endphp
        <div class="header">
            @if($hasLocalLogo && isset($message))
                <img src="{{ $message->embed($logoLocalPath) }}" alt="BICAP">
            @elseif($hasLocalLogo)
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoLocalPath)) }}" alt="BICAP">
            @endif
            <p>Aggiornamento Autorizzazioni Agente</p>
        </div>
        
        <div class="content">
            <p class="greeting">Gentile <strong>{{ $agent->name }} {{ $agent->surname }}</strong>,</p>
            <p class="text-lead">
                Ti informiamo che l'amministrazione ha aggiornato il profilo e le autorizzazioni associate al tuo account Agente sul Portale B2B.
            </p>

            <div class="btn-container">
                <a href="{{ route('login') }}" class="btn" style="color:#ffffff;">Accedi al Portale Agenti →</a>
            </div>

            <!-- Box Linee / Brand -->
            <div class="card-box">
                <h3>🏷️ Linee / Brand Assegnati</h3>
                <p style="margin: 0 0 8px; font-size: 12px; color: #64748b;">
                    Elenco aggiornato delle linee e dei cataloghi a cui hai accesso:
                </p>
                @if($agent->b2bBrands && $agent->b2bBrands->count() > 0)
                    <div style="margin-top: 6px;">
                        @foreach($agent->b2bBrands as $brand)
                            <span class="badge" style="margin-right: 4px; margin-bottom: 4px; display: inline-block;">{{ $brand->name }}</span>
                        @endforeach
                    </div>
                @else
                    <p style="margin: 0; font-size: 12px; color: #dc2626; font-weight: bold;">Nessuna linea specifica attualmente abilitata.</p>
                @endif
            </div>

            <!-- Box Clienti Assegnati -->
            <div class="card-box">
                <h3>🏢 Aziende Clienti Autorizzate ({{ $agent->b2bCustomers ? $agent->b2bCustomers->count() : 0 }})</h3>
                <p style="margin: 0 0 8px; font-size: 12px; color: #64748b;">
                    Elenco aggiornato dei clienti abilitati per cui puoi operare ed inserire ordini:
                </p>
                @if($agent->b2bCustomers && $agent->b2bCustomers->count() > 0)
                    <table class="table-wrap">
                        <thead>
                            <tr>
                                <th>Ragione Sociale</th>
                                <th>P.IVA / CF</th>
                                <th>Codice</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agent->b2bCustomers as $customer)
                                <tr>
                                    <td><strong>{{ $customer->business_name }}</strong></td>
                                    <td>{{ $customer->vat_number ?? '-' }}</td>
                                    <td>{{ $customer->code ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="margin: 0; font-size: 12px; color: #dc2626; font-weight: bold;">Nessuna azienda cliente attualmente assegnata.</p>
                @endif
            </div>
        </div>

        <div class="footer">
            &copy; Cedma srl - Tutti i diritti riservati.
        </div>
    </div>
</body>
</html>
