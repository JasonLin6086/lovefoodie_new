<?php

use Illuminate\Database\Seeder;

class WishTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $wishes = factory(App\Wish::class, 200)->create();
    }
}
