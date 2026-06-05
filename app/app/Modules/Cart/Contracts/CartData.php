<?php

namespace App\Modules\Cart\Contracts;

final readonly class CartData
{
    /** @param list<CartItemData> $items */
    public function __construct(
        public array $items,
    ) {}
}
