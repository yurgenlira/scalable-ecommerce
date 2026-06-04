<?php

use App\Modules\Catalog\Domain\Models\Product;
use App\Modules\Identity\Domain\Models\User;
use Laravel\Sanctum\Sanctum;

it('completes checkout and marks the order paid', function () {
    config(['services.payment.mock.decline_over_cents' => null]);
    Sanctum::actingAs(User::factory()->create());
    $product = Product::factory()->create(['price_cents' => 5000, 'stock' => 10]);

    $this->postJson('/api/cart/items', ['product_id' => $product->id, 'quantity' => 2])->assertCreated();

    $this->postJson('/api/checkout')
        ->assertCreated()
        ->assertJson(['status' => 'paid', 'total_cents' => 10000]);

    expect($product->fresh()->stock)->toBe(8);
});

it('marks the order payment_failed when declined and leaves stock intact', function () {
    config(['services.payment.mock.decline_over_cents' => 1000]);
    Sanctum::actingAs(User::factory()->create());
    $product = Product::factory()->create(['price_cents' => 5000, 'stock' => 10]);

    $this->postJson('/api/cart/items', ['product_id' => $product->id, 'quantity' => 1])->assertCreated();

    $this->postJson('/api/checkout')->assertCreated()->assertJson(['status' => 'payment_failed']);

    expect($product->fresh()->stock)->toBe(10);
});
