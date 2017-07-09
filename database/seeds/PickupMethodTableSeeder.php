<?php

use Illuminate\Database\Seeder;

class PickupMethodTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pickupMethod = factory(App\PickupMethod::class, 200)->create();
    }
}
