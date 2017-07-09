<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Review extends Model
{
    use Searchable;
    protected $guarded = [];
    
    public function dish()
    {
        return $this->belongsTo('App\Dish');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    } 
    
    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }
    
}
