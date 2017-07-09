<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Keyword extends Model
{
    use Searchable;
    protected $guarded = [];
    
    public function dish()
    {
        return $this->belongsTo('App\Dish');
    }
    
    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }
}
