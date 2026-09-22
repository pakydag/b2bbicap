<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reimpostazione Password</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background-color: #f1f5f9; }
        .wrapper { max-width: 650px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { background: #000000; color: #ffffff; padding: 28px 24px; text-align: center; border-bottom: 4px solid #facc15; }
        .header img { max-height: 48px; max-width: 220px; width: auto; object-fit: contain; margin: 0 auto; display: block; }
        .header p { margin: 12px 0 0; font-size: 12px; color: #cbd5e1; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; }
        .content { padding: 32px 28px; }
        .greeting { font-size: 16px; font-weight: 700; color: #000000; margin-bottom: 12px; }
        .text-lead { font-size: 14px; color: #475569; margin-bottom: 24px; }
        .btn-container { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; padding: 14px 32px; background: #000000; color: #ffffff !important; text-decoration: none; border-radius: 10px; font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 0.8px; }
        .info-box { background: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #facc15; padding: 16px; border-radius: 8px; margin-top: 24px; font-size: 13px; color: #78350f; }
        .sub-footer { margin-top: 28px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; line-height: 1.5; }
        .sub-footer a { color: #0f172a; word-break: break-all; font-weight: 600; }
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
            <p>Reimpostazione Password</p>
        </div>
        
        <div class="content">
            <p class="greeting">
                @if(!empty($user->name))
                    Gentile <strong>{{ $user->name }} {{ $user->surname ?? '' }}</strong>,
                @else
                    Gentile <strong>Utente</strong>,
                @endif
            </p>
            <p class="text-lead">
                Hai ricevuto questa email perché è stata richiesta la reimpostazione della password per il tuo account sul portale di <strong>{{ $companyName }}</strong>.
            </p>

            <div class="btn-container">
                <a href="{{ $url }}" class="btn" style="color:#ffffff;">Reimposta Password →</a>
            </div>

            <div class="info-box">
                <p style="margin: 0 0 6px 0;">⏳ Questo link di reimpostazione password scadrà tra <strong>{{ $count ?? 60 }} minuti</strong>.</p>
                <p style="margin: 0;">Se non hai richiesto il ripristino della password, non è necessaria alcuna azione: il tuo account e la tua password attuale sono al sicuro.</p>
            </div>

            <p style="margin-top: 24px; font-size: 14px; color: #334155;">
                Cordiali saluti,<br>
                <strong>{{ $companyName }}</strong>
            </p>

            <div class="sub-footer">
                Se riscontri problemi cliccando sul pulsante "Reimposta Password", copia e incolla il seguente link direttamente nella barra degli indirizzi del tuo browser:<br>
                <a href="{{ $url }}">{{ $url }}</a>
            </div>
        </div>

        <div class="footer">
            &copy; Cedma srl - Tutti i diritti riservati.
        </div>
    </div>
</body>
</html>
