<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Passport::tokensExpireIn(now()->seconds(60));
        // Passport::refreshTokensExpireIn(now()->seconds(10));
        // Passport::personalAccessTokensExpireIn(now()->seconds(10)); do not need at all!
    }
}
