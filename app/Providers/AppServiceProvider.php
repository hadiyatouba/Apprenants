<?php

namespace App\Providers;

use App\Services\FirebaseServices;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\ServiceProvider;
use App\Repositories\FirebaseReferentielRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FirebaseReferentielRepository::class, function ($app) {
            return new FirebaseReferentielRepository($app->make(Database::class));
        });
        $this->app->bind(FirebaseServices::class, function ($app) {
            return new FirebaseServices();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
