<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseProductionV2_1 extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('periods')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->call([
            PeriodSeeder::class,
        ]);
    }
}