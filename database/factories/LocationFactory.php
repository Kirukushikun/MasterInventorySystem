<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'location_name' => $this->faker->city,
            'description' => $this->faker->sentence,
            'active_status' => true,
            'deleted_status' => false,
        ];
    }
}

