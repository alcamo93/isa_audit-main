<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\V2\Catalogs\Period;

class PeriodSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  { 
    $records = [
      [
        'name' => 'Día',
        'key' => 'days',
      ],
      [
        'name' => 'Meses',
        'key' => 'months',
      ],
      [
        'name' => 'Años',
        'key' => 'years',
      ]
    ];
    Period::insert($records);
  }
}