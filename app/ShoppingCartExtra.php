<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShoppingCartExtra extends Model
{
    //
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }
}
