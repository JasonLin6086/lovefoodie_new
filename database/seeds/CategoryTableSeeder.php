<?php

use Illuminate\Database\Seeder;
use App\Category;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$categories = factory(App\Category::class, 10)->create();
        $category_name = array('Chinese', 'Indian', 'Healthy', 'Italian', 'Salad', 'Pizza', 'Noodle');
        $x = 0;
        foreach ($category_name as $category) {
            $data = Category::create([
                'name' => $category,
                'order' => $x++
            ]);
        }
    }
}
