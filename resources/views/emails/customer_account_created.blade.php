<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attivazione Accesso Portale B2B</title>
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
        .password-badge { font-family: monospace; background: #e2e8f0; padding: 3px 8px; border-radius: 6px; font-size: 14px; color: #000000; font-weight: bold; }
        .btn-container { text-align: center; margin: 24px 0; }
        .btn { display: inline-block; padding: 14px 28px; background: #000000; color: #ffffff !important; text-decoration: none; border-radius: 10px; font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 0.8px; }
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
            <p>Attivazione Accesso Portale B2B</p>
        </div>
        
        <div class="content">
            <p class="greeting">Gentile <strong>{{ $customer->business_name }}</strong>,</p>
            <p class="text-lead">
                È stato attivato il tuo profilo aziendale per accedere direttamente al <strong>Portale B2B</strong> di <strong>{{ $companyName }}</strong>.
                Di seguito trovi le tue credenziali di accesso per consultare il catalogo e gestire i tuoi ordini.
            </p>

            <!-- Box Credenziali -->
            <div class="card-box" style="border-left: 4px solid #facc15;">
                <h3>🔐 Credenziali di Accesso</h3>
                <p style="margin: 4px 0; font-size: 13px;"><strong>Email (Login):</strong> {{ $loginEmail }}</p>
                <p style="margin: 4px 0; font-size: 13px;"><strong>Password:</strong> <span class="password-badge">{{ $password }}</span></p>
                <p style="margin: 10px 0 0; font-size: 11px; color: #64748b;">
                    * Ti consigliamo di accedere ed eventualmente personalizzare la password nella sezione profilo.
                </p>
            </div>

            <div class="btn-container">
                <a href="{{ $portalUrl ?? route('login') }}" class="btn" style="color:#ffffff;">Accedi al Portale B2B →</a>
            </div>

            <!-- Box Agente di Riferimento -->
            @if($customer->agents && $customer->agents->isNotEmpty())
                <div class="card-box" style="border-left: 4px solid #000000; background: #fffdf5; border-color: #fef08a;">
                    <h3 style="color: #000000; margin-bottom: 8px;">👤 Agente Commerciale di Riferimento</h3>
                    <p style="margin: 0 0 12px; font-size: 13px; color: #475569;">
                        Per qualsiasi richiesta d'ordine o assistenza commerciale, puoi fare riferimento al tuo agente dedicato:
                    </p>
                    @foreach($customer->agents as $ag)
                        <div style="background: #ffffff; padding: 12px 14px; border-radius: 8px; border: 1px solid #fde047; margin-bottom: 8px;">
                            <p style="margin: 0 0 4px; font-size: 14px; font-weight: 800; color: #000000;">
                                {{ $ag->name }} {{ $ag->surname }}
                            </p>
                            <p style="margin: 2px 0; font-size: 12px; color: #475569;">
                                <strong>Email:</strong> <a href="mailto:{{ $ag->email }}" style="color: #2563eb; text-decoration: none; font-weight: bold;">{{ $ag->email }}</a>
                            </p>
                            @if($ag->phone)
                                <p style="margin: 2px 0; font-size: 12px; color: #475569;">
                                    <strong>Telefono:</strong> <a href="tel:{{ $ag->phone }}" style="color: #1e293b; text-decoration: none; font-weight: bold;">{{ $ag->phone }}</a>
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Box Riepilogo Azienda -->
            <div class="card-box">
                <h3>🏢 Dati Anagrafica Aziendale</h3>
                <table class="table-wrap">
                    <tbody>
                        <tr>
                            <th style="width: 35%;">Ragione Sociale</th>
                            <td><strong>{{ $customer->business_name }}</strong></td>
                        </tr>
                        @if($customer->code)
                            <tr>
                                <th>Codice Cliente</th>
                                <td><code>{{ $customer->code }}</code></td>
                            </tr>
                        @endif
                        @if($customer->vat_number)
                            <tr>
                                <th>Partita IVA / CF</th>
                                <td>{{ $customer->vat_number }}</td>
                            </tr>
                        @endif
                        @if($customer->agents && $customer->agents->isNotEmpty())
                            <tr>
                                <th>Agente Assegnato</th>
                                <td>
                                    <strong>{{ $customer->agents->map(fn($a) => $a->name . ' ' . $a->surname)->join(', ') }}</strong>
                                    <br><span style="color: #64748b; font-size: 11px;">{{ $customer->agents->pluck('email')->join(', ') }}</span>
                                </td>
                            </tr>
                        @endif
                        @if($customer->contact_name || $customer->contact_surname)
                            <tr>
                                <th>Referente</th>
                                <td>{{ $customer->contact_name }} {{ $customer->contact_surname }}</td>
                            </tr>
                        @endif
                        @if($customer->email)
                            <tr>
                                <th>Email Aziendale</th>
                                <td>{{ $customer->email }}</td>
                            </tr>
                        @endif
                        @if($customer->phone)
                            <tr>
                                <th>Telefono</th>
                                <td>{{ $customer->phone }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            &copy; Cedma srl - Tutti i diritti riservati.
        </div>
    </div>
</body>
</html>
