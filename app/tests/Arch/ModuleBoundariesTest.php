<?php

arch('catalog is independent of other modules')
    ->expect('App\Modules\Catalog')
    ->not->toUse([
        'App\Modules\Cart',
        'App\Modules\Ordering',
        'App\Modules\Payment',
        'App\Modules\Identity',
    ]);

arch('payment is independent of other modules')
    ->expect('App\Modules\Payment')
    ->not->toUse([
        'App\Modules\Catalog',
        'App\Modules\Cart',
        'App\Modules\Ordering',
        'App\Modules\Identity',
    ]);

arch('identity is independent of other modules')
    ->expect('App\Modules\Identity')
    ->not->toUse([
        'App\Modules\Catalog',
        'App\Modules\Cart',
        'App\Modules\Ordering',
        'App\Modules\Payment',
    ]);

arch('cart reaches catalog only through contracts')
    ->expect('App\Modules\Cart')
    ->not->toUse([
        'App\Modules\Ordering',
        'App\Modules\Identity',
        'App\Modules\Payment',
        'App\Modules\Catalog\Domain',
        'App\Modules\Catalog\Http',
    ]);

arch('ordering crosses boundaries only through contracts')
    ->expect('App\Modules\Ordering')
    ->not->toUse([
        'App\Modules\Catalog\Domain',
        'App\Modules\Catalog\Http',
        'App\Modules\Cart\Domain',
        'App\Modules\Cart\Http',
        'App\Modules\Payment\Domain',
        'App\Modules\Identity',
    ]);
