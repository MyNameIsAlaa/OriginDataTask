<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class EmployeesTableSeeder extends Seeder
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

        for ($i=0; $i < 100; $i++) { 
            DB::table('employees')->insert([
                'company_id' => $faker->randomElement($companiesIDs),
                'first_name' => Str::random(10),
                'last_name' => Str::random(10),
                'email' => Str::random(5).'@gmail.com',
                'phone' => $faker->numerify('##########')
           ]);
       }


    }
}
