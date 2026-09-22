<?php

namespace App\Providers;

use App\Models\Contacto;
use App\Models\Usuario;
use App\Observers\ContactoObserver;
use App\Policies\ComunidadEducativaPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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

            return rtrim($baseUrl, '/').'/reset-password?token='.$token.'&email='.$user->email;
        });

        Contacto::observe(ContactoObserver::class);

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

        // Register Gates and Policies
        Gate::define('view-comunidad', [ComunidadEducativaPolicy::class, 'view']);
    }
}
