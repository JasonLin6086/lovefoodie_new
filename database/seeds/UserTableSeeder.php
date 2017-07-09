<?php

use Illuminate\Database\Seeder;
use Intervention\Image\ImageManagerStatic as Image;
use App\Service\UserAccountService;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$users = factory(App\User::class, 10)->create();
        
        $all = App\User::Where('image', 'like', 'http%')->get();
        foreach ($all as $user) {
            $img = Image::make($user->image);
            $fileName = 'users/'.$user->id.'.jpg';
            Storage::put('/public/'.$fileName, $img->stream());
            $user->update(['image' => $fileName]);
        }
        
        $all2 = App\User::whereNull('image')->get();
        foreach ($all2 as $user) {
            UserAccountService::storeDefaultImage($user);
        }
    }
}
