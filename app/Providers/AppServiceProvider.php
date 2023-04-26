<?php

namespace App\Providers;

use App\Services\Token\JwtTokenService;
use App\Services\Token\Tokenable;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            Tokenable::class,
            fn (Application $app) => new JwtTokenService()
        );

        $this->app->alias(Tokenable::class, 'jwt');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
