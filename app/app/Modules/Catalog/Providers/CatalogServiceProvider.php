<?php

namespace App\Modules\Catalog\Providers;

use App\Modules\Catalog\Contracts\CatalogContract;
use App\Modules\Catalog\Domain\CatalogService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CatalogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CatalogContract::class, CatalogService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        Route::middleware('api')->prefix('api')->group(__DIR__.'/../routes.php');
    }
}
