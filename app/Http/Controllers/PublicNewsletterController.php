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

        if ($subscriber && $subscriber->active) {
            $existingCoupon = !empty($subscriber->coupon_code) 
                ? ShopCoupon::where('code', $subscriber->coupon_code)->first() 
                : null;

            if ($existingCoupon && $existingCoupon->attivo && (!$existingCoupon->sempre_attivo && $existingCoupon->utilizzi < 1)) {
                $msg = $isEn
                    ? "This email address is already subscribed! Your coupon code is: {$existingCoupon->code}."
                    : "Questo indirizzo email risulta già iscritto alla nostra newsletter! Il tuo codice sconto è: {$existingCoupon->code}.";
                $retCoupon = $existingCoupon->code;
            } else {
                $msg = $isEn
                    ? 'This email address is already subscribed to our newsletter!'
                    : 'Questo indirizzo email risulta già iscritto alla nostra newsletter!';
                $retCoupon = $subscriber->coupon_code;
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'already_subscribed' => true,
                    'coupon' => $retCoupon,
                    'message' => $msg,
                ]);
            }
            return redirect()->back()->with('success', $msg);
        }

        if ($subscriber) {
            // Riattivazione iscrizione
            $subscriber->active = true;
            $subscriber->ip_address = $ip;
            $subscriber->locale = $locale;
            $subscriber->touch();
            $subscriber->save();
        } else {
            // Nuova iscrizione
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'ip_address' => $ip,
                'locale' => $locale,
                'active' => true,
            ]);
        }

        // Genera coupon univoco incrementale welcomebicapshop-X (Mono-uso: si disattiva dopo l'uso)
        $coupon = $this->generateUniqueNewsletterCoupon($email);
        $subscriber->coupon_code = $coupon->code;
        $subscriber->save();

        // Invia email di benvenuto con il coupon univoco
        try {
            Mail::to($subscriber->email)->send(new NewsletterWelcome($subscriber, $coupon, $locale));
        } catch (\Exception $e) {
            Log::error('Errore durante invio email di benvenuto newsletter (' . $subscriber->email . '): ' . $e->getMessage());
        }

        $msg = $isEn
            ? "Thank you for subscribing to our newsletter! We sent you an email with your welcome coupon code ({$coupon->code})."
            : "Grazie per esserti iscritto alla nostra Newsletter! Ti abbiamo inviato una mail con il tuo codice sconto ({$coupon->code}).";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'coupon' => $coupon->code,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Genera un coupon univoco progressivo welcomebicapshop-X
     * con disattivazione automatica al primo utilizzo (mono-uso).
     */
    protected function generateUniqueNewsletterCoupon(string $email): ShopCoupon
    {
        $existingCodes = ShopCoupon::where('code', 'like', 'welcomebicapshop-%')->pluck('code');
        $maxNum = 0;
        foreach ($existingCodes as $code) {
            if (preg_match('/^welcomebicapshop-(\d+)$/', $code, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $nextNum = $maxNum + 1;
        $newCode = 'welcomebicapshop-' . $nextNum;

        while (ShopCoupon::where('code', $newCode)->exists()) {
            $nextNum++;
            $newCode = 'welcomebicapshop-' . $nextNum;
        }

        return ShopCoupon::create([
            'code' => $newCode,
            'name' => 'Sconto Benvenuto Newsletter (' . $email . ')',
            'type' => 'percentuale',
            'valore' => 5.00,
            'min_spesa' => 100.00,
            'sempre_attivo' => false, // Mono-uso: si disattiva dopo l'utilizzo!
            'attivo' => true,
            'utilizzi' => 0,
        ]);
    }
}

