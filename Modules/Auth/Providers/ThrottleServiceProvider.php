<?php

namespace Modules\Auth\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class ThrottleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerLoginThrottle();
    }

    /**
     * Login throttle: 3 requests per minute
     */
    protected function registerLoginThrottle(): void
    {
        RateLimiter::for('auth', function (Request $request) {
            $email = (string) $request->input('email');
            return Limit::perMinute(3)->by($email ?: $request->ip());
        });
    }
}
