<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Connessione Email SMTP - BICAP</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #18181b;">
<div style="max-width: 600px; margin: 30px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    
    <!-- Header Giallo / Nero BICAP -->
    <div style="background-color: #000000; padding: 24px 30px; text-align: center; border-bottom: 4px solid #fedb00;">
        <h1 style="color: #fedb00; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase;">
            BICAP
        </h1>
        <p style="color: #ffffff; margin: 4px 0 0 0; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; font-weight: 600;">
            ABSOLUTELY SAFE
        </p>
    </div>

    <!-- Body -->
    <div style="padding: 30px;">
        <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 16px; margin-bottom: 24px; text-align: center;">
            <div style="font-size: 28px; margin-bottom: 8px;">✅</div>
            <h2 style="color: #065f46; margin: 0 0 4px 0; font-size: 18px; font-weight: 700;">
                Test di Connessione Riuscito!
            </h2>
            <p style="color: #047857; margin: 0; font-size: 13px;">
                La configurazione del server SMTP per <strong>{{ $type }}</strong> è corretta e funzionante.
            </p>
        </div>

        <h3 style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; border-bottom: 1px solid #e4e4e7; padding-bottom: 8px; margin-top: 0;">
            Dettagli della Configurazione
        </h3>

        <table style="width: 100%; font-size: 13px; line-height: 1.8; margin-bottom: 24px;">
            <tr>
                <td style="color: #71717a; width: 40%; font-weight: 600;">Ambito Email:</td>
                <td style="color: #000000; font-weight: 700;">{{ $type }}</td>
            </tr>
            <tr>
                <td style="color: #71717a; font-weight: 600;">Server Host (SMTP):</td>
                <td style="color: #000000; font-family: monospace;">{{ $serverHost }}</td>
            </tr>
            <tr>
                <td style="color: #71717a; font-weight: 600;">Email Mittente:</td>
                <td style="color: #000000;">{{ $fromEmail }}</td>
            </tr>
            <tr>
                <td style="color: #71717a; font-weight: 600;">Nome Mittente:</td>
                <td style="color: #000000;">{{ $fromName }}</td>
            </tr>
            <tr>
                <td style="color: #71717a; font-weight: 600;">Data e Ora Invio:</td>
                <td style="color: #000000;">{{ $sentAt }}</td>
            </tr>
        </table>

        <p style="font-size: 12px; color: #a1a1aa; line-height: 1.5; margin: 0; text-align: center;">
            Questa è un'email automatica generata dal pannello di amministrazione di BICAP per verificare la corretta trasmissione della posta elettronica.
        </p>
    </div>

    <!-- Footer -->
    <div style="background-color: #fafafa; padding: 16px; text-align: center; border-top: 1px solid #e4e4e7; font-size: 11px; color: #a1a1aa;">
        &copy; {{ date('Y') }} BICAP - Calzaturificio 5BI S.r.l. · Tutti i diritti riservati.
    </div>
</div>
</body>
</html>

