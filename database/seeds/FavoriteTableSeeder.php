<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Seller;
use App\User;
use App\Favorite;

class FavoriteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        
        $sellers = Seller::pluck("id")->toArray();
        $users = User::pluck("id")->toArray();
        foreach (range(1, 600) as $index) {
            repeat:
            $sellerId = $faker->randomElement($sellers);
            $userId = $faker->randomElement($users);
            try {
                Favorite::create([
                    "seller_id" => $sellerId,
                    "user_id" => $userId,
                ]);
           } catch (\Illuminate\Database\QueryException $e) {
                //look for integrity violation exception (23000)
                if($e->errorInfo[0]==23000)
                    goto repeat;
           }
        }
    }
}
