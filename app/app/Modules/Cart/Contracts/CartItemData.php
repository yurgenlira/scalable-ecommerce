<?php

namespace App\Modules\Cart\Contracts;

final readonly class CartItemData
{
    public function __construct(
        public int $productId,
        public int $quantity,
    ) {}
}
