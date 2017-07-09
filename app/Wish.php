<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wish extends Model
{
    protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
    
    public function bid()
    {
        return $this->hasMany('App\Bid');
    }
    
    public function location()
    {
        return $this->morphMany('App\Location', 'locationable');
    }
}
