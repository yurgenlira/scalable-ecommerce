<?php

namespace App\Modules\Catalog\Domain\Models;

use App\Modules\Catalog\Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'price_cents', 'stock', 'description'];

    protected $casts = [
        'price_cents' => 'integer',
        'stock' => 'integer',
    ];

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
