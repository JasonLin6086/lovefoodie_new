<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{

    protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }

    public function dish()
    {
        return $this->belongsTo('App\Dish');
    }
    
}
