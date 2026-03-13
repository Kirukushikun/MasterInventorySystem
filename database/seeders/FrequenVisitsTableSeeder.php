<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FrequenVisitsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('frequen_visits')->delete();
        
        \DB::table('frequen_visits')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 4,
                'visit_count' => 3,
                'created_at' => '2023-08-13 00:33:53',
                'updated_at' => '2023-08-13 10:30:22',
            ),
        ));
        
        
    }
}