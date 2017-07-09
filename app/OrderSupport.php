<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderSupport extends Model
{
    //
    protected $guarded = [];
    
    public function order()
    {
        return $this->belongsTo('App\Order');
    }
    
    public function problemcode()
    {
        return $this->belongsTo('App\ProblemCode');
    }
    
    public function solution()
    {
        return $this->belongsTo('App\Solution');
    }
}
