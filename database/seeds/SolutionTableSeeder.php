<?php

use Illuminate\Database\Seeder;

class SolutionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('solutions')->insert([
            'problem_code_id' => '10101',
            'solution_description' => "return 5% of the tatol to users' credit",
        ]);
        DB::table('solutions')->insert([
            'problem_code_id' => '10101',
            'solution_description' => "return 5% of the tatol to users' credit",
        ]);
        DB::table('solutions')->insert([
            'problem_code_id' => '10101',
            'solution_description' => "return 5% of the tatol to users' credit",
        ]);
        DB::table('solutions')->insert([
            'problem_code_id' => '10101',
            'solution_description' => "return 5% of the tatol to users' credit",
        ]);
        DB::table('solutions')->insert([
            'problem_code_id' => '10101',
            'solution_description' => "return 5% of the tatol to users' credit",
        ]);
        DB::table('solutions')->insert([
            'problem_code_id' => '10101',
            'solution_description' => "return 5% of the tatol to users' credit",
        ]);
    }
}
