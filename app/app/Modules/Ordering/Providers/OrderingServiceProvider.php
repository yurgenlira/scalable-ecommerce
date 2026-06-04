<?php

namespace App\Modules\Ordering\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class OrderingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        Route::middleware('api')->prefix('api')->group(__DIR__.'/../routes.php');
    }
}
