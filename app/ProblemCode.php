<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProblemCode extends Model
{
    //
    protected $guarded = ['parent_code'];
    
    public function ordersupport()
    {
        return $this->hasMany('App\OrderSupport');
    }
    
    public function solution()
    {
        return $this->hasMany('App\Solution');
    }
}
