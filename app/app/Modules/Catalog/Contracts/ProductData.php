<?php

namespace App\Modules\Catalog\Contracts;

final readonly class ProductData
{
    public function __construct(
        public int $id,
        public string $name,
        public int $priceCents,
        public int $stock,
    ) {}
}
