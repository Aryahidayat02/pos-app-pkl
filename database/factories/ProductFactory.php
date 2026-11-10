<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'sku' => 'SKU-' . fake()->unique()->numerify('####'),
            'name' => fake()->words(2, true),
            'unit' => 'pcs',
            'purchase_price' => 5000,
            'selling_price' => 10000,
            'stock' => 10,
            'min_stock' => 2,
            'is_active' => true,
        ];
    }
}