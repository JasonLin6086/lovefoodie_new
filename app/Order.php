<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $guarded = ['status'];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }
    
    public function wish()
    {
        return $this->hasMany('App\OrderSupport');
    }
    
    public function orderDetail()
    {
        return $this->hasMany('App\OrderDetail');
    } 
    
    public function location()
    {
        return $this->morphMany('App\Location', 'locationable');
    }
}
