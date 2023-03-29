<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\TransferRequest;
use App\Observers\PaymentObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Observers\TransferRequestObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Payment::observe(PaymentObserver::class);
        TransferRequest::observe(TransferRequestObserver::class);
    }
}
