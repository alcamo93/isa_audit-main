<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('c_status')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        \DB::table('c_status')->insert(['status' => 'Activo', 'group' => 1, 'key' => 'ACTIVE_GENERAL']);
        \DB::table('c_status')->insert(['status' => 'Inactivo', 'group' => 1, 'key' => 'INCATIVE_GENERAL']);
        \DB::table('c_status')->insert(['status' => 'Sin clasificar', 'group' => 2, 'key' => 'NOT_CLASSIFIED_APLICABILITY']);
        \DB::table('c_status')->insert(['status' => 'Clasificado', 'group' => 2, 'key' => 'CLASSIFIED_APLICABILITY']);
        \DB::table('c_status')->insert(['status' => 'Evaluando', 'group' => 2, 'key' => 'EVALUATING_APLICABILITY']);
        \DB::table('c_status')->insert(['status' => 'Finalizado', 'group' => 2, 'key' => 'FINISHED_APLICABILITY']);
        \DB::table('c_status')->insert(['status' => 'Sin auditar', 'group' => 3, 'key' => 'NOT_AUDITED_AUDIT']);
        \DB::table('c_status')->insert(['status' => 'Auditando', 'group' => 3, 'key' => 'AUDITING_AUDIT']);
        \DB::table('c_status')->insert(['status' => 'Auditado', 'group' => 3, 'key' => 'AUDITED_AUDIT']);
        \DB::table('c_status')->insert(['status' => 'Finalizado', 'group' => 3, 'key' => 'FINISHED_AUDIT_AUDIT']);
        \DB::table('c_status')->insert(['status' => 'No Iniciada', 'group' => 4, 'key' => 'NO_STARTED_TASK']);
        \DB::table('c_status')->insert(['status' => 'En Curso', 'group' => 4, 'key' => 'PROGRESS_TASK']);
        \DB::table('c_status')->insert(['status' => 'Sin Asignar', 'group' => 7, 'key' => 'UNASSIGNED_AP']);
        \DB::table('c_status')->insert(['status' => 'Vencido', 'group' => 4, 'key' => 'EXPIRED_TASK']);
        \DB::table('c_status')->insert(['status' => 'En Revisión', 'group' => 4, 'key' => 'REVIEW_TASK']);
        \DB::table('c_status')->insert(['status' => 'En Curso', 'group' => 7, 'key' => 'PROGRESS_AP']);
        \DB::table('c_status')->insert(['status' => 'Completado', 'group' => 7, 'key' => 'COMPLETED_AP']);
        \DB::table('c_status')->insert(['status' => 'En Revisión', 'group' => 7, 'key' => 'REVIEW_AP']);
        \DB::table('c_status')->insert(['status' => 'Aprobado', 'group' => 4, 'key' => 'APPROVED_TASK']);
        \DB::table('c_status')->insert(['status' => 'Sin Responsable', 'group' => 6, 'key' => 'NO_STARTED_OBLIGATION']);
        \DB::table('c_status')->insert(['status' => 'Por Vencer', 'group' => 6, 'key' => 'FOR_EXPIRED_OBLIGATION']);
        \DB::table('c_status')->insert(['status' => 'Vencido', 'group' => 6, 'key' => 'EXPIRED_OBLIGATION']);
        \DB::table('c_status')->insert(['status' => 'Vigente', 'group' => 6, 'key' => 'APPROVED_OBLIGATION']);
        \DB::table('c_status')->insert(['status' => 'Sin vigencia', 'group' => 6, 'key' => 'NO_DATES_OBLIGATION']);
        \DB::table('c_status')->insert(['status' => 'Vencido', 'group' => 7, 'key' => 'EXPIRED_AP']);
        \DB::table('c_status')->insert(['status' => 'Rechazado', 'group' => 4, 'key' => 'REJECTED_TASK']);
        \DB::table('c_status')->insert(['status' => 'Cerrado', 'group' => 7, 'key' => 'CLOSED_AP']);
        \DB::table('c_status')->insert(['status' => 'Sin Permiso/Documento', 'group' => 6, 'key' => 'NO_EVIDENCE_OBLIGATION']);
    }
}
