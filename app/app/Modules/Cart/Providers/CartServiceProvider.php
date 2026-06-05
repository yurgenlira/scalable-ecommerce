<?php

namespace App\Modules\Cart\Providers;

use App\Modules\Cart\Contracts\CartContract;
use App\Modules\Cart\Domain\CartService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CartContract::class, CartService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        Route::middleware('api')->prefix('api')->group(__DIR__.'/../routes.php');
    }
}
