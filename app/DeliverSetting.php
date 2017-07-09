<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliverSetting extends Model
{
    protected $guarded = [];
    
    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }
}
