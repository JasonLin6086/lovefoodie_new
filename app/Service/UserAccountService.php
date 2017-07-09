<?php

namespace App\Service;

use Laravel\Socialite\Contracts\User as ProviderUser;
use App\User;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use URL;
use File;
use Illuminate\Support\Facades\Input;

class UserAccountService
{
    public static function createOrGetUser(ProviderUser $providerUser, $provider)
    {
        $user = User::whereProvider($provider)
            ->whereProviderUserId($providerUser->getId())
            ->first();
        
        if (!$user) {
            $user = User::create([
                'provider' => $provider,
                'provider_user_id' => $providerUser->getId(),
                'avatar' => $providerUser->getAvatar(),
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail() == null? "":$providerUser->getEmail() ,  
                'password' => "N/A"
            ]);
        }
        
        if(!$user->image || $user->avatar != $providerUser->getAvatar()) {
            self::storeImage($providerUser, $user);
        }
        return $user;
    }
    
     private static function storeImage(ProviderUser $providerUser, $user)
    {
        try{
            $img = Image::make($providerUser->getAvatar());
            $imagePath = 'users/'.$user->id.'.jpg';
            Storage::put('/public/'.$imagePath, $img->stream());
            $user->update(['image'=>$imagePath]);   
        }catch(\Intervention\Image\Exception\NotReadableException $e){
            
        }
        return $user;
    }

    
    public static function storeDefaultImage($user)
    {
        $img = Image::make(URL::asset('/image/avatar1.jpg'));
        $imagePath = 'users/'.$user->id.'.jpg';
        Storage::put('/public/'.$imagePath, $img->stream());
        $user->update(['image'=>$imagePath]);
        return $user;
    }
    
    public static function updateImage($user, $file)
    {
        if($file && $file->isValid()){
            $fileName = 'users/'.$user->id.'.'.$file->getClientOriginalExtension();
            Storage::put('/public/'.$fileName, File::get($file));
            $user->update(['image' => $fileName]);
        }
        return $user;
    }
}
