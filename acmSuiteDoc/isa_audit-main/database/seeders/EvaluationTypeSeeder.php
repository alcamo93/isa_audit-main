<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluationTypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('evaluation_types')->truncate();
    DB::table('evaluation_types')->insert(['name' => 'Auditoría']);
    DB::table('evaluation_types')->insert(['name' => 'Permisos críticos']);
    DB::table('evaluation_types')->insert(['name' => 'Auditoría/Permisos críticos']);
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  }
}