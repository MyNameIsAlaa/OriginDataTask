<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\CompaniesTableSeeder;
use Database\Seeders\EmployeesTableSeeder;
use Database\Seeders\ProjectsTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UsersTableSeeder::class);
         $this->call(CompaniesTableSeeder::class);
         $this->call(EmployeesTableSeeder::class);
         $this->call(ProjectsTableSeeder::class);
    }
}
