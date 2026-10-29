<!DOCTYPE html>
<html lang="{{ $isEn ? 'en' : 'it' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isEn ? 'Confirm your BICAP Subscription' : 'Conferma la tua iscrizione BICAP' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            font-family: 'Montserrat', Arial, sans-serif;
            color: #333333;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        td {
            padding: 0;
        }
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }
            .content-padding {
                padding: 25px 20px !important;
            }
            .action-btn {
                width: 100% !important;
                padding: 16px 20px !important;
                box-sizing: border-box !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 20px 0; background-color: #f4f4f4;">
    <center>
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f4f4;">
            <tr>
                <td align="center" style="padding: 10px;">
                    <!-- Main Container -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06);">
                        
                        <!-- Header with BICAP Logo -->
                        <tr>
                            <td align="center" style="background-color: #000000; padding: 28px 20px;">
                                <a href="https://bicap.shop" target="_blank" style="text-decoration: none;">
                                    <img src="https://bicap.shop/images/bicap-logo-popup.png" width="190" alt="BICAP" style="width: 190px; max-width: 190px; border: 0; display: block; margin: 0 auto;">
                                </a>
                            </td>
                        </tr>

                        <!-- Top Yellow Line Accent -->
                        <tr>
                            <td height="4" style="background-color: #fedb00; line-height: 4px; font-size: 4px;">&nbsp;</td>
                        </tr>

                        <!-- Main Content -->
                        <tr>
                            <td class="content-padding" style="padding: 40px 35px 25px 35px; text-align: center;">
                                <div style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #7a6100; margin-bottom: 8px;">
                                    {{ $isEn ? '★ EMAIL CONFIRMATION ★' : '★ CONFERMA INDIRIZZO EMAIL ★' }}
                                </div>
                                <h1 style="margin: 0 0 18px 0; font-family: 'Montserrat', Arial, sans-serif; font-size: 24px; font-weight: 800; color: #111111; letter-spacing: 0.5px; text-transform: uppercase; line-height: 1.2;">
                                    {{ $isEn ? 'Almost done! Confirm your email' : 'Ci siamo quasi! Conferma la tua email' }}
                                </h1>
                                <p style="margin: 0 0 24px 0; font-size: 15px; line-height: 24px; color: #444444;">
                                    @if($isEn)
                                        Thank you for requesting to join the <strong>BICAP</strong> community!<br><br>
                                        To ensure this is your email address and to receive your <strong>exclusive 5% welcome discount coupon</strong>, please click the button below:
                                    @else
                                        Grazie per aver richiesto l'iscrizione alla community di <strong>BICAP</strong>!<br><br>
                                        Per verificare che questo indirizzo ti appartenga e ricevere il tuo <strong>codice coupon sconto esclusivo del 5%</strong>, conferma la tua iscrizione cliccando sul pulsante qui sotto:
                                    @endif
                                </p>

                                <!-- Button CTA -->
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 25px auto;">
                                    <tr>
                                        <td align="center" style="background-color: #fedb00; border-radius: 4px;">
                                            <a href="{{ $confirmationUrl }}" class="action-btn" target="_blank" style="display: inline-block; padding: 18px 36px; font-family: 'Montserrat', Arial, sans-serif; font-size: 15px; font-weight: 800; color: #000000; text-decoration: none; text-transform: uppercase; letter-spacing: 1px;">
                                                {{ $isEn ? 'CONFIRM MY SUBSCRIPTION & GET COUPON →' : 'CONFERMA ISCRIZIONE E RICEVI IL COUPON →' }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin: 24px 0 0 0; font-size: 12px; line-height: 18px; color: #888888;">
                                    @if($isEn)
                                        If the button does not work, copy and paste this link into your browser:<br>
                                        <a href="{{ $confirmationUrl }}" style="color: #666666; word-break: break-all;">{{ $confirmationUrl }}</a>
                                    @else
                                        Se il pulsante non funziona, copia e incolla questo link nel tuo browser:<br>
                                        <a href="{{ $confirmationUrl }}" style="color: #666666; word-break: break-all;">{{ $confirmationUrl }}</a>
                                    @endif
                                </p>
                            </td>
                        </tr>

                        <!-- Notice Info -->
                        <tr>
                            <td style="padding: 0 35px 25px 35px; text-align: center;">
                                <div style="background-color: #fbfbfb; border: 1px solid #eeeeee; border-radius: 6px; padding: 14px 18px;">
                                    <p style="margin: 0; font-size: 12px; line-height: 18px; color: #777777;">
                                        @if($isEn)
                                            <strong>Didn't request this subscription?</strong> You can safely ignore this email. You will not be subscribed and will not receive any further emails from us.
                                        @else
                                            <strong>Non hai richiesto tu l'iscrizione?</strong> Puoi semplicemente ignorare questa email. Non verrai iscritto e non riceverai altre comunicazioni.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>

                        <!-- Divider -->
                        <tr>
                            <td style="padding: 0 35px;">
                                <hr style="border: none; border-top: 1px solid #eeeeee; margin: 0;">
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style="padding: 25px 35px 35px 35px; text-align: center; background-color: #fafafa;">
                                <div style="font-size: 13px; font-weight: 700; color: #111111; margin-bottom: 6px;">
                                    CALZATURIFICIO 5BI S.R.L. &bull; BICAP
                                </div>
                                <div style="font-size: 12px; color: #777777; line-height: 18px; margin-bottom: 16px;">
                                    Via dell'Euro 2 &bull; Zona Industriale &bull; 76121 Barletta (BT) &bull; Italia<br>
                                    P.IVA 03867620723
                                </div>

                                <!-- Social Links -->
                                <div style="margin: 15px 0;">
                                    <a href="https://www.linkedin.com/company/bicap-calzaturificio-5-bi/" target="_blank" style="display: inline-block; margin: 0 6px;">
                                        <img src="https://bicap.shop/images/link.jpg" style="width: 20px; height: 20px; border: 0;" alt="LinkedIn">
                                    </a>
                                    <a href="https://www.facebook.com/bicap.absolutelysafe" target="_blank" style="display: inline-block; margin: 0 6px;">
                                        <img src="https://bicap.shop/images/fb.jpg" style="width: 19px; height: 19px; border: 0;" alt="Facebook">
                                    </a>
                                    <a href="https://www.instagram.com/bicap.absolutelysafe/" target="_blank" style="display: inline-block; margin: 0 6px;">
                                        <img src="https://bicap.shop/images/insta.jpg" style="width: 18px; height: 18px; border: 0;" alt="Instagram">
                                    </a>
                                    <a href="https://www.youtube.com/channel/UCzRKy7yaDRhdAnnEhoA-T-g" target="_blank" style="display: inline-block; margin: 0 6px;">
                                        <img src="https://bicap.shop/images/you.jpg" style="width: 22px; height: 22px; border: 0;" alt="YouTube">
                                    </a>
                                </div>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
