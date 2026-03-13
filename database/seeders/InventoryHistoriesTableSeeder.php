<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InventoryHistoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('inventory_histories')->delete();
        
        \DB::table('inventory_histories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'item_id' => 1,
                'transaction_type_id' => 1,
                'previous_quantity' => NULL,
                'new_quantity' => 4,
                'change_date' => '2023-08-12 20:14:52',
                'change_reason' => NULL,
                'user_id' => 4,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 20:14:52',
                'updated_at' => '2023-08-12 20:14:52',
            ),
            1 => 
            array (
                'id' => 2,
                'item_id' => 1,
                'transaction_type_id' => 10,
                'previous_quantity' => 4,
                'new_quantity' => 2,
                'change_date' => '2023-08-12 23:27:58',
                'change_reason' => NULL,
                'user_id' => 4,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 23:27:58',
                'updated_at' => '2023-08-12 23:27:58',
            ),
            2 => 
            array (
                'id' => 3,
                'item_id' => 1,
                'transaction_type_id' => 10,
                'previous_quantity' => 2,
                'new_quantity' => 1,
                'change_date' => '2023-08-12 23:30:07',
                'change_reason' => NULL,
                'user_id' => 4,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 23:30:07',
                'updated_at' => '2023-08-12 23:30:07',
            ),
            3 => 
            array (
                'id' => 4,
                'item_id' => 1,
                'transaction_type_id' => 10,
                'previous_quantity' => 1,
                'new_quantity' => -3,
                'change_date' => '2023-08-12 23:41:59',
                'change_reason' => NULL,
                'user_id' => 4,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 23:41:59',
                'updated_at' => '2023-08-12 23:41:59',
            ),
            4 => 
            array (
                'id' => 5,
                'item_id' => 2,
                'transaction_type_id' => 1,
                'previous_quantity' => NULL,
                'new_quantity' => 11,
                'change_date' => '2023-08-12 23:59:23',
                'change_reason' => NULL,
                'user_id' => 4,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 23:59:23',
                'updated_at' => '2023-08-12 23:59:23',
            ),
            5 => 
            array (
                'id' => 6,
                'item_id' => 2,
                'transaction_type_id' => 10,
                'previous_quantity' => 11,
                'new_quantity' => 9,
                'change_date' => '2023-08-13 00:02:49',
                'change_reason' => NULL,
                'user_id' => 4,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-13 00:02:49',
                'updated_at' => '2023-08-13 00:02:49',
            ),
            6 => 
            array (
                'id' => 7,
                'item_id' => 3,
                'transaction_type_id' => 1,
                'previous_quantity' => NULL,
                'new_quantity' => 15,
                'change_date' => '2023-08-13 10:58:12',
                'change_reason' => NULL,
                'user_id' => 4,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-13 10:58:12',
                'updated_at' => '2023-08-13 10:58:12',
            ),
        ));
        
        
    }
}