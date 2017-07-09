<?php

use Illuminate\Database\Seeder;

class ShoppingCartExtraTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker\Factory::create();
        $pickup_method = array('PICKUP', 'DELIVER');
        $shoppingCarts = DB::table('shopping_carts')->where('user_id',1)
                                     ->groupBy('seller_id')
                                     ->get();
        foreach($shoppingCarts as $shoppingCart){
            App\ShoppingCartExtra::create(array(
                    "user_id" => 1,
                    "seller_id" => $shoppingCart->seller_id,
                    "pickup_time" => $faker->dateTimeThisMonth,
                    'address' => $faker->streetAddress,
                    'google_place_id' => $faker->uuid,
                    "pickup_description" =>$pickup_method[random_int(0, 1)],
                    "extra_fee" => random_int(0,5),
                    ));
        }
    }
}
