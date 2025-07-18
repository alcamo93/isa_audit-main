<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeederAudit extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CatalogueConditionsSeeder::class,
            CatalogueMattersSeeder::class,
            CatalogueAspectsSeeder::class,
            CatalogueLegalClassificationSeeder::class,
            CatalogueApplicationTypesSeeder::class,
            CatalogueFrequenciesSeeder::class,
            CatalogueAuditTypesTableSeeder::class,
            GuidelinesSeeder::class,
            LegalBasisesSeeder::class,
            // QuestionsSeeder::class,
            // RequirementsSeeder::class,
            // SubrequirementsSeeder::class,
            // QuestionLegalBasiesSeeder::class,
            // RequirementsLegalBasiesSeeder::class,
            // SubrequirementsLegalBasiesSeeder::class,
            // QuestionRequirementsSeeder::class,
            AplicabilityRegistersSeeder::class,
            ContractsMattersSeeder::class,
            ContractsAspectsSeeder::class,
            // AuditRegistersSeeder::class,
            // AuditMattersSeeder::class,
            // AuditAspectsSeeder::class,
            // ActionRegisterSeeder::class,
            // ActionPlansSeeder::class,
            // TasksSeeder::class,
            // ObligationsSeeder::class,
        ]);
    }
}
