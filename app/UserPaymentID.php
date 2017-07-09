<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPaymentID extends Model
{
    //
      protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
