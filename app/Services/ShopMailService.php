<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Models\Setting;

class ShopMailService
{
    /**
     * Invia un'email mailable tramite il mailer dedicato dello Shop / Sito Web.
     *
     * @param mixed $recipient Indirizzo email o modello destinatario
     * @param \Illuminate\Mail\Mailable $mailable Istanza mailable da inviare
     * @return mixed
     */
    public static function send($recipient, $mailable)
    {
        $shopFrom = self::getShopFrom();
        if (!empty($shopFrom['address'])) {
            $mailable->from($shopFrom['address'], $shopFrom['name']);
        }

        return Mail::mailer('shop_smtp')->to($recipient)->send($mailable);
    }

    /**
     * Invia un'email mailable tramite il mailer dello Shop con copia nascosta (BCC).
     *
     * @param mixed $recipient
     * @param mixed $bcc
     * @param \Illuminate\Mail\Mailable $mailable
     * @return mixed
     */
    public static function sendWithBcc($recipient, $bcc, $mailable)
    {
        $shopFrom = self::getShopFrom();
        if (!empty($shopFrom['address'])) {
            $mailable->from($shopFrom['address'], $shopFrom['name']);
        }

        $mail = Mail::mailer('shop_smtp')->to($recipient);
        if (!empty($bcc)) {
            $mail->bcc($bcc);
        }

        return $mail->send($mailable);
    }

    /**
     * Ottiene l'indirizzo e il nome mittente configurati per lo Shop.
     *
     * @return array{address: string, name: string}
     */
    public static function getShopFrom(): array
    {
        try {
            $settings = Setting::whereIn('key', [
                'shop_mail_from_address',
                'shop_mail_from_name',
                'mail_from_address',
                'mail_from_name'
            ])->pluck('value', 'key');

            $address = !empty($settings['shop_mail_from_address'])
                ? $settings['shop_mail_from_address']
                : (!empty($settings['mail_from_address']) ? $settings['mail_from_address'] : config('mail.from.address'));

            $name = !empty($settings['shop_mail_from_name'])
                ? $settings['shop_mail_from_name']
                : (!empty($settings['mail_from_name']) ? $settings['mail_from_name'] : 'BICAP Official Shop');

            return [
                'address' => $address ?: 'shop@bicap.it',
                'name'    => $name ?: 'BICAP Official Shop'
            ];
        } catch (\Throwable $e) {
            return [
                'address' => config('mail.from.address', 'shop@bicap.it'),
                'name'    => config('mail.from.name', 'BICAP Official Shop')
            ];
        }
    }
}

