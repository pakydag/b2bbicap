<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocaleMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhook/spoki',
            'webhook/*',
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'agent' => \App\Http\Middleware\IsAgent::class,
        ]);

        $middleware->redirectTo(
            guests: function (\Illuminate\Http\Request $request) {
                if ($request->expectsJson() || $request->is('*ping-sync*') || $request->is('api/*')) {
                    return null;
                }
                if ($request->is('booking*')) {
                    return route('public.booking.login.view');
                }
                return route('login');
            },
            users: function (\Illuminate\Http\Request $request) {
                $user = $request->user();
                if ($user) {
                    if ($user->role === 'agent' || ($user->role === 'customer' && $user->b2b_customer_id !== null)) {
                        return route('agent.dashboard');
                    }
                    if ($user->role === 'admin') {
                        if (!$user->is_super_admin && $user->can_manage_agents && !$user->can_manage_site) {
                            return route('admin.b2b.dashboard');
                        }
                        return route('dashboard');
                    }
                    if ($user->role === 'customer') {
                        return route('public.account.dashboard');
                    }
                }
                return route('dashboard');
            }
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
