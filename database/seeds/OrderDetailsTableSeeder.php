<?php

use Illuminate\Database\Seeder;
use App\Order;
use App\OrderDetail;
use App\Dish;

class OrderDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$orderdetails = factory(App\OrderDetail::class, 50)->create();
        $faker = Faker\Factory::create();
        $orders = Order::all();
        foreach ($orders as $order) {
            $dishIds = Dish::where('seller_id', '=', $order->seller_id)->pluck("id")->toArray();
            $num = random_int(1, sizeof($dishIds));    
            $sum = 0;
            $seen = [];
            foreach  (range(1, $num) as $index) {
                Repeat:
                
                $dish_id = $faker->randomElement($dishIds);
                if(in_array($dish_id, $seen)){ goto Repeat; }else{ array_push($seen, $dish_id); }
                
                $quantity = random_int(1,7);
                $unit_price = Dish::where('id', '=',$dish_id)->first()->price;
                $total_price = $quantity*$unit_price;
                $orderdetail = OrderDetail::create([
                    'dish_name' => $faker->word,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'total_price' => $total_price,
                    'order_id' => $order->id,
                    'dish_id' => $dish_id,
                ]);
                
                $sum += $total_price;
            }
            
            $order->update(['total' => $sum]);
        }
    }
}
