<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('topics')->insert([
            'name' => 'Tema 1',
        ]);
        \DB::table('topics')->insert([
            'name' => 'Tema 2',
        ]);
        \DB::table('topics')->insert([
            'name' => 'Tema 3',
        ]);
    }
}
