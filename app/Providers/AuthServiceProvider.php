<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Laravel\Passport\Passport;
use App\Services\AuthenticationService;
use App\Interfaces\AuthenticationInterface;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    public function register()
    {
        $this->app->bind(AuthenticationInterface::class, AuthenticationService::class);
    }

    public function boot()
    {
        $this->registerPolicies();

    }
}