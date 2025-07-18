<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueAuditTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_audit_types')->insert(['audit_type' => 'Documental']);
        \DB::table('c_audit_types')->insert(['audit_type' => 'Física']);
        \DB::table('c_audit_types')->insert(['audit_type' => 'Documental/Física']);
    }
}