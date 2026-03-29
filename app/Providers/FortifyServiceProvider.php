<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Http\Responses\CaptivePortalLoginResponse;
use App\Http\Responses\CaptivePortalRegisterResponse;
use App\Http\Responses\CaptivePortalTwoFactorLoginResponse;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind custom Fortify responses to handle captive portal flow
        $this->app->singleton(LoginResponseContract::class, CaptivePortalLoginResponse::class);
        $this->app->singleton(RegisterResponseContract::class, CaptivePortalRegisterResponse::class);
        $this->app->singleton(TwoFactorLoginResponseContract::class, CaptivePortalTwoFactorLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);

        // Allow login using email OR username OR mobile
        Fortify::authenticateUsing(function (Request $request) {
            $identifier = $request->input('email'); // UI uses 'email' field for identifier
            $password = $request->input('password');

            if (! $identifier || ! $password) {
                return null;
            }

            $query = \App\Models\User::query();
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $query->where('email', $identifier);
            } elseif (preg_match('/^\+?\d[\d\-\s]*$/', $identifier)) {
                // Normalize mobile: remove non-digits, convert local 0XXXXXXXXX to +92XXXXXXXXX
                $digits = preg_replace('/\D+/', '', $identifier);
                $normalized = str_starts_with($digits, '0')
                    ? '+92'.substr($digits, 1)
                    : '+'.$digits;
                $query->where('mobile', $normalized);
            } else {
                $query->where('username', $identifier);
            }

            $user = $query->first();
            if (! $user) {
                return null;
            }

            return \Illuminate\Support\Facades\Hash::check($password, $user->getAuthPassword()) ? $user : null;
        });
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(function (Request $request) {
            if (User::query()->nonSystem()->doesntExist()) {
                return redirect()->route('register', $request->query());
            }

            // Capture captive portal params from query into session to persist across auth flow
            if ($request->query('clientMac')) {
                $request->session()->put('captive.clientMac', $request->query('clientMac'));
            }
            if ($request->query('target')) {
                $request->session()->put('captive.target', $request->query('target'));
            }

            return Inertia::render('auth/login', [
                'canResetPassword' => Features::enabled(Features::resetPasswords()),
                'canRegister' => Features::enabled(Features::registration()),
                'status' => $request->session()->get('status'),
                'clientMac' => $request->query('clientMac'),
                'target' => $request->query('target'),
            ]);
        });

        Fortify::resetPasswordView(fn (Request $request) => Inertia::render('auth/reset-password', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]));

        Fortify::requestPasswordResetLinkView(fn (Request $request) => Inertia::render('auth/forgot-password', [
            'status' => $request->session()->get('status'),
        ]));

        Fortify::verifyEmailView(fn (Request $request) => Inertia::render('auth/verify-email', [
            'status' => $request->session()->get('status'),
        ]));

        Fortify::registerView(function (Request $request) {
            if ($request->query('clientMac')) {
                $request->session()->put('captive.clientMac', $request->query('clientMac'));
            }
            if ($request->query('target')) {
                $request->session()->put('captive.target', $request->query('target'));
            }

            return Inertia::render('auth/register', [
                'isFirstSignup' => User::query()->nonSystem()->doesntExist(),
            ]);
        });

        Fortify::twoFactorChallengeView(fn () => Inertia::render('auth/two-factor-challenge'));

        Fortify::confirmPasswordView(fn () => Inertia::render('auth/confirm-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
