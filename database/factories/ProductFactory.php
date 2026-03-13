<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'subcategory_id' => SubCategory::factory(),
            'product_name' => $this->faker->word,
            'product_description' => $this->faker->sentence,
            'active_status' => true,
            'deleted_status' => false,
        ];
    }
}
