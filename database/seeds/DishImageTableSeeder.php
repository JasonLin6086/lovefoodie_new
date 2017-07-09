<?php

use Illuminate\Database\Seeder;

class DishImageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dishimages = factory(App\DishImage::class, 40)->create();
//        $faker = Faker\Factory::create();
//
//        /*
//        $dishes = Dish::all();
//        foreach ($dishes as $dish) {
//            $imageNum = random_int(1,4);
//            for ($x = 0; $x < $imageNum; $x++) {
//                $dishImage = DishImage::create([
//                    'dish_id' => $dish->id,
//                    'path' => '',
//                    'order' => $x
//                ]);                
//                
//                $img = Image::make($faker->imageUrl());
//                $fileName = 'dishes/'.$dish->id.'/' . $dishImage->id . '.jpg';
//                Storage::put('/public/'.$fileName, $img->stream());
//                $dishImage->update(['path' => $fileName]);
//            }
//        }*/
//        
//        $dishImages = DishImage::Where('path', 'like', 'http%')->get();
//        foreach($dishImages as $image){
//            $img = Image::make($image->path);
//            $fileName = 'dishes/' . $image->dish_id . '/' . $image->id . '.jpg';
//            Storage::put('/public/' . $fileName, $img->stream());
//            $image->update(['path' => $fileName]);
//        }  
    }
}
