<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ActionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        \DB::table('t_action_plans')->insert([
            'init_date' =>  '2020-04-01 00:00:00',
            'close_date' =>  '2020-04-01 23:59:59',
            'real_close_date' =>  '2020-04-02 23:59:59',
            'id_user' =>  NULL,
            'finding' => 'Hallazgo 1',
            'id_contract' => 1,
            'id_aspect' => 1,
            'id_requirement' => 1,
            'id_subrequirement' => NULL,
            'id_recomendation' => NULL,
            'id_action_register' => 1,
            'id_user_asigned' => NULL,
            'total_tasks' => 2
        ]);
        \DB::table('t_action_plans')->insert([
            'init_date' =>  '2020-04-01 00:00:00',
            'close_date' =>  '2020-04-15 23:59:59',
            'real_close_date' =>  '2020-04-17 23:59:59',
            'id_user' =>  NULL,
            'finding' => 'Hallazgo 1',
            'id_contract' => 1,
            'id_aspect' => 1,
            'id_requirement' => 2,
            'id_subrequirement' => NULL,
            'id_recomendation' => NULL,
            'id_action_register' => 1,
            'id_user_asigned' => NULL,
            'total_tasks' => 3
        ]);
        \DB::table('t_action_plans')->insert([
            'init_date' =>  '2020-04-01 00:00:00',
            'close_date' =>  '2020-04-30 23:59:59',
            'real_close_date' =>  '2020-05-02 23:59:59',
            'id_user' =>  1,
            'id_user' =>  NULL,
            'finding' => 'Hallazgo 1',
            'id_contract' => 1,
            'id_aspect' => 1,
            'id_requirement' =>  6,
            'id_subrequirement' => 1,
            'id_recomendation' => NULL,
            'id_action_register' => 1,
            'id_user_asigned' => NULL,
            'total_tasks' => 1
        ]);
        \DB::table('t_action_plans')->insert([
            'init_date' =>  '2020-04-01 00:00:00',
            'close_date' =>  '2020-04-30 23:59:59',
            'real_close_date' =>  '2020-05-02 23:59:59',
            'id_user' =>  1,
            'id_user' =>  NULL,
            'finding' => 'Hallazgo 1',
            'id_contract' => 1,
            'id_aspect' => 1,
            'id_requirement' =>  6,
            'id_subrequirement' => 2,
            'id_recomendation' => NULL,
            'id_action_register' => 1,
            'id_user_asigned' => NULL,
            'total_tasks' => 1
        ]);
        \DB::table('t_action_plans')->insert([
            'init_date' =>  '2020-04-01 00:00:00',
            'close_date' =>  '2020-06-30 23:59:59',
            'real_close_date' =>  '2020-07-02 23:59:59',
            'id_user' =>  1,
            'id_user' =>  NULL,
            'finding' => 'Hallazgo 1',
            'id_contract' => 1,
            'id_aspect' => 1,
            'id_requirement' => 8,
            'id_subrequirement' => NULL,
            'id_recomendation' => NULL,
            'id_action_register' => 1,
            'id_user_asigned' => NULL,
            'total_tasks' => 1
        ]);
        \DB::table('t_action_plans')->insert([
            'init_date' =>  '2020-04-01 00:00:00',
            'close_date' =>  '2020-04-30 23:59:59',
            'real_close_date' =>  '2020-05-02 23:59:59',
            'id_user' =>  1,
            'id_user' =>  NULL,
            'finding' => 'Hallazgo 1',
            'id_contract' => 1,
            'id_aspect' => 1,
            'id_requirement' =>  6,
            'id_recomendation' => NULL,
            'id_action_register' => 1,
            'id_user_asigned' => NULL,
        ]);
    }
}