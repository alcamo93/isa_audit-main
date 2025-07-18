<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_profiles')->insert(['profile_name'=> 'Super Administrador', 'id_profile_type' => 1, 'id_status' => 1, 'id_customer' => 1, 'id_corporate' => 1]);
    }
}
