<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider{


    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('is-admin', function($user){
            return $user->isAdmin();
        });

        Gate::define('is-agency', function($user){
            return $user->isAgency();
        });

        Gate::define('is-customer', function($user){
            return $user->isCustomer();
        });

        Gate::define('is-customer-colombia', function($user){
            return $user->isCustomer() && ($user->country_id === 2) && ($user->parent_id != 3);
        });

        Gate::define('is-customer-beon24', function($user){
            return $user->isCustomer() && ($user->country_id === 2) && ($user->parent_id == 3);
        });

        Gate::define('is-customer-chile', function($user){
            return $user->isCustomer() && ($user->country_id === 1);
        });
    }
}
