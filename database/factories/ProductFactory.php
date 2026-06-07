<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $cost = fake()->randomFloat(2, 5, 100);

        return [
            'category_id'        => Category::factory(),
            'name'               => fake()->words(3, true),
            'sku'                => strtoupper(Str::random(8)),
            'barcode'            => fake()->optional()->ean13(),
            'cost_price'         => $cost,
            'selling_price'      => round($cost * fake()->randomFloat(2, 1.2, 2.5), 2),
            'wholesale_price'    => round($cost * 1.1, 2),
            'stock_quantity'     => fake()->numberBetween(0, 100),
            'low_stock_threshold'=> 5,
            'is_active'          => true,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(['stock_quantity' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
