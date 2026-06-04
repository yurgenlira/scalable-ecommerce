<?php

namespace App\Modules\Catalog\Database\Factories;

use App\Modules\Catalog\Domain\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => str($name)->slug(),
            'price_cents' => fake()->numberBetween(1000, 50000),
            'stock' => fake()->numberBetween(0, 100),
            'description' => fake()->sentence(),
        ];
    }
}
