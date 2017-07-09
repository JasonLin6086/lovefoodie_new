<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'verify_token', 
    ];
    
    /**
     *
     * Boot the model.
     *
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->verify_token = str_random(40);
        });
    }
    
    /**
     * Confirm the user.
     *
     * @return void
     */
    public function confirmEmail()
    {
        $this->verified = true;
        $this->verify_token = null;
        $this->save();
    }    
    
    public function wish()
    {
        return $this->hasMany('App\Wish');
    }
    
    public function order()
    {
        return $this->hasMany('App\Order');
    }
    
    public function seller()
    {
        return $this->hasOne('App\Seller');
    }    
    
    public function favorite()
    {
        return $this->hasMany('App\Favorite');
    }
    
    public function shoppingCart()
    {
        return $this->hasMany('App\ShoppingCart');
    }    
    
    public function userPaymentID()
    {
        return $this->hasMany('App\UserPaymentID');
    }
    
    public function shoppingCartExtra()
    {
        return $this->hasMany('App\shoppingCartExtra');
    }
    public function location()
    {
        return Location::where('locations.table_name', 'users')
                ->where('table_id', $this->id)->first();
    }
    
}
