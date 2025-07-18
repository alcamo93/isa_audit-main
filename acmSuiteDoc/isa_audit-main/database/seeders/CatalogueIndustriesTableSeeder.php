<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueIndustriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_industries')->insert(['industry' => 'Maquila', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Automotriz', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Química', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Petróleo y petroquímica', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Pinturas y tintas', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Celulosa y papel', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Metalúrgica', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Vidrio', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Generación de energía eléctrica', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Asbesto', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Cementera y calera', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Tratamiento de residuos peligrosos', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Manufactura', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Industria pesada', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Siderúrgicas', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Alimentos', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Textil', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Farmacéutica', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Robótica', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Informática', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Mecánica', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Aeroespacial', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Astronáutica', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Hidráulica', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Comercial', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Construcción', 'id_status' => 1]);
        \DB::table('c_industries')->insert(['industry' => 'Cervecera', 'id_status' => 1]);
    }
}
