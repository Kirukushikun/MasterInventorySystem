<?php

namespace Database\Factories;


use App\Models\Item;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\ItemName;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UnitOfMeasurement;
use App\Models\Approvals;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        // Get array of IDs
        $approvalIds = Approvals::pluck('id')->toArray();

    // Get array of titles
        $approvalTitles = Approvals::pluck('description')->toArray();
        return [
            'category_id' => Category::factory(),
            'subcategory_id' => SubCategory::factory(),
            'product_id' => Product::factory(),
            'item_name_id' => ItemName::factory(),
            'location_id' => Location::factory(),
            'user_id' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 55]),
            'model_number' => $this->faker->word,
            'item_number' => $this->faker->uuid,
            'order_number' => $this->faker->uuid,
            'supplier_id' => Supplier::factory(),
            'uom_id' => UnitOfMeasurement::factory(),
            'quantity' => $this->faker->numberBetween(15, 1000),
            'current_quantity' => $this->faker->numberBetween(15, 500),
            'reorder_threshold' => 10,
            'average_consumption' => 5,
            'average_lead_time' => 3,
            'time_unit' => 'days',
            'purchase_date' => $this->faker->date(),
            'expiry_date' => $this->faker->date(),
            'purchase_cost' => $this->faker->randomFloat(2, 100, 1000),
            'remarks' => $this->faker->sentence(),
            'qr_code' => $this->faker->uuid,
            'item_image' => 'default.png',
            'item_image_path' => 'images/items/default.png',
            'approval_id' => 7,//$this->faker->randomElement($approvalIds),
            'active_status' => true,
            'deleted_status' => false,
        ];
    }
}
