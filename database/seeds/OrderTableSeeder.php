<?php

use Illuminate\Database\Seeder;
use App\Order;
use App\User;
use App\Seller;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        
        //Query sellers with at least one dish
        $sellers = DB::table('sellers')->join('dishes', 'sellers.id', '=', 'dishes.seller_id')->groupBy('seller_id')->pluck('seller_id')->toArray(); 
        $users = User::pluck("id")->toArray();
        $pickup_method = array('GROUP_PICKUP', 'DELIVER', 'STORE_PICKUP');
        $payment_method = array('STRIPE');
        $type = array('BID', 'REGULAR');
        $status = array('NEW', 'ACCEPTED', 'REJECTED', 'DELIVERED', 'COMPLETED');
        
        foreach (range(1, 200) as $index) {
            $sellerId = $faker->randomElement($sellers);
            $userId = $faker->randomElement($users);
            $PM = $pickup_method[random_int(0, 2)];
            $order = Order::create([
                        'type' => $type[random_int(0, 1)],
                        'deliver_fee' => $PM == 'DELIVER' ? $faker->randomFloat($nbMaxDecimals = 4, $min = 0, $max = 5): 0,
                        'total' => $faker->randomFloat($nbMaxDecimals = 4, $min = 30, $max = 100),
                        'pickup_time' => $faker->dateTimeThisMonth,
                        'pickup_type' => $PM,
                        'pickup_location_desc' => $PM == 'GROUP_PICKUP' ? $faker->title: null,
                        'address' => $faker->streetAddress,
                        'google_place_id' => $faker->uuid,
                        'payment_method' => $payment_method[random_int(0, 0)],
                        'complete_time' => $faker->dateTimeThisMonth,
                        'status' => $status[random_int(0, 4)],
                        'user_id' => $userId,
                        'seller_id' => $sellerId,
            ]);
        }
    }
}
