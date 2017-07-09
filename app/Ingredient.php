<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Ingredient extends Model
{
    use Searchable;
    protected $guarded = [];
    
    public function dish()
    {
        return $this->belongsTo('App\Dish');
    }
}
