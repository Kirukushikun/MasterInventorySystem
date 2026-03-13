<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'category_name' => $this->faker->word,
            'category_description' => $this->faker->sentence,
            'active_status' => true,
            'deleted_status' => false,
        ];
    }
}

