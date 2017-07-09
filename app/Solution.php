<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    //
    protected $guarded = [];
    
    public function problemcode()
    {
        return $this->belongsTo('App\ProblemCode');
    }
    
}
