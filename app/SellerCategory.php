<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerCategory extends Model
{
    protected $guarded = [];
    
    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }
    
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
