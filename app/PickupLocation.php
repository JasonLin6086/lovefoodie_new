<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickupLocation extends Model
{
    protected $guarded = [];
    
    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }
    
    public function location()
    {
        return Location::where('locations.table_name', 'sellers')
                ->where('table_id', $this->id)->first();
    }    

    public function pickupLocationMapping()
    {
        return $this->hasMany('App\PickupLocationMapping');
    }
}
