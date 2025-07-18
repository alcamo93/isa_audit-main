<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseProductionTruncate extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // system
        DB::table('c_modules')->truncate();
        DB::table('c_submodules')->truncate();
        DB::table('c_question_types')->truncate();
        DB::table('c_requirement_types')->truncate();
        DB::table('c_application_types')->truncate();
        // risks
        DB::table('c_risk_categories')->truncate();
        DB::table('t_risk_answers')->truncate();
        DB::table('t_risk_consequences')->truncate();
        DB::table('t_risk_exhibitions')->truncate();
        DB::table('t_risk_help')->truncate();
        DB::table('t_risk_interpretations')->truncate();
        DB::table('t_risk_probabilities')->truncate();
        DB::table('t_risk_specifications')->truncate();
        DB::table('t_risk_totals')->truncate();
        // all system functions
        DB::table('notifications')->truncate();
        DB::table('t_news')->truncate();
        DB::table('t_files_den')->truncate();
        DB::table('t_comments')->truncate();
        DB::table('t_reminders')->truncate();
        DB::table('t_obligations')->truncate();
        DB::table('t_tasks')->truncate();
        DB::table('t_action_plans')->truncate();
        DB::table('t_action_registers')->truncate();
        DB::table('r_audit_aspects')->truncate();
        DB::table('r_audit_matters')->truncate();
        DB::table('t_audit_registers')->truncate();
        DB::table('t_audit')->truncate();
        DB::table('t_aplicability')->truncate();
        DB::table('r_contract_aspects')->truncate();
        DB::table('r_contract_matters')->truncate();
        DB::table('t_aplicability_registers')->truncate();
        DB::table('r_question_legal_basies')->truncate();
        DB::table('t_contracts')->truncate();
        DB::table('t_contract_details')->truncate();
        DB::table('t_contracts_extends')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}