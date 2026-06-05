<?php

namespace App\Modules\Catalog\Domain;

use App\Modules\Catalog\Contracts\CatalogContract;
use App\Modules\Catalog\Contracts\ProductData;
use App\Modules\Catalog\Domain\Models\Product;

final class CatalogService implements CatalogContract
{
    public function find(int $productId): ?ProductData
    {
        $product = Product::find($productId);

        return $product === null ? null : new ProductData(
            id: $product->id,
            name: $product->name,
            priceCents: $product->price_cents,
            stock: $product->stock,
        );
    }

    public function decrementStock(int $productId, int $quantity): void
    {
        Product::whereKey($productId)->decrement('stock', $quantity);
    }
}
