<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueStatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		\DB::table('c_states')->insert(['state'=>'Aguascalientes','state_code'=>'Ags.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Baja California','state_code'=>'BC','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Baja California Sur','state_code'=>'BCS','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Campeche','state_code'=>'Camp.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Coahuila de Zaragoza','state_code'=>'Coah.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Colima','state_code'=>'Col.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Chiapas','state_code'=>'Chis.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Chihuahua','state_code'=>'Chih.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Ciudad de México','state_code'=>'CDMX','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Durango','state_code'=>'Dgo.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Guanajuato','state_code'=>'Gto.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Guerrero','state_code'=>'Gro.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Hidalgo','state_code'=>'Hgo.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Jalisco','state_code'=>'Jal.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'México','state_code'=>'Mex.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Michoacán de Ocampo','state_code'=>'Mich.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Morelos','state_code'=>'Mor.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Nayarit','state_code'=>'Nay.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Nuevo León','state_code'=>'NL','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Oaxaca','state_code'=>'Oax.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Puebla','state_code'=>'Pue.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Querétaro','state_code'=>'Qro.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Quintana Roo','state_code'=>'Q. Roo','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'San Luis Potosí','state_code'=>'SLP','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Sinaloa','state_code'=>'Sin.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Sonora','state_code'=>'Son.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Tabasco','state_code'=>'Tab.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Tamaulipas','state_code'=>'Tamps.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Tlaxcala','state_code'=>'Tlax.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Veracruz de Ignacio de la Llave','state_code'=>'Ver.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Yucatán','state_code'=>'Yuc.','id_country' => 1]); 
		\DB::table('c_states')->insert(['state'=>'Zacatecas','state_code'=>'Zac.','id_country' => 1]);
    }
}
