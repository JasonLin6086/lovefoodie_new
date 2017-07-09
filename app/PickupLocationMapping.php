<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickupLocationMapping extends Model
{
    protected $guarded = ['weekday_msg'];
    
    public function pickupLocation()
    {
        return $this->belongsTo('App\PickupLocation');
    }
    
    public function pickupMethod()
    {
        return $this->belongsTo('App\PickupMethod');
    }  
}
