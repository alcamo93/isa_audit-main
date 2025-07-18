<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseProductionTruncateComplete extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // system customers
        DB::table('t_contracts')->truncate();
        DB::table('t_contracts_extends')->truncate();
        DB::table('t_contract_details')->truncate();
        DB::table('t_users')->truncate();
        DB::table('t_people')->truncate();
        DB::table('t_profiles')->truncate();
        DB::table('t_profiles_permissions')->truncate();
        DB::table('t_customers')->truncate();
        DB::table('t_corporates')->truncate();
        DB::table('t_addresses')->truncate();
        DB::table('t_contacts')->truncate();
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
        // questions
        DB::table('r_question_legal_basies')->truncate();
        DB::table('r_question_requirements')->truncate();
        DB::table('t_answers_question')->truncate();
        DB::table('t_questions')->truncate();
        // requirements 
        DB::table('t_requirements')->truncate();
        DB::table('r_obligation_requirements')->truncate();
        DB::table('r_question_requirements')->truncate();
        DB::table('r_requirements_legal_basies')->truncate();
        DB::table('r_subrequirements_legal_basies')->truncate();
        DB::table('t_requirements')->truncate();
        DB::table('t_requirement_recomendations')->truncate();
        DB::table('t_subrequirements')->truncate();
        DB::table('t_subrequirement_recomendations')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            CustomersTableSeeder::class,
            CorporatesTableSeeder::class,
            AddressesTableSeeder::class,
            ProfilesTypesTableSeeder::class,
            ProfilesTableSeeder::class,
            PeopleTableSeeder::class,
            UsersTableSeeder::class,
            ProfilesPermissionsTable::class,

            CatalogueRiskCategoriesSeeder::class,
            RiskInterpretationsSeeder::class,
            RiskAttributesSeeder::class,
            RiskHelpSeeder::class
        ]);
    }
}