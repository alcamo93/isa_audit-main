<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RiskSpecificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_risk_specifications')->insert([
            'specification' => 'La instalación no maneja sustancias químicas de igual o mayor cantidad al Apendice A de la NOM-028-STPS, la instalación ',
            'id_risk_consequence' => 1, 
            'id_status' => 1
        ]);
        \DB::table('t_risk_specifications')->insert([
            'specification' => 'La instalación no maneja sustancias químicas listadas en el primer y segundo listado de SEMARNAT (Nota: no se contempla en este punto cantidad)',
            'id_risk_consequence' => 1, 
            'id_status' => 1
        ]);

        \DB::table('t_risk_specifications')->insert([
            'specification' => 'La instalación no maneja sustancias químicas de igual o mayor cantidad al Apendice A de la NOM-028-STPS, la instalación ',
            'id_risk_consequence' => 2, 
            'id_status' => 1
        ]);
        \DB::table('t_risk_specifications')->insert([
            'specification' => 'La instalación no maneja sustancias químicas listadas en el primer y segundo listado de SEMARNAT (Nota: no se contempla en este punto cantidad). ',
            'id_risk_consequence' => 2, 
            'id_status' => 1
        ]);

        \DB::table('t_risk_specifications')->insert([
            'specification' => 'especificaciones de nivel de riego salud 1',
            'id_risk_consequence' => 3, 
            'id_status' => 1
        ]);
        \DB::table('t_risk_specifications')->insert([
            'specification' => 'especificaciones de nivel de riego salud 1',
            'id_risk_consequence' => 3, 
            'id_status' => 1
        ]);

        \DB::table('t_risk_specifications')->insert([
            'specification' => 'especificaciones de nivel de riego salud 2',
            'id_risk_consequence' => 4, 
            'id_status' => 1
        ]);
        \DB::table('t_risk_specifications')->insert([
            'specification' => 'especificaciones de nivel de riego salud 2',
            'id_risk_consequence' => 4, 
            'id_status' => 1
        ]);
    }
}