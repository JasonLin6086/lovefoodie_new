<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Dish extends Model
{
    //
    use Searchable;
    
    protected $guarded = ['availible_time'];
    
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
    
    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }
    
    public function dishImage()
    {
        return $this->hasMany('App\DishImage');
    }
    
    public function keyword()
    {
        return $this->hasMany('App\Keyword');
    }
    
    public function ingredient()
    {
        return $this->hasMany('App\Ingredient');
    }    

    public function review()
    {
        return $this->hasMany('App\Review');
    }
    
    public function imagePreview(){
        return $this->hasOne('App\DishImage');
    }
    
}
