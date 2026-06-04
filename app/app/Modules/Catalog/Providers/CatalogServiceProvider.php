<?php

namespace App\Modules\Catalog\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CatalogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        Route::middleware('api')->prefix('api')->group(__DIR__.'/../routes.php');
    }
}
