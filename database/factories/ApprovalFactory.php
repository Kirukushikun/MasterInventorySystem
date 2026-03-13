<?php

namespace Database\Factories;

use App\Models\Approvals;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalFactory extends Factory
{
    protected $model = Approvals::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'active_status' => true,
            'deleted_status' => false,
        ];
    }
}
