<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use Searchable;
    //
    public function dish()
    {
        return $this->hasMany('App\Dish');
    }
    
     public function wish()
    {
        return $this->hasMany('App\Wish');
    }
    
}
