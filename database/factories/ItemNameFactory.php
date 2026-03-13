<?php

namespace Database\Factories;


use App\Models\ItemName;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemNameFactory extends Factory
{
    protected $model = ItemName::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'subcategory_id' => SubCategory::factory(),
            'product_id' => Product::factory(),
            'item_name' => $this->faker->word,
            'item_description' => $this->faker->sentence,
            'active_status' => true,
            'deleted_status' => false,
        ];
    }
}

