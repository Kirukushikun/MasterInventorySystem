<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('transactions')->delete();
        
        \DB::table('transactions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'item_id' => 1,
                'assigned_by_user_id' => 3,
                'assigned_user_id' => 3,
                'transaction_type_id' => 10,
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'quantity' => 2,
                'transaction_date' => '2023-08-12 23:27:58',
                'notes' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 23:27:58',
                'updated_at' => '2023-08-12 23:27:58',
            ),
            1 => 
            array (
                'id' => 2,
                'item_id' => 1,
                'assigned_by_user_id' => 4,
                'assigned_user_id' => 4,
                'transaction_type_id' => 10,
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'quantity' => 1,
                'transaction_date' => '2023-08-12 23:30:07',
                'notes' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 23:30:07',
                'updated_at' => '2023-08-12 23:30:07',
            ),
            2 => 
            array (
                'id' => 3,
                'item_id' => 1,
                'assigned_by_user_id' => 1,
                'assigned_user_id' => 1,
                'transaction_type_id' => 10,
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'quantity' => 4,
                'transaction_date' => '2023-08-12 23:41:59',
                'notes' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 23:41:59',
                'updated_at' => '2023-08-12 23:41:59',
            ),
            3 => 
            array (
                'id' => 4,
                'item_id' => 2,
                'assigned_by_user_id' => 3,
                'assigned_user_id' => 3,
                'transaction_type_id' => 10,
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'quantity' => 2,
                'transaction_date' => '2023-08-13 00:02:49',
                'notes' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-13 00:02:49',
                'updated_at' => '2023-08-13 00:02:49',
            ),
        ));
        
        
    }
}