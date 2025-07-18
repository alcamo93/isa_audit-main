<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RiskConsequencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_risk_consequences')->insert([
            'id_risk_Consequence' => 1,
            'name_consequence' => 'No hay fuga o derrame externo. FUGAS O DERRAMES INTERNOS DE SUSTANCIAS QUIMICAS CONTROLABLES.. SIN NECESIDAD DE REMEDACIÓN.', 
            'consequence' => 1,
            'id_risk_category' => 1,
            'id_status' => 1
        ]);
        \DB::table('t_risk_consequences')->insert([
            'id_risk_Consequence' => 2, 
            'name_consequence' =>  'Fuga o derrame externo que se pueda controlar en menos de una hora (incluyendo el tiempo para detectar). Fugas o derrames de sustancias quimicas de forma interna que puedan implicar acciones de remedación', 
            'consequence' => 5,
            'id_risk_category' => 1,
            'id_status' => 1
        ]);

        \DB::table('t_risk_consequences')->insert([
            'id_risk_Consequence' => 3,
            'name_consequence' => 'No se esperan heridas o daños físicos. En caso de lesiones estas son leves tales como:  como cortadas, quemaduras, contusiones menores sin necesidad de primeros auxilios', 
            'consequence' => 1,
            'id_risk_category' => 2,
            'id_status' => 1
        ]);
        \DB::table('t_risk_consequences')->insert([
            'id_risk_Consequence' => 4, 
            'name_consequence' =>  'Interno: Heridas o daños físicos reportables y/o que se atienden con primeros auxilios. LESIONES MENORES. Lesiones como cortadas, quemaduras, contusiones menores que requieren primeros auxilios o consulta externa mas no incapacidad En caso Externo: No se esperan heridas o daños físicos. Ruido, olores e impacto visual imperceptibles. ', 
            'consequence' => 5,
            'id_risk_category' => 2,
            'id_status' => 1
        ]);
    }
}