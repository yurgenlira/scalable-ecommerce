<?php

use App\Modules\Cart\Providers\CartServiceProvider;
use App\Modules\Catalog\Providers\CatalogServiceProvider;
use App\Modules\Identity\Providers\IdentityServiceProvider;
use App\Modules\Ordering\Providers\OrderingServiceProvider;
use App\Modules\Payment\Providers\PaymentServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    IdentityServiceProvider::class,
    CatalogServiceProvider::class,
    CartServiceProvider::class,
    OrderingServiceProvider::class,
    PaymentServiceProvider::class,
];
