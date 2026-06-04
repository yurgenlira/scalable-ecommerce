<?php

use App\Modules\Catalog\Domain\Models\Product;

it('lists products paginated', function () {
    Product::factory()->count(3)->create();

    $this->getJson('/api/products')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name', 'slug', 'price_cents']], 'current_page', 'per_page', 'total']);
});
