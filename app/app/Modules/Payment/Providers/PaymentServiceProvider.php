<?php

namespace App\Modules\Payment\Providers;

use App\Modules\Payment\Contracts\PaymentGateway;
use App\Modules\Payment\Domain\MockPaymentGateway;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, MockPaymentGateway::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
