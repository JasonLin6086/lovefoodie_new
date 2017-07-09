p<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(CategoryTableSeeder::class);
        $this->call(SellerTableSeeder::class);
        $this->call(DishTableSeeder::class);
        $this->call(ReviewTableSeeder::class);
        $this->call(KeywordTableSeeder::class);
        $this->call(DishImageTableSeeder::class);
        $this->call(OrderTableSeeder::class);
        $this->call(OrderDetailsTableSeeder::class);
        $this->call(ProblemCodeTableSeeder::class);
        $this->call(SolutionTableSeeder::class);
        $this->call(OrderSupportTableSeeder::class);
        $this->call(WishTableSeeder::class);
        $this->call(BidTableSeeder::class);
        $this->call(PickupMethodTableSeeder::class);
        $this->call(PickupLocationTableSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(SellerCategoryTableSeeder::class);         
        $this->call(IngredientTableSeeder::class);
        $this->call(FavoriteTableSeeder::class);
        $this->call(DeliverSettingTableSeeder::class);
        $this->call(DeliverFeeSettingTableSeeder::class);
        $this->call(PickupLocationMappingTableSeeder::class);
    }
}
