<?php

namespace Database\Factories;

use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubCategoryFactory extends Factory
{
    protected $model = SubCategory::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'subcategory_name' => $this->faker->word,
            'subcategory_description' => $this->faker->sentence,
            'active_status' => true,
            'deleted_status' => false,
        ];
    }
}

