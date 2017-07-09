<?php

use Illuminate\Database\Seeder;

class ProblemCodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
       DB::table('problem_codes')->insert([
            'id' => '1',
            'parent_code' => '0',
           'problem_description' => 'Missing or Incorrect Items',
           'final_problem' => '0',
        ]);
       
       DB::table('problem_codes')->insert([
            'id' => '2',
            'parent_code' => '0',
            'problem_description' => 'Food Quanlity',
            'final_problem' => '0',
        ]);
       
       DB::table('problem_codes')->insert([
            'id' => '3',
            'parent_code' => '0',
            'problem_description' => 'Devivery Timing',
            'final_problem' => '0',
        ]);
     
       DB::table('problem_codes')->insert([
            'id' => '4',
            'parent_code' => '0',
            'problem_description' => 'Devivery Service',
            'final_problem' => '0',
        ]);
       
       DB::table('problem_codes')->insert([
            'id' => '5',
            'parent_code' => '0',
            'problem_description' => 'Never Delivered',
            'final_problem' => '1', 
        ]);
       
       DB::table('problem_codes')->insert([
            'id' => '6',
            'parent_code' => '0',
            'problem_description' => 'Something Else',
            'final_problem' => '1',
        ]);
       
       /////////////////////////////////////////////////////////////////////////
       //id-01 Missing Item or incorrect items
       DB::table('problem_codes')->insert([
            'id' => '101',
            'parent_code' => '1',
            'problem_description' => "This item wasn't made correctly",
            'final_problem' => '0',
        ]);
       /////////////////
       DB::table('problem_codes')->insert([
            'id' => '10101',
            'parent_code' => '101',
            'problem_description' => "Options I selected weren't followed",
            'final_problem' => '1',
        ]);
       DB::table('problem_codes')->insert([
            'id' => '10102',
            'parent_code' => '101',
            'problem_description' => "Instruction I wrote weren't followed",
            'final_problem' => '1',
        ]);
       DB::table('problem_codes')->insert([
            'id' => '10103',
            'parent_code' => '101',
            'problem_description' => "The item was the wrong size",
            'final_problem' => '1',
        ]);
       
       DB::table('problem_codes')->insert([
            'id' => '102',
            'parent_code' => '1',
            'problem_description' => "This item never arrived",
            'final_problem' => '1',
        ]);
       
       DB::table('problem_codes')->insert([
            'id' => '103',
            'parent_code' => '1',
            'problem_description' => "item's side was missing or incorrect",
            'final_problem' => '1',
        ]);
       
       //////////////////////////////////////////////////////////////////////////
       //id-02 Food quanlity
       DB::table('problem_codes')->insert([
            'id' => '201',
            'parent_code' => '2',
            'problem_description' => "Food was overcooked or undercooked",
            'final_problem' => '1',
        ]);
       
       DB::table('problem_codes')->insert([
            'id' => '202',
            'parent_code' => '2',
            'problem_description' => "The item or ingredients weren't fresh",
            'final_problem' => '1',
        ]);
       
       DB::table('problem_codes')->insert([
            'id' => '203',
            'parent_code' => '3',
            'problem_description' => "Delivery has spilled or messey food",
            'final_problem' => '1',
        ]);
       
       DB::table('problem_codes')->insert([
            'id' => '204',
            'parent_code' => '4',
            'problem_description' => "The food arrived cold",
            'final_problem' => '1',
        ]);
       
       //////////////////////////////////////////////////////////////////////////
       //id-03 Food quanlity
       
       DB::table('problem_codes')->insert([
            'id' => '301',
            'parent_code' => '3',
            'problem_description' => "Too early",
            'final_problem' => '1',
        ]);
       
       DB::table('problem_codes')->insert([
            'id' => '302',
            'parent_code' => '3',
            'problem_description' => "Too late",
            'final_problem' => '1',
        ]);
       //////////////////////////////////////////////////////////////////////////
       //id-04 Delivery Service
       
       DB::table('problem_codes')->insert([
            'id' => '401',
            'parent_code' => '4',
            'problem_description' => "Carrier was rude or unprofessional",
            'final_problem' => '1',
        ]);
       
       DB::table('problem_codes')->insert([
            'id' => '402',
            'parent_code' => '4',
            'problem_description' => "Carrier didn't follow delivery instructions",
            'final_problem' => '1',
        ]);
       
    }
}
