<?php

use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('adds an item and recalculates the total', function () {
    Sanctum::actingAs(User::factory()->create());
    $product = Product::factory()->create(['price_cents' => 2500, 'stock' => 10]);

    $this->postJson('/api/cart/items', ['product_id' => $product->id, 'quantity' => 2])
        ->assertCreated()
        ->assertJson(['total_cents' => 5000]);
});

it('rejects adding more than available stock', function () {
    Sanctum::actingAs(User::factory()->create());
    $product = Product::factory()->create(['stock' => 1]);

    $this->postJson('/api/cart/items', ['product_id' => $product->id, 'quantity' => 5])
        ->assertStatus(422);
});
