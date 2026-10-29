<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterConfirmEmail;
use App\Mail\NewsletterWelcome;
use App\Models\NewsletterSubscriber;
use App\Models\ShopCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        // Operazione 'IN' (Iscrizione con Double Opt-in: richiede conferma via email)
        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        // Se è già confermato ed è attivo
        if ($subscriber && $subscriber->confirmed && $subscriber->active) {
            $msg = $isEn
                ? 'This email address is already subscribed and confirmed to our newsletter!'
                : 'Questo indirizzo email risulta già iscritto e confermato alla nostra newsletter!';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'already_subscribed' => true,
                    'coupon' => $subscriber->coupon_code,
                    'message' => $msg,
                ]);
            }
            return redirect()->back()->with('success', $msg);
        }

        // Se l'utente esiste ma non ha ancora confermato l'email (o era disiscritto)
        if ($subscriber) {
            $subscriber->verification_token = Str::random(40);
            $subscriber->ip_address = $ip;
            $subscriber->locale = $locale;
            $subscriber->save();
        } else {
            // Nuovo iscritto: creato NON confermato e NON attivo finché non clicca sul link
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'ip_address' => $ip,
                'locale' => $locale,
                'verification_token' => Str::random(40),
                'confirmed' => false,
                'active' => false,
                'coupon_code' => null,
            ]);
        }

        // Invia email di richiesta conferma (Double Opt-In) - NESSUN coupon viene creato/inviato ora!
        try {
            \App\Services\ShopMailService::send($subscriber->email, new NewsletterConfirmEmail($subscriber, $locale));
        } catch (\Exception $e) {
            Log::error('Errore durante invio email di conferma newsletter (' . $subscriber->email . '): ' . $e->getMessage());
        }

        $msg = $isEn
            ? "Thank you! We sent a confirmation email to {$subscriber->email}. Please check your inbox and click the link to confirm your subscription and receive your discount coupon!"
            : "Grazie! Ti abbiamo inviato un'email di verifica a {$subscriber->email}. Clicca sul link nell'email per confermare l'iscrizione e ricevere il tuo coupon sconto!";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'requires_confirmation' => true,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Endpoint di conferma email cliccato dall'utente nell'email ricevuta.
     * Solo qui l'utente viene effettivamente iscritto e riceve il coupon sconto.
     */
    public function confirm(string $token)
    {
        $isEn = app()->getLocale() === 'en';
        $subscriber = NewsletterSubscriber::where('verification_token', $token)->first();

        if (!$subscriber) {
            return redirect()->route('public.home')->with('error', $isEn
                ? 'Invalid or expired confirmation link.'
                : 'Link di conferma non valido o scaduto.');
        }

        // Se era già confermato in precedenza
        if ($subscriber->confirmed) {
            $coupon = !empty($subscriber->coupon_code) 
                ? ShopCoupon::where('code', $subscriber->coupon_code)->first() 
                : null;

            return view('public.newsletter_confirmed', [
                'subscriber' => $subscriber,
                'coupon' => $coupon,
                'alreadyConfirmed' => true,
            ]);
        }

        // 1. Genera il coupon univoco progressivo welcomebicapshop-X (mono-uso)
        $coupon = $this->generateUniqueNewsletterCoupon($subscriber->email);

        // 2. Attiva e conferma l'iscrizione
        $subscriber->confirmed = true;
        $subscriber->confirmed_at = now();
        $subscriber->active = true;
        $subscriber->coupon_code = $coupon->code;
        $subscriber->save();

        // 3. Invia email di benvenuto con il coupon sconto
        try {
            \App\Services\ShopMailService::send($subscriber->email, new NewsletterWelcome($subscriber, $coupon, $subscriber->locale));
        } catch (\Exception $e) {
            Log::error('Errore invio email welcome newsletter (' . $subscriber->email . '): ' . $e->getMessage());
        }

        return view('public.newsletter_confirmed', [
            'subscriber' => $subscriber,
            'coupon' => $coupon,
            'alreadyConfirmed' => false,
        ]);
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
