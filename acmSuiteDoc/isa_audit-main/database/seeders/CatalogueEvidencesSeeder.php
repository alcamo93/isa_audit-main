<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueEvidencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        \DB::table('c_evidences')->insert([
            'id_evidence' => 1,
            'evidence' => 'Tramite'
        ]);
        \DB::table('c_evidences')->insert([
            'id_evidence' => 2,
            'evidence' => 'Registro'
        ]);
        \DB::table('c_evidences')->insert([
            'id_evidence' => 3,
            'evidence' => 'Instalación (Físico)'
        ]);
        \DB::table('c_evidences')->insert([
            'id_evidence' => 4,
            'evidence' => 'Especifico'
        ]);
    }
}