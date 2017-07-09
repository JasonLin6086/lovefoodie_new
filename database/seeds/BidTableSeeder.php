<?php

use Illuminate\Database\Seeder;
use App\Seller;
use App\Wish;
use App\Bid;

class BidTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$bids = factory(App\Bid::class, 80)->create();
        $faker = Faker\Factory::create();

        $sellers = Seller::pluck("id")->toArray();
        $wishes = Wish::pluck("id")->toArray();
        foreach (range(1, 200) as $index) {
            repeat:
            $sellerId = $faker->randomElement($sellers);
            $wishId = $faker->randomElement($wishes);
            try {
                Bid::create([
                    'seller_id' => $sellerId,
                    'wish_id' => $wishId,
                    'bid_price' => $faker -> randomFloat($nbMaxDecimals = 4, $min = 6, $max = 20),
                    'bid_description' => $faker-> sentence,
                ]);
           } catch (\Illuminate\Database\QueryException $e) {
                    //look for integrity violation exception (23000)
                    if($e->errorInfo[0]==23000)
                      goto repeat;
           }
        }   
    }
}
