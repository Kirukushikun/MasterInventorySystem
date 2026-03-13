<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\ItemName;
use App\Models\RequestItem;
use DB;

class ItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {   
        // DB::table('item_names')->delete();
        // ItemName::factory()->count(50)->create();
        // Item::factory()->count(50)->create();


        RequestItem::factory()->count(100)->create();
        // \DB::table('items')->delete();
        
        // \DB::table('items')->insert(array (
        //     0 => 
        //     array (
        //         'id' => 1,
        //         'category_id' => 1,
        //         'subcategory_id' => 1,
        //         'product_id' => 1,
        //         'item_name_id' => 1,
        //         'location_id' => 2,
        //         'user_id' => 4,
        //         'model_number' => 'SDF412',
        //         'item_number' => 'ZSD4241546SD',
        //         'order_number' => 'O5123',
        //         'supplier_id' => NULL,
        //         'uom_id' => 1,
        //         'quantity' => 4,
        //         'current_quantity' => -3,
        //         'reorder_threshold' => 1,
        //         'purchase_date' => '2023-08-04',
        //         'expiry_date' => '2023-08-24',
        //         'purchase_cost' => 3000.1234,
        //         'remarks' => NULL,
        //         'qr_code' => 'c4ca4238a0b923820dcc509a6f75849b.png',
        //         'active_status' => 1,
        //         'deleted_status' => 0,
        //         'created_at' => '2023-08-12 20:14:52',
        //         'updated_at' => '2023-08-12 23:41:59',
        //     ),
        //     1 => 
        //     array (
        //         'id' => 2,
        //         'category_id' => 1,
        //         'subcategory_id' => 1,
        //         'product_id' => 1,
        //         'item_name_id' => 1,
        //         'location_id' => 2,
        //         'user_id' => 4,
        //         'model_number' => NULL,
        //         'item_number' => NULL,
        //         'order_number' => NULL,
        //         'supplier_id' => NULL,
        //         'uom_id' => 8,
        //         'quantity' => 11,
        //         'current_quantity' => 9,
        //         'reorder_threshold' => 2,
        //         'purchase_date' => '2023-08-04',
        //         'expiry_date' => NULL,
        //         'purchase_cost' => 3000.1234,
        //         'remarks' => NULL,
        //         'qr_code' => 'c81e728d9d4c2f636f067f89cc14862c.png',
        //         'active_status' => 1,
        //         'deleted_status' => 0,
        //         'created_at' => '2023-08-12 23:59:23',
        //         'updated_at' => '2023-08-13 00:02:49',
        //     ),
        //     2 => 
        //     array (
        //         'id' => 3,
        //         'category_id' => 1,
        //         'subcategory_id' => 1,
        //         'product_id' => 1,
        //         'item_name_id' => 1,
        //         'location_id' => 1,
        //         'user_id' => 4,
        //         'model_number' => '341',
        //         'item_number' => 'sasd41',
        //         'order_number' => 'aser5134',
        //         'supplier_id' => NULL,
        //         'uom_id' => 2,
        //         'quantity' => 15,
        //         'current_quantity' => 15,
        //         'reorder_threshold' => 2,
        //         'purchase_date' => '2023-08-13',
        //         'expiry_date' => NULL,
        //         'purchase_cost' => 3000.1234,
        //         'remarks' => NULL,
        //         'qr_code' => 'eccbc87e4b5ce2fe28308fd9f2a7baf3.png',
        //         'active_status' => 1,
        //         'deleted_status' => 0,
        //         'created_at' => '2023-08-13 10:58:12',
        //         'updated_at' => '2023-08-13 10:58:13',
        //     ),
        // ));


    }
}