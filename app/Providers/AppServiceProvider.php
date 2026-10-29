<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            // Se le impostazioni esistono nel filesystem o db, carichiamo la conf mail
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
                \Illuminate\Support\Facades\View::share('global_settings', $settings);

                // 1. Configurazione Mail B2B (default mailer 'smtp')
                if (!empty($settings['mail_host'])) {
                    config([
                        'mail.mailers.smtp.host'       => $settings['mail_host'] ?? config('mail.mailers.smtp.host'),
                        'mail.mailers.smtp.port'       => $settings['mail_port'] ?? config('mail.mailers.smtp.port'),
                        'mail.mailers.smtp.encryption' => !empty($settings['mail_encryption']) ? $settings['mail_encryption'] : null,
                        'mail.mailers.smtp.username'   => $settings['mail_username'] ?? config('mail.mailers.smtp.username'),
                        'mail.mailers.smtp.password'   => $settings['mail_password'] ?? config('mail.mailers.smtp.password'),
                        'mail.from.address'            => $settings['mail_from_address'] ?? config('mail.from.address'),
                        'mail.from.name'               => $settings['mail_from_name'] ?? config('mail.from.name'),
                    ]);
                    
                    if (!empty($settings['mail_mailer'])) {
                        config(['mail.default' => $settings['mail_mailer']]);
                    }
                }

                // 2. Configurazione Mail Sito Web & Shop (mailer 'shop_smtp')
                $shopHost = !empty($settings['shop_mail_host']) ? $settings['shop_mail_host'] : ($settings['mail_host'] ?? null);
                if (!empty($shopHost)) {
                    $shopMailer = !empty($settings['shop_mail_mailer']) ? $settings['shop_mail_mailer'] : ($settings['mail_mailer'] ?? 'smtp');
                    config([
                        'mail.mailers.shop_smtp' => [
                            'transport'    => $shopMailer === 'log' ? 'log' : 'smtp',
                            'host'         => $shopHost,
                            'port'         => !empty($settings['shop_mail_port']) ? $settings['shop_mail_port'] : ($settings['mail_port'] ?? 587),
                            'encryption'   => !empty($settings['shop_mail_encryption']) ? $settings['shop_mail_encryption'] : (!empty($settings['mail_encryption']) ? $settings['mail_encryption'] : null),
                            'username'     => !empty($settings['shop_mail_username']) ? $settings['shop_mail_username'] : ($settings['mail_username'] ?? null),
                            'password'     => !empty($settings['shop_mail_password']) ? $settings['shop_mail_password'] : ($settings['mail_password'] ?? null),
                            'timeout'      => null,
                            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
                        ],
                    ]);
                }

                // Forza il ricaricamento dei mailer con le nuove configurazioni
                \Illuminate\Support\Facades\Mail::purge();
            }
        } catch (\Exception $e) {
            // Ignora errori di boot se ad esempio il DB non è ancora migrato
        }

        // Configurazione personalizzata dell'email di Reset Password
        \Illuminate\Auth\Notifications\ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            $companyName = \App\Models\Setting::where('key', 'mail_from_name')->value('value') ?? config('mail.from.name') ?? 'Calzaturificio 5b';
            $expireMinutes = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Reimpostazione Password - ' . $companyName)
                ->view('emails.password_reset', [
                    'user' => $notifiable,
                    'url' => $url,
                    'companyName' => $companyName,
                    'count' => $expireMinutes,
                ]);
        });

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('sections')) {
                \Illuminate\Support\Facades\View::composer(['public.partials.header', 'public.partials.footer'], function ($view) {
                    $view->with('shared_sezioni', \App\Models\Section::where('visibile', true)->orderBy('ordine')->get());
                });
            }
        } catch (\Exception $e) {
            // Ignora
        }
    }
}
