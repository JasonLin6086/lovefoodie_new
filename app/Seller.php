<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Service\DateTimeFormatService;

class Seller extends Model
{
    use Searchable;
    use DateTimeFormatService;
   /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    //protected $dates = ['created_at', 'updated_at'];
    
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function dish()
    {
        return $this->hasMany('App\Dish');
    }
    
    public function bid()
    {
        return $this->hasMany('App\Bid');
    }
    
    public function order()
    {
        return $this->hasMany('App\Order');
    }
    
    public function pickupMethod()
    {
        return $this->hasMany('App\PickupMethod');
    } 
    
    public function pickupLocation()
    {
        return $this->hasMany('App\PickupLocation');
    }
    
    public function sellerCategory()
    {
        return $this->belongsToMany('App\Category', 'seller_categories', 'seller_id', 'category_id');
    }
    
    public function location()
    {
        return Location::where('locations.table_name', 'sellers')
                ->where('table_id', $this->id)->first();
    }
    
    public function review()
    {
        return $this->hasMany('App\Review');
    }
    
    public function deliverSetting()
    {
        return $this->hasOne('App\DeliverSetting');
    }    
    
    public function deliverFeeSetting()
    {
        return $this->hasMany('App\DeliverFeeSetting')
            ->orderBy('miles_within', 'desc');
    }
    
    public function groupPickupOption()
    {
        return $this->hasMany('App\PickupMethod')
            ->join('pickup_location_mappings', 'pickup_methods.id','=', 'pickup_location_mappings.pickup_method_id');
    }
    
    public function dishPreview(){
        return $this->dish()->with('imagePreview');
    }

}
