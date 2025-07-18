<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CataloguePrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    public function run()
    {
        DB::table('c_priority')->insert(['priority' => 'Baja']);
        DB::table('c_priority')->insert(['priority' => 'Media']);
        DB::table('c_priority')->insert(['priority' => 'Alta']);
    }
}
