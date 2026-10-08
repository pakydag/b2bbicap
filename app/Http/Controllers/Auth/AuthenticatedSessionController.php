<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        if ($request->has('redirect_to')) {
            session()->put('url.intended', $request->query('redirect_to'));
        }
        
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Salva l'eventuale lingua selezionata nella schermata di login prima della rigenerazione
        $preLoginLocale = session('locale');

        $request->authenticate();

        $request->session()->regenerate();

        // Gestione Lingua per Ruolo
        $user = $request->user();
        if ($user) {
            if ($user->role === 'admin' || $user->role === 'agent') {
                // Amministrazione e Agenti: sempre e solo in Italiano
                session(['locale' => 'it']);
                \Illuminate\Support\Facades\App::setLocale('it');
                if ($user->locale !== 'it') {
                    $user->update(['locale' => 'it']);
                }
            } else {
                // Clienti (Customer):
                // Se hanno selezionato inglese al login O se il loro profilo/cliente è impostato in inglese
                $customerLocale = $user->b2bCustomer?->locale ?: $user->locale;
                $chosenLocale = in_array($preLoginLocale, ['it', 'en']) ? $preLoginLocale : ($customerLocale ?: 'it');
                
                session(['locale' => $chosenLocale]);
                \Illuminate\Support\Facades\App::setLocale($chosenLocale);
                $user->update(['locale' => $chosenLocale]);
                if ($user->b2bCustomer && $user->b2bCustomer->locale !== $chosenLocale) {
                    $user->b2bCustomer->update(['locale' => $chosenLocale]);
                }
            }
        }

        // Avvio non-bloccante della sincronizzazione Giacenze B2B in background ad ogni accesso (FTPS)
        try {
            $phpBinary = \App\Models\B2bProduct::getPhpCliBinary();
            $artisan = base_path('artisan');
            exec("{$phpBinary} {$artisan} b2b:sync-giacenze --force > /dev/null 2>&1 &");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("[AuthLogin] Impossibile avviare il sync giacenze in background: " . $e->getMessage());
        }

        // Ignora background/ajax endpoint eventualmente salvati in intended
        $intended = session()->get('url.intended');
        if ($intended && (
            str_contains($intended, 'ping-sync') ||
            str_contains($intended, 'sync') ||
            str_contains($intended, 'api/') ||
            str_ends_with($intended, '.json')
        )) {
            session()->forget('url.intended');
        }

        // Determina la fallback route in base al ruolo se non c'è un url intended vero e proprio
        if ($request->user()->role === 'admin') {
            $user = $request->user();
            // Se è un admin B2B e NON è super_admin, lo mandiamo direttamente al dashboard B2B
            if (!$user->is_super_admin && $user->can_manage_agents && !$user->can_manage_site) {
                $fallbackRoute = route('admin.b2b.dashboard', absolute: false);
            } else {
                $fallbackRoute = route('dashboard', absolute: false);
            }
        } elseif ($request->user()->role === 'agent' || ($request->user()->role === 'customer' && $request->user()->b2b_customer_id !== null)) {
            $fallbackRoute = route('agent.dashboard', absolute: false);
        } else {
            $fallbackRoute = route('public.account.dashboard', absolute: false);
        }

        return redirect()->intended($fallbackRoute);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $role = $request->user()?->role;
        $isB2bCustomer = $request->user()?->b2b_customer_id !== null;

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($role === 'admin' || $role === 'agent' || ($role === 'customer' && $isB2bCustomer)) {
            return redirect()->route('login');
        }

        return redirect('/');
    }
}
