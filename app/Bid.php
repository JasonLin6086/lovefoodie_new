<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $guarded = ['status'];
    
    public function wish()
    {
        return $this->belongsTo('App\Wish');
    }
    
    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }
}
