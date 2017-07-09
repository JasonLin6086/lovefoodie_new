<?php

use Illuminate\Database\Seeder;

class OrderSupportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ordersupports = factory(App\OrderSupport::class, 50)->create();
    }
}
