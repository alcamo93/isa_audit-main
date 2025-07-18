<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_users')->insert(['id_person' => 1, 'email' => 'admin@isaambiental.com', 'password' => '$2y$10$W1ioUZP5Y6T2K4rxcuckUubfjtzIV5Y/1lRX1IhA7NBzZNZPE0.Ci', 'secondary_email' => 'noreply@isaambiental.com', 'id_customer' => 1, 'id_corporate' => 1, 'id_profile'=> 1, 'id_status' => 1]);
        \DB::table('t_users')->insert(['id_person' => 2, 'email' => 'acerda@isaambiental.com', 'password' => '$2y$10$gL0Wcp/r8cIWTog9kTvuEekDLJRkBOrE5bvmcrcWFjxqU/8vpJB0i', 'id_customer' => 1, 'id_corporate' => 1, 'id_profile'=> 1, 'id_status' => 1]);
        \DB::table('t_users')->insert(['id_person' => 3, 'email' => 'mrichkarday@isaambiental.com', 'password' => '$2y$10$8txOBVtfZScoympIQoA.2uMQaQ4s8HdiRIN7XVmR6SfO0I/fXscGO', 'id_customer' => 1, 'id_corporate' => 1, 'id_profile'=> 1, 'id_status' => 1]);
    }
}
