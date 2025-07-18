<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PeopleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_people')->insert(['first_name'=> 'Administrador','second_name' => 'ISA', 'last_name' => 'Ambiental', 'rfc' => 'ISAA800825569', 'gender'=> 'Masculino', 'phone'=> '5513452678', 'birthdate' => '1990-01-01']);
        \DB::table('t_people')->insert(['first_name'=> 'Angelina','second_name' => 'Cerda', 'last_name' => '', 'rfc' => 'ACE800825569', 'gender'=> 'Femenino', 'phone'=> '5559870198', 'birthdate' => '1990-01-01']);
        \DB::table('t_people')->insert(['first_name'=> 'Mariana','second_name' => 'Richkarday', 'last_name' => 'Lozano', 'rfc' => 'MRL800825569', 'gender'=> 'Femenino', 'phone'=> '5555670197', 'birthdate' => '1990-01-01']);
    }
}
