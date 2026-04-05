<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\Usuario;

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
        // Personalizar la URL de recuperación de contraseña para la SPA
        ResetPassword::createUrlUsing(function (Usuario $user, string $token) {
            $frontendUrls = explode(',', env('FRONTEND_URL', 'http://localhost:5173'));
            $baseUrl = trim($frontendUrls[0]);
            return rtrim($baseUrl, '/') . '/reset-password?token=' . $token . '&email=' . $user->email;
        });

        \App\Models\Contacto::observe(\App\Observers\ContactoObserver::class);

        // Standardize password requirements globally
        Password::defaults(function () {
            return Password::min(10)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        RateLimiter::for('login', function (Request $request) {
            $identifier = $request->input('email') 
                ?: $request->input('documento_numero') 
                ?: $request->ip();
                
            return Limit::perMinute(5)->by($identifier);
        });

        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perHour(3)->by($request->input('email') ?: $request->ip());
        });

        RateLimiter::for('resend-verification', function (Request $request) {
            return Limit::perHour(3)->by($request->user()?->id ?: $request->ip());
        });
    }
}
