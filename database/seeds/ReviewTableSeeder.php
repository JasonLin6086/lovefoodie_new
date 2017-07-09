<?php

use Illuminate\Database\Seeder;

class ReviewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //$reviews = factory(App\Review::class,300)->create();
        $faker = Faker\Factory::create();

        $dishes = App\Dish::pluck("id")->toArray();
        $users = App\User::pluck("id")->toArray();
        
        foreach (range(1, 800) as $index) {
            repeat:
            $dish = App\Dish::find($faker->randomElement($dishes));
            $userId = $faker->randomElement($users);
            try {
                App\Review::create([
                    'rating' => $faker-> randomFloat($nbMaxDecimals = 2, $min = 0, $max = 5),
                    'description' => $faker->sentence,
                    'dish_id' => $dish->id,
                    'seller_id' => $dish->seller_id,
                    'user_id' => $userId,
                ]);
           } catch (\Illuminate\Database\QueryException $e) {
                    //look for integrity violation exception (23000)
                    if($e->errorInfo[0]==23000)
                      goto repeat;
           }
        }   
    }
}
