<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogueCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('c_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('c_categories')->insert(['category' => 'Autorización']);
        DB::table('c_categories')->insert(['category' => 'Permiso']);
        DB::table('c_categories')->insert(['category' => 'Licencia']);
        DB::table('c_categories')->insert(['category' => 'Uso de Suelo']);
        DB::table('c_categories')->insert(['category' => 'Estudio']);
        DB::table('c_categories')->insert(['category' => 'Programa']);
        DB::table('c_categories')->insert(['category' => 'Plan']);
        DB::table('c_categories')->insert(['category' => 'Registro']);
        DB::table('c_categories')->insert(['category' => 'Informe ']);
        DB::table('c_categories')->insert(['category' => 'Expediente']);
        DB::table('c_categories')->insert(['category' => 'Reporte de laboratorio']);
        DB::table('c_categories')->insert(['category' => 'Bitácora']);
        DB::table('c_categories')->insert(['category' => 'Diploma (s)']);
        DB::table('c_categories')->insert(['category' => 'DC-3']);
        DB::table('c_categories')->insert(['category' => 'Dictamen']);
        DB::table('c_categories')->insert(['category' => 'Certificado']);
        DB::table('c_categories')->insert(['category' => 'Notificación/Aviso']);
        DB::table('c_categories')->insert(['category' => 'Estudio Ambiental ']);
        DB::table('c_categories')->insert(['category' => 'Estudio de Impacto Ambiental']);
        DB::table('c_categories')->insert(['category' => 'Estudio de Riesgo Ambiental']);
        DB::table('c_categories')->insert(['category' => 'Licencia de Anuncio']);
        DB::table('c_categories')->insert(['category' => 'Licencia de Funcionamiento (Operación)']);
        DB::table('c_categories')->insert(['category' => 'Residuos Peligrosos']);
        DB::table('c_categories')->insert(['category' => 'Residuos No Peligrosos']);
        DB::table('c_categories')->insert(['category' => 'Descarga de Agua Residual']);
        DB::table('c_categories')->insert(['category' => 'Emisiones a la Atmósfera']);
        DB::table('c_categories')->insert(['category' => 'Ruido Ambiental']);
        DB::table('c_categories')->insert(['category' => 'COA ']);
        DB::table('c_categories')->insert(['category' => 'Estudio de Riesgo de Incendio']);
        DB::table('c_categories')->insert(['category' => 'Estudio de Riesgo por Maquinaria Y Equipo']);
        DB::table('c_categories')->insert(['category' => 'Estudio de Riesgo por Sustancias Químicas']);
        DB::table('c_categories')->insert(['category' => 'Monitoreo de Sustancias Químicas']);
        DB::table('c_categories')->insert(['category' => 'Monitoreo de Ruido Laboral']);
        DB::table('c_categories')->insert(['category' => 'Radiaciones Ionizantes']);
        DB::table('c_categories')->insert(['category' => 'Radiaciones No Ionizante']);
        DB::table('c_categories')->insert(['category' => 'Temperaturas Elevadas / Abatidas']);
        DB::table('c_categories')->insert(['category' => 'Estudio de Equipo de Protección Personal']);
        DB::table('c_categories')->insert(['category' => 'Comisión de Seguridad E Higiene']);
        DB::table('c_categories')->insert(['category' => 'Recipientes Sujetos A Presión']);
        DB::table('c_categories')->insert(['category' => 'Historial de Accidentes']);
        DB::table('c_categories')->insert(['category' => 'Monitoreo de Tierras Físicas']);
        DB::table('c_categories')->insert(['category' => 'Monitoreo de Vibraciones']);
        DB::table('c_categories')->insert(['category' => 'Monitoreo de Iluminación']);
        DB::table('c_categories')->insert(['category' => 'Estudio de Riesgo Por Corte Y Soldadura']);
        DB::table('c_categories')->insert(['category' => 'Estudio de Riesgo Eléctrico']);
        DB::table('c_categories')->insert(['category' => 'Diagnóstico de Seguridad Y Salud']);
        DB::table('c_categories')->insert(['category' => 'Estudio de Riesgo Por Factores Psicosociales']);
        DB::table('c_categories')->insert(['category' => 'Estudio de Riesgo de Ergonomía']);
        DB::table('c_categories')->insert(['category' => 'Plan de Contingencias (Capacitación A Brigadas)']);
        DB::table('c_categories')->insert(['category' => 'Plan de Capacitación']);
    }
}
