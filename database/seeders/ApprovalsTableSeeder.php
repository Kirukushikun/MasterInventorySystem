<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ApprovalsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('approvals')->delete();

        \DB::table('approvals')->insert(array (
            0 =>
            array (
                'id' => 1,
                'title' => 'ITEM CHECKED OUT',
                'description' => 'ITEM CHECKED OUT',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-07 06:17:26',
                'updated_at' => '2023-08-07 06:17:26',
            ),
            1 =>
            array (
                'id' => 2,
                'title' => 'FOR RELEASE',
                'description' => 'FOR RELEASE',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-07 06:18:03',
                'updated_at' => '2023-08-07 06:18:03',
            ),
            2 =>
            array (
                'id' => 3,
                'title' => 'IN-TRANSIT',
                'description' => 'IN-TRANSIT',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-07 06:18:52',
                'updated_at' => '2023-08-07 06:18:52',
            ),
            3 =>
            array (
                'id' => 4,
                'title' => 'RECEIVED',
                'description' => 'RECEIVED',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-07 06:19:29',
                'updated_at' => '2023-08-07 06:19:29',
            ),
            4 =>
            array (
                'id' => 5,
                'title' => 'FOR APPROVAL',
                'description' => 'FOR APPROVAL',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-07 06:20:02',
                'updated_at' => '2023-08-07 06:20:02',
            ),
            5 =>
            array (
                'id' => 6,
                'title' => 'DENIED',
                'description' => 'DENIED',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-14 14:05:10',
                'updated_at' => '2023-08-14 14:05:10',
            ),
            6 =>
            array (
                'id' => 7,
                'title' => 'APPROVED',
                'description' => 'APPROVED',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-14 14:05:19',
                'updated_at' => '2023-08-14 14:05:19',
            ),
            7 =>
            array (
                'id' => 8,
                'title' => 'ITEM PARTIALLY CHECKED OUT',
                'description' => 'ITEM CHECKED OUT',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-07 06:17:26',
                'updated_at' => '2023-08-07 06:17:26',
            ),
            8 =>
            array (
                'id' => 9,
                'title' => 'ITEM FULLY CHECKED OUT',
                'description' => 'ITEM CHECKED OUT',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-07 06:17:26',
                'updated_at' => '2023-08-07 06:17:26',
            ),
            9 =>
            array (
                'id' => 10,
                'title' => 'ITEM PARTIALLY CHECKED OUT ARE RECEIVED',
                'description' => 'ITEM PARTIALLY CHECKED OUT ARE RECEIVED',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-07 06:17:26',
                'updated_at' => '2023-08-07 06:17:26',
            ),
            10 =>
            array (
                'id' => 11,
                'title' => 'ITEM PARTIALLY CHECKED OUT ARE FOR RELEASE',
                'description' => 'ITEM PARTIALLY CHECKED OUT ARE FOR RELEASE',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-07 06:17:26',
                'updated_at' => '2023-08-07 06:17:26',
            ),
            11 =>
            array (
                'id' => 12,
                'title' => 'ITEM PARTIALLY CHECKED OUT ARE IN-TRANSIT',
                'description' => 'ITEM PARTIALLY CHECKED OUT ARE IN-TRANSIT',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-07 06:17:26',
                'updated_at' => '2023-08-07 06:17:26',
            ),
        ));


    }
}
