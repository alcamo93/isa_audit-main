<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ObligationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        \DB::table('t_obligations')->insert([
            'obligation' => 'Levantamiento de información para el requerimiento AMB-IRA-01' ,
            'init_date' => '2020-04-01 00:00:00' ,
            'renewal_date' => '2022-03-31 23:59:59' ,
            'id_status' => 11 ,
            'id_period' => 4 ,
            'id_condition' => 1 ,
            'id_action_register' => 1 ,
            'id_user' => 5 ,
        ]);
        \DB::table('t_obligations')->insert([
            'obligation' => 'Entrega de documentación para el requerimiento AMB-IRA-01' ,
            'init_date' => '2020-05-01 00:00:00',
            'renewal_date' => '2020-05-15 23:59:59',
            'id_status' => 11 ,
            'id_period' => 2 ,
            'id_condition' => 1 ,
            'id_action_register' => 1 ,
            'id_user' => 5 ,
        ]);
    }
}