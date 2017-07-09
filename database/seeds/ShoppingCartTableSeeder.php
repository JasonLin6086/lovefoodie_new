<?php

use Illuminate\Database\Seeder;

class ShoppingCartTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $shoppingcart = factory(App\ShoppingCart::class, 1000)->create();
    }
}
