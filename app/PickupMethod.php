<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickupMethod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }
    
    public function pickupLocationMapping()
    {
        return $this->hasMany('App\PickupLocationMapping');
    }
}
