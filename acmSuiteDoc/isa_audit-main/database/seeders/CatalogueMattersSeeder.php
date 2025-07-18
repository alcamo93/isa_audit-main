<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\V2\Catalogs\Matter;

class CatalogueMattersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // \DB::table('c_matters')->truncate();
        // \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $matters = [
            [
                'matter' => 'Ambiental',
                'description' => 'Ambiental',
                'image' => 'images/matter_1.svg',
                'color' => '#4eaf8f'
            ],
            [
                'matter' => 'Seguridad & Higiene',
                'description' => 'Seguridad & Higiene',
                'image' => 'images/matter_2.svg',
                'color' => '#113C53'
            ],
            [
                'matter' => 'Salud',
                'description' => 'Salud',
                'image' => 'images/matter_3.svg',
                'color' => '#2581cc'
            ],
            [
                'matter' => 'Protección civil',
                'description' => 'Protección civil',
                'image' => 'images/matter_4.svg',
                'color' => '#7F7F7F'
            ]
        ];

        foreach ($matters as $matter) {
            Matter::updateOrCreate(
                ['matter' => $matter['matter']],
                $matter,
            );
        }
    }
}