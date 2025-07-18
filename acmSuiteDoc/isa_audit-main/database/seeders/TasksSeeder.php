<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_tasks')->insert([
            'id_action_plan' => 1,
            'id_period' => 2,
            'task' => 'Tarea 1',
            'title' => 'Tarea 01-AP-20 ',
            'id_user' => null
        ]);
        \DB::table('t_tasks')->insert([
            'id_action_plan' => 1,
            'id_period' => 2,
            'task' => 'Tarea 2',
            'title' => 'Tarea 02-AP-20 ',
            'id_user' => null
        ]);
        \DB::table('t_tasks')->insert([
            'id_action_plan' => 2,
            'id_period' => 2,
            'task' => 'Tarea 1',
            'title' => 'Tarea 01-AP-20 ',
            'id_user' => null
        ]);
        \DB::table('t_tasks')->insert([
            'id_action_plan' => 2,
            'id_period' => 2,
            'task' => 'Tarea 2',
            'title' => 'Tarea 02-AP-20 ',
            'id_user' => null
        ]);
        \DB::table('t_tasks')->insert([
            'id_action_plan' => 2,
            'id_period' => 2,
            'task' => 'Tarea 3',
            'title' => 'Tarea 03-AP-20 ',
            'id_user' => null
        ]);
        \DB::table('t_tasks')->insert([
            'id_action_plan' => 3,
            'id_period' => 2,
            'task' => 'Tarea 1',
            'title' => 'Tarea 01-AP-20 ',
            'id_user' => null
        ]);
        \DB::table('t_tasks')->insert([
            'id_action_plan' => 4,
            'id_period' => 2,
            'task' => 'Tarea 1',
            'title' => 'Tarea 01-AP-20 ',
            'id_user' => null
        ]);
        \DB::table('t_tasks')->insert([
            'id_action_plan' => 5,
            'id_period' => 2,
            'task' => 'Tarea 1',
            'title' => 'Tarea 01-AP-20 ',
            'id_user' => null
        ]);
    }
}