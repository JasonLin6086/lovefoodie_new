<?php

use Illuminate\Database\Seeder;

class DeliverSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        
        $sellers = App\Seller::all();
        foreach ($sellers as $seller) {               
            App\DeliverSetting::create([
                'seller_id' => $seller->id,
                'is_free_delivery' => $faker->boolean(), //random_int(0, 1),
                'free_delivery_price' => $faker->randomFloat($nbMaxDecimals = 6, $min = 100, $max = 500),
                'free_delivery_mile' => $faker->randomFloat($nbMaxDecimals = 4, $min = 0, $max = 50),
                'is_delivery_fee' => $faker->boolean(),
                'store_open_hour' => $faker->sentence,
                'is_at_store' => $faker->boolean(),
                'order_before_hour' => $faker->randomFloat($nbMaxDecimals = 4, $min = 1, $max = 50)
            ]);
        }
    }
}
