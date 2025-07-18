<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeederReset extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CustomersTableSeeder::class, 
            CorporatesTableSeeder::class,
            AddressesTableSeeder::class,
            ProfilesTableSeeder::class,
            PeopleTableSeeder::class,
            UsersTableSeeder::class,
            ProfilesPermissionsTable::class
        ]);
    }
}
