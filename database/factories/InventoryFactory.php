<?php

namespace Database\Factories;


use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class InventoryFactory extends Factory
{
    protected $model = Item::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => 1,
            'subcategory_id' => 1,
            'product_id' => 1,
            'item_name_id' => $this->faker->numberBetween(1, 10),
            'location_id' => 1,
            'user_id' => 4,
            'model_number' => $this->faker->optional()->text(10),
            'item_number' => $this->faker->optional()->text(10),
            'order_number' => $this->faker->optional()->text(10),
            'supplier_id' => 1,
            'uom_id' => $this->faker->numberBetween(1, 5),
            'quantity' => $this->faker->numberBetween(10, 200),
            'current_quantity' => $this->faker->optional()->numberBetween(30, 300),
            'reorder_threshold' => $this->faker->numberBetween(15, 100),
            'purchase_date' => $this->faker->optional()->date(),
            'expiry_date' => $this->faker->optional()->date(),
            'purchase_cost' => $this->faker->numberBetween(10000, 20000),
            'remarks' => $this->faker->optional()->text(100),
            'qr_code' => "sample.png",
            'active_status' => 1, // 90% chance of being true
            'deleted_status' => 0, // 10% chance of being true
        ];
    }
}
