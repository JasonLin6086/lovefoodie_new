<?php

use Illuminate\Database\Seeder;
use Intervention\Image\ImageManagerStatic as Image;

class SellerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sellers = factory(App\Seller::class, 50)->create();
        
        $all = App\Seller::Where('icon', 'like', 'http%')->get();
        foreach ($all as $seller) {               
            $img = Image::make($seller->icon);
            $fileName = 'sellers/'.$seller->id.'.jpg';
            Storage::put('/public/'.$fileName, $img->stream());
            $seller->update(['icon' => $fileName]);
        }
    }
}
