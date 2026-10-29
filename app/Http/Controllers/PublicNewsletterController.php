<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterWelcome;
use App\Models\NewsletterSubscriber;
use App\Models\ShopCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PublicNewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $isEn = app()->getLocale() === 'en';

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'nl_op' => 'nullable|in:IN,OUT',
        ], [
            'email.required' => $isEn ? 'Please enter a valid email address.' : 'Inserisci un indirizzo email valido.',
            'email.email' => $isEn ? 'Please enter a valid email address.' : 'Inserisci un indirizzo email valido.',
        ]);

        $email = strtolower(trim($validated['email']));
        $op = $validated['nl_op'] ?? 'IN';
        $ip = $request->ip();
        $locale = session('locale') ?? app()->getLocale();

        if ($op === 'OUT') {
            $subscriber = NewsletterSubscriber::where('email', $email)->first();
            if ($subscriber) {
                $subscriber->active = false;
                $subscriber->ip_address = $ip;
                $subscriber->save();
            }

            $msg = $isEn 
                ? 'You have been successfully unsubscribed from our newsletter.'
                : 'Sei stato cancellato dalla nostra newsletter con successo.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }

            return redirect()->back()->with('success', $msg);
        }

        // Operazione 'IN' (Iscrizione)
        $subscriber = NewsletterSubscriber::where('email', $email)->first();
        $isNewSubscription = false;

        if ($subscriber) {
            if ($subscriber->active) {
                $msg = $isEn
                    ? 'This email address is already subscribed to our newsletter! Check your inbox for your welcome coupon.'
                    : 'Questo indirizzo email risulta già iscritto alla nostra newsletter! Controlla la tua casella per il codice sconto.';

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'already_subscribed' => true,
                        'message' => $msg,
                    ]);
                }
                return redirect()->back()->with('success', $msg);
            } else {
                // Riattivazione iscrizione
                $subscriber->active = true;
                $subscriber->ip_address = $ip;
                $subscriber->locale = $locale;
                $subscriber->touch();
                $subscriber->save();
                $isNewSubscription = true;
            }
        } else {
            // Nuova iscrizione
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'ip_address' => $ip,
                'locale' => $locale,
                'active' => true,
            ]);
            $isNewSubscription = true;
        }

        // Recupera il coupon di benvenuto (welcomebicapshop)
        $coupon = ShopCoupon::where('code', 'welcomebicapshop')->first();

        // Invia email di benvenuto
        if ($isNewSubscription) {
            try {
                Mail::to($subscriber->email)->send(new NewsletterWelcome($subscriber, $coupon, $locale));
            } catch (\Exception $e) {
                Log::error('Errore durante invio email di benvenuto newsletter (' . $subscriber->email . '): ' . $e->getMessage());
            }
        }

        $msg = $isEn
            ? 'Thank you for subscribing to our newsletter! We sent you an email with your welcome coupon code.'
            : 'Grazie per esserti iscritto alla nostra Newsletter! Ti abbiamo inviato una mail con il tuo codice sconto di benvenuto.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'coupon' => $coupon ? $coupon->code : 'welcomebicapshop',
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
