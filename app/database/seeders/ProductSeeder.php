<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Mechanical Keyboard', 'slug' => 'mechanical-keyboard', 'price_cents' => 4999, 'stock' => 50],
            ['name' => 'Wireless Mouse', 'slug' => 'wireless-mouse', 'price_cents' => 2999, 'stock' => 80],
            ['name' => '27\" Monitor', 'slug' => '27-monitor', 'price_cents' => 19999, 'stock' => 20],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['slug' => $product['slug']], $product);
        }
    }
}
