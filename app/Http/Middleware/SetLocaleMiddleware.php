<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Agenti e Amministrazione sono ESCLUSIVAMENTE in lingua Italiana
        if ($request->user() && ($request->user()->role === 'admin' || $request->user()->role === 'agent')) {
            $locale = 'it';
            session(['locale' => 'it']);
        } elseif ($request->has('lang') && in_array($request->query('lang'), ['it', 'en'])) {
            $locale = $request->query('lang');
            session(['locale' => $locale]);
            if ($request->user()) {
                $request->user()->update(['locale' => $locale]);
                if ($request->user()->b2bCustomer) {
                    $request->user()->b2bCustomer->update(['locale' => $locale]);
                }
            }
        } elseif (session()->has('locale')) {
            $locale = session('locale');
        } elseif ($request->user() && !empty($request->user()->locale)) {
            $locale = $request->user()->locale;
            session(['locale' => $locale]);
        } elseif ($request->user() && $request->user()->b2bCustomer && !empty($request->user()->b2bCustomer->locale)) {
            $locale = $request->user()->b2bCustomer->locale;
            session(['locale' => $locale]);
        } else {
            $locale = config('app.locale', 'it');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
