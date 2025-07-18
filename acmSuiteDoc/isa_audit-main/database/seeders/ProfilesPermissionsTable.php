<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProfilesPermissionsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Module Administration
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 1, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 2, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 3, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 4, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 5, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 6, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 7, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 8, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 9, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        //Module Audit
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 10, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 11, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 12, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 13, 'id_submodule'=> null, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        // Submodules
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 8, 'id_submodule'=> 1, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 8, 'id_submodule'=> 2, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 8, 'id_submodule'=> 3, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 8, 'id_submodule'=> 4, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 8, 'id_submodule'=> 5, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 8, 'id_submodule'=> 6, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);
        \DB::table('t_profiles_permissions')->insert(['id_module'=> 8, 'id_submodule'=> 7, 'id_profile' => 1, 'visualize' => 1, 'create' => 1, 'modify' => 1, 'delete' => 1]);

    }
}
