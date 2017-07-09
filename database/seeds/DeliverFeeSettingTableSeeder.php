<?php

use Illuminate\Database\Seeder;

class DeliverFeeSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        
        $deliverSettings = App\DeliverSetting::where('is_delivery_fee', '=', '1')->get();
        
        foreach ($deliverSettings as $setting) {
            $feeNum = random_int(1, 5);
            $rate = $faker->randomFloat($nbMaxDecimals = 2, $min = 0.2, $max = 1.1);
            
            for ($i=0; $i<$feeNum; $i++){
                $miles_within = $faker->randomFloat($nbMaxDecimals = 1, $min = 5, $max = 35);
                
                App\DeliverFeeSetting::create([
                    'seller_id' => $setting->seller_id,
                    'miles_within' => $miles_within,
                    'price' => $rate*$miles_within
                ]);
            }
        }
    }
}
