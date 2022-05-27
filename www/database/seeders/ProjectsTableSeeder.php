<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker::create();
        $companiesIDs = DB::table('companies')->pluck('id');
        $employeesIDs = DB::table('employees')->pluck('id');

        for ($i=0; $i < 100; $i++) { 
            DB::table('projects')->insert([
                'company_id' => $faker->randomElement($companiesIDs),
                'employee_id' => $faker->randomElement($employeesIDs),
                'title' => Str::random(10),
                'description' => Str::random(99),
           ]);
       }
    }
}
