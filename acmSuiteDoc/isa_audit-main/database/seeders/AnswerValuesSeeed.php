<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AnswerValuesSeeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_answer_values')->insert([
            'id_answer_value' => 1,
            'answer_value' => 'Afirmativo'
        ]);
        \DB::table('t_answer_values')->insert([
            'id_answer_value' => 2,
            'answer_value' => 'Negativo'
        ]);
    }
}
