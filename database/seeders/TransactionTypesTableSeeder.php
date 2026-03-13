<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TransactionTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('transaction_types')->delete();
        
        \DB::table('transaction_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'transaction_type' => 'add',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 20:08:55',
                'updated_at' => '2023-08-12 20:08:55',
            ),
            1 => 
            array (
                'id' => 2,
                'transaction_type' => 'update',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 20:08:55',
                'updated_at' => '2023-08-12 20:08:55',
            ),
            2 => 
            array (
                'id' => 3,
                'transaction_type' => 'Edit',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 20:08:55',
                'updated_at' => '2023-08-12 20:08:55',
            ),
            3 => 
            array (
                'id' => 4,
                'transaction_type' => 'edit',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 20:08:55',
                'updated_at' => '2023-08-12 20:08:55',
            ),
            4 => 
            array (
                'id' => 5,
                'transaction_type' => 'create',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 20:08:55',
                'updated_at' => '2023-08-12 20:08:55',
            ),
            5 => 
            array (
                'id' => 6,
                'transaction_type' => 'Create',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 20:08:55',
                'updated_at' => '2023-08-12 20:08:55',
            ),
            6 => 
            array (
                'id' => 7,
                'transaction_type' => 'CREATE',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 20:08:55',
                'updated_at' => '2023-08-12 20:08:55',
            ),
            7 => 
            array (
                'id' => 8,
                'transaction_type' => 'EDIT',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 20:08:55',
                'updated_at' => '2023-08-12 20:08:55',
            ),
            8 => 
            array (
                'id' => 9,
                'transaction_type' => 'UPDATE',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 20:08:55',
                'updated_at' => '2023-08-12 20:08:55',
            ),
            9 => 
            array (
                'id' => 10,
                'transaction_type' => 'In',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 22:17:46',
                'updated_at' => '2023-08-12 22:17:46',
            ),
            10 => 
            array (
                'id' => 11,
                'transaction_type' => 'Checkout',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 22:17:54',
                'updated_at' => '2023-08-12 22:17:54',
            ),
            11 => 
            array (
                'id' => 12,
                'transaction_type' => 'IN',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 22:18:00',
                'updated_at' => '2023-08-12 22:18:00',
            ),
            12 => 
            array (
                'id' => 13,
                'transaction_type' => 'CHECKOUT',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 22:18:08',
                'updated_at' => '2023-08-12 22:18:08',
            ),
            13 => 
            array (
                'id' => 14,
                'transaction_type' => 'renew',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 22:18:08',
                'updated_at' => '2023-08-12 22:18:08',
            ),
            14 => 
            array (
                'id' => 15,
                'transaction_type' => 'Renew',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 22:18:08',
                'updated_at' => '2023-08-12 22:18:08',
            ),
            15 => 
            array (
                'id' => 16,
                'transaction_type' => 'diminish',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 22:18:08',
                'updated_at' => '2023-08-12 22:18:08',
            ),
            16 => 
            array (
                'id' => 17,
                'transaction_type' => 'Diminish',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 22:18:08',
                'updated_at' => '2023-08-12 22:18:08',
            ),
            17 => 
            array (
                'id' => 18,
                'transaction_type' => 'checkout - withdrawal',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 22:18:08',
                'updated_at' => '2023-08-12 22:18:08',
            ),
            18 => 
            array (
                'id' => 19,
                'transaction_type' => 'checkout - issuance',
                'transaction_type_description' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-12 22:18:08',
                'updated_at' => '2023-08-12 22:18:08',
            ),
        ));
    }
}