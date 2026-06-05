<?php

namespace App\Modules\Catalog\Contracts;

interface CatalogContract
{
    public function find(int $productId): ?ProductData;

    public function decrementStock(int $productId, int $quantity): void;
}
