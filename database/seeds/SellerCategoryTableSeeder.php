<?php

use Illuminate\Database\Seeder;
use App\Seller;
use App\Category;
use App\SellerCategory;

class SellerCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$sellerCategories = factory(App\SellerCategories::class, 200)->create();
        $faker = Faker\Factory::create();
        
        $sellers = Seller::pluck("id")->toArray();
        $categories = Category::pluck("id")->toArray();
        foreach (range(1, 200) as $index) {
            repeat:
            $sellerId = $faker->randomElement($sellers);
            $categoryId = $faker->randomElement($categories);
            try {
                SellerCategory::create([
                    "seller_id" => $sellerId,
                    "category_id" => $categoryId,
                ]);
           } catch (\Illuminate\Database\QueryException $e) {
                //look for integrity violation exception (23000)
                if($e->errorInfo[0]==23000)
                    goto repeat;
           }
        }
    }
}
