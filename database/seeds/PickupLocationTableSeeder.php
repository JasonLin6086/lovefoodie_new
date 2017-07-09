<?php

use Illuminate\Database\Seeder;

class PickupLocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pickupLocation = factory(App\PickupLocation::class, 100)->create();
    }
}
