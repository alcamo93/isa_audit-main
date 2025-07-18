<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CatalogueStatusTableSeeder::class,
            CatalogueModulesSeeder::class,
            CatalogueSubmodulesSeeder::class,
            CatalogueCountriesTableSeeder::class,
            CatalogueStatesTableSeeder::class,
            CatalogueCitiesTableSeeder::class,
            CatalogueIndustriesTableSeeder::class,
            CataloguePeriodsSeeder::class,
            CatalogueRequirementTypesSeeder::class,
            CatalogueQuestionTypesSeeder::class,
            CustomersTableSeeder::class,
            CorporatesTableSeeder::class,
            AddressesTableSeeder::class,
            ProfilesTypesTableSeeder::class,
            ProfilesTableSeeder::class,
            PeopleTableSeeder::class,
            UsersTableSeeder::class,
            ProfilesPermissionsTable::class,
            LicensesSeeder::class,
            ContractsSeeder::class,
            ContractDetailsSeeder::class,
            //audit db
            DatabaseSeederAudit::class,
        ]);
    }
}
