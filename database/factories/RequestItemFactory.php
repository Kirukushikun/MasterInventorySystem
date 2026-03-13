<?php

namespace Database\Factories;

use App\Models\RequestItem;
use App\Models\User;
use App\Models\FarmLocation;
use App\Models\DepartmentDivision;
use App\Models\Approval;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestItem>
 */
class RequestItemFactory extends Factory
{
    protected $model = RequestItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'series_number' => $this->faker->unique()->text(10),
            'approver_id' => 4,
            'requested_by_id' => 4,
            'farm_location_id' => 1,
            'department_division_id' => 2,
            'approval_id' => $this->faker->numberBetween(1, 7),
            'remarks' => $this->faker->optional()->text(100),
            'date_requested' => $this->faker->date(),
            'date_needed' => $this->faker->date(),
            'checkout_status' => 0,
            'active_status' => 1,
            'deleted_status' => 0,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
