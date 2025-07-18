<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueSubmodulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Module Administration
        \DB::table('c_submodules')->insert(['name_submodule'=> 'Giros', 'initials_submodule' => 'GR','path' => 'catalogs/industries', 'id_module' => 8, 'id_status' => 1]);
        \DB::table('c_submodules')->insert(['name_submodule'=> 'Periodos de Acción', 'initials_submodule' => 'PA','path' => 'catalogs/periods', 'id_module' => 8, 'id_status' => 1]);
        \DB::table('c_submodules')->insert(['name_submodule'=> 'Materias Legales', 'initials_submodule' => 'ML','path' => 'catalogs/matters', 'id_module' => 8, 'id_status' => 1]);
        \DB::table('c_submodules')->insert(['name_submodule'=> 'Fundamentos Legales', 'initials_submodule' => 'FL','path' => 'catalogs/legals', 'id_module' => 8, 'id_status' => 1]);
        \DB::table('c_submodules')->insert(['name_submodule'=> 'Requerimientos Auditoria', 'initials_submodule' => 'RA','path' => 'catalogs/requirements', 'id_module' => 8, 'id_status' => 1]);
        \DB::table('c_submodules')->insert(['name_submodule'=> 'Cuestionario Aplicabilidad', 'initials_submodule' => 'CA','path' => 'catalogs/questions', 'id_module' => 8, 'id_status' => 1]);
        \DB::table('c_submodules')->insert(['name_submodule'=> 'Nivel de Riesgo', 'initials_submodule' => 'NR','path' => 'catalogs/risks', 'id_module' => 8, 'id_status' => 1]);
        \DB::table('c_submodules')->insert(['name_submodule'=> 'Formularios', 'initials_submodule' => 'FM','path' => 'catalogs/forms', 'id_module' => 8, 'id_status' => 1]);
    }
}
