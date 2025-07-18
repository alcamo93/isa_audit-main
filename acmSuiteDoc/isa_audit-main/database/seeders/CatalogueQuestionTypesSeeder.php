<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueQuestionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_question_types')->insert(['question_type' => 'Identificación Federal']);
        \DB::table('c_question_types')->insert(['question_type' => 'Identificación Estatal']);
        \DB::table('c_question_types')->insert(['question_type' => 'Desbloqueo de requisitos']);
        \DB::table('c_question_types')->insert(['question_type' => 'Identificación Local']);
    }
}