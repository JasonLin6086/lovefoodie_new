<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DishImage extends Model
{
    //
    protected $guarded = [];
    
    public function dish()
    {
        return $this->belongsTo('App\Dish');
    }
}
