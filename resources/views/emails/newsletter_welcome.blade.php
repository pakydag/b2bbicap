<!DOCTYPE html>
<html lang="{{ $isEn ? 'en' : 'it' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isEn ? 'Welcome to BICAP' : 'Benvenuto in BICAP' }}</title>
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
                padding: 20px 15px !important;
            }
            .coupon-title {
                font-size: 20px !important;
            }
            .coupon-code {
                font-size: 22px !important;
                letter-spacing: 2px !important;
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

                        <!-- Hero Title & Welcome Message -->
                        <tr>
                            <td class="content-padding" style="padding: 40px 35px 25px 35px; text-align: center;">
                                <h1 style="margin: 0 0 16px 0; font-family: 'Montserrat', Arial, sans-serif; font-size: 24px; font-weight: 800; color: #111111; letter-spacing: 0.5px; text-transform: uppercase;">
                                    {{ $isEn ? 'Welcome to our Newsletter!' : 'Benvenuto nella nostra Newsletter!' }}
                                </h1>
                                <p style="margin: 0 0 18px 0; font-size: 15px; line-height: 24px; color: #444444;">
                                    @if($isEn)
                                        Thank you for subscribing to the <strong>BICAP</strong> community!<br>
                                        From now on, you'll be the first to discover our safety innovations, technical footwear collections, and exclusive private offers.
                                    @else
                                        Grazie per esserti iscritto alla community di <strong>BICAP</strong>!<br>
                                        Da oggi sarai tra i primi a scoprire le nostre novità sulle calzature di sicurezza, collezioni speciali e promozioni riservate.
                                    @endif
                                </p>
                            </td>
                        </tr>

                        <!-- Coupon Box -->
                        <tr>
                            <td style="padding: 0 35px 30px 35px;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fffdf0; border: 2px dashed #fedb00; border-radius: 8px;">
                                    <tr>
                                        <td style="padding: 25px 20px; text-align: center;">
                                            <div style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #7a6100; margin-bottom: 8px;">
                                                {{ $isEn ? '★ YOUR WELCOME GIFT ★' : '★ IL TUO REGALO DI BENVENUTO ★' }}
                                            </div>
                                            <div class="coupon-title" style="font-size: 22px; font-weight: 800; color: #111111; margin-bottom: 12px;">
                                                {{ $isEn ? '5% DISCOUNT ON YOUR NEXT ORDER' : 'SCONTO DEL 5% SUL PROSSIMO ORDINE' }}
                                            </div>
                                            
                                            <!-- Code Tag -->
                                            <div style="margin: 15px auto; display: inline-block;">
                                                <div class="coupon-code" style="background-color: #ffffff; border: 2px solid #111111; border-radius: 6px; padding: 12px 24px; font-family: 'Courier New', Courier, monospace; font-size: 24px; font-weight: 800; letter-spacing: 3px; color: #111111; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                                    {{ $coupon ? $coupon->code : 'welcomebicapshop' }}
                                                </div>
                                            </div>

                                            <p style="margin: 14px 0 0 0; font-size: 13px; line-height: 20px; color: #666666;">
                                                @if($isEn)
                                                    Enter this promo code in your cart before checkout.<br>
                                                    <span style="font-size: 12px; color: #888888;">Valid on a minimum purchase of €100. Cannot be combined with other current offers.</span>
                                                @else
                                                    Inserisci questo codice promozionale nel carrello prima del pagamento.<br>
                                                    <span style="font-size: 12px; color: #888888;">Valido su una spesa minima di 100€. Non cumulabile con altre promozioni in corso.</span>
                                                @endif
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Call To Action Button -->
                        <tr>
                            <td align="center" style="padding: 0 35px 35px 35px;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="center" style="background-color: #fedb00; border-radius: 4px;">
                                            <a href="https://bicap.shop" target="_blank" style="display: inline-block; padding: 16px 36px; font-family: 'Montserrat', Arial, sans-serif; font-size: 15px; font-weight: 800; color: #000000; text-decoration: none; text-transform: uppercase; letter-spacing: 1px;">
                                                {{ $isEn ? 'EXPLORE CATALOG & SHOP NOW →' : 'SCOPRI IL CATALOGO E ACQUISTA ORA →' }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Assistance info -->
                        <tr>
                            <td style="padding: 0 35px 25px 35px; text-align: center;">
                                <p style="margin: 0; font-size: 13px; line-height: 20px; color: #777777;">
                                    {{ $isEn ? 'Need help or sizing advice? Contact our customer support team directly at info@bicap.it' : 'Hai bisogno di assistenza o consigli sulle taglie? Contatta il nostro team all\'indirizzo info@bicap.it' }}
                                </p>
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

                                <div style="font-size: 11px; color: #999999; margin-top: 15px; line-height: 16px;">
                                    {{ $isEn ? 'You received this email because you subscribed to our newsletter on bicap.shop.' : 'Ricevi questa email perché ti sei iscritto alla nostra newsletter su bicap.shop.' }}
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

