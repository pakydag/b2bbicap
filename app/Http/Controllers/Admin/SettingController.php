<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    private function stripDomain($url)
    {
        if (empty($url)) return $url;
        $baseUrl = config('app.url');
        return str_replace($baseUrl, '', $url);
    }

    /**
     * Show the form for editing the settings.
     */
    public function edit()
    {
        // Peschiamo tutti i settings dal database
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.settings.edit', compact('settings'));
    }

    /**
     * Update the settings in storage.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        // Define settings groups
        $shopSettings = [
            'shop_enabled',
            'payment_stripe_enabled',
            'payment_bonifico_enabled',
            'payment_contrassegno_enabled',
            'payment_paypal_enabled',
        ];
        
        $bookingSettings = [
            'booking_enabled',
            'booking_payment_stripe_enabled',
            'booking_payment_paypal_enabled',
            'booking_payment_bonifico_enabled',
        ];

        $b2bSettings = [
            'b2b_payment_stripe_enabled',
            'b2b_payment_paypal_enabled',
            'b2b_payment_bonifico_enabled',
        ];

        $paymentParams = [
            'stripe_key', 'stripe_secret',
            'paypal_client_id', 'paypal_secret', 'paypal_mode',
            'bonifico_intestazione', 'bonifico_banca', 'bonifico_iban',
            'payment_contrassegno_fee',
        ];

        $trackingSettings = [
            'tracking_ga_analytics', 'tracking_gtm', 'tracking_facebook_pixel',
            'cookie_consent_enabled', 'cookie_consent_text'
        ];

        $generalSettings = [
            'site_domain', 'site_logo', 'site_favicon', 'mail_mailer', 'mail_host', 'mail_port', 
            'mail_username', 'mail_password', 'mail_encryption', 
            'mail_from_address', 'mail_from_name',
            'vapi_key', 'vapi_assistant_id', 'spoki_key',
            'company_address', 'company_phone', 'company_email_public', 'company_vat',
            'social_facebook', 'social_instagram', 'social_linkedin', 'social_youtube',
            'footer_description', 'footer_description_en'
        ];

        $validated = $request->validate([
            'site_domain' => 'nullable|string|url',
            'site_logo' => 'nullable|string',
            'site_favicon' => 'nullable|string',
            'mail_mailer' => 'nullable|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|string',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
            'stripe_key' => 'nullable|string',
            'stripe_secret' => 'nullable|string',
            'paypal_client_id' => 'nullable|string',
            'paypal_secret' => 'nullable|string',
            'paypal_mode' => 'nullable|string',
            'bonifico_intestazione' => 'nullable|string',
            'bonifico_banca' => 'nullable|string',
            'bonifico_iban' => 'nullable|string',
            'payment_contrassegno_fee' => 'nullable|numeric|min:0',
            'vapi_key' => 'nullable|string',
            'vapi_assistant_id' => 'nullable|string',
            'spoki_key' => 'nullable|string',
            'tracking_ga_analytics' => 'nullable|string',
            'tracking_gtm' => 'nullable|string',
            'tracking_facebook_pixel' => 'nullable|string',
            'cookie_consent_text' => 'nullable|string',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string',
            'company_email_public' => 'nullable|email',
            'company_vat' => 'nullable|string',
            'social_facebook' => 'nullable|string',
            'social_instagram' => 'nullable|string',
            'social_linkedin' => 'nullable|string',
            'social_youtube' => 'nullable|string',
            'footer_description' => 'nullable|string',
            'footer_description_en' => 'nullable|string',
        ]);

        if (isset($validated['site_logo'])) {
            $validated['site_logo'] = $this->stripDomain($validated['site_logo']);
        }
        if (isset($validated['site_favicon'])) {
            $validated['site_favicon'] = $this->stripDomain($validated['site_favicon']);
        }

        // Process Checkboxes
        $checkboxes = array_merge($shopSettings, $bookingSettings, $b2bSettings, ['spoki_enabled', 'cookie_consent_enabled', 'accessibility_panel_enabled', 'english_enabled']);
        foreach ($checkboxes as $chk) {
            $canEdit = false;
            
            // Solo il super admin può abilitare/disabilitare i moduli principali
            if (in_array($chk, ['shop_enabled', 'booking_enabled', 'spoki_enabled'])) {
                if ($user->is_super_admin) $canEdit = true;
            } else {
                // Gli altri toggle (Stripe, Paypal, etc) possono essere gestiti dai rispettivi admin
                if ($user->is_super_admin) $canEdit = true;
                elseif (in_array($chk, $shopSettings) && $user->can_manage_shop) $canEdit = true;
                elseif (in_array($chk, $bookingSettings) && $user->can_manage_booking) $canEdit = true;
                elseif (in_array($chk, $b2bSettings) && $user->can_manage_agents) $canEdit = true;
                elseif (in_array($chk, ['cookie_consent_enabled', 'accessibility_panel_enabled', 'english_enabled'])) $canEdit = true;
            }

            if ($canEdit) {
                $validated[$chk] = $request->has($chk) ? '1' : '0';
            }
        }

        // Process standard fields
        foreach ($validated as $key => $value) {
            $canEdit = false;
            if ($user->is_super_admin) $canEdit = true;
            elseif (in_array($key, $generalSettings)) {
                // Tutti gli amministratori che accedono a questa pagina possono modificare logo e mail
                $canEdit = true;
            } elseif (in_array($key, $paymentParams)) {
                // I parametri API possono essere salvati se si ha accesso a Shop, Booking o B2B
                if ($user->can_manage_shop || $user->can_manage_booking || $user->can_manage_agents) $canEdit = true;
            } elseif (in_array($key, $trackingSettings)) {
                $canEdit = true;
            }

            if ($canEdit) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Configurazione aggiornata con successo.');
    }
}
