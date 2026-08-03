<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAgent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->role === 'agent' || (auth()->user()->role === 'customer' && auth()->user()->b2b_customer_id !== null))) {
            
            if (!session()->has('b2b_cart_loaded')) {
                $dbCart = \App\Models\B2bCart::where('user_id', auth()->id())->first();
                if ($dbCart && is_array($dbCart->cart_data)) {
                    session()->put('b2b_cart', $dbCart->cart_data);
                }
                session()->put('b2b_cart_loaded', true);
            }
            
            return $next($request);
        }

        return redirect('/login')->with('error', 'Accesso Negato: Area riservata agli Agenti ed Aziende B2B.');
    }
}
