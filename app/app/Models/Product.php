<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'price_cents', 'stock', 'description'];

    protected $casts = [
        'price_cents' => 'integer',
        'stock' => 'integer',
    ];
}