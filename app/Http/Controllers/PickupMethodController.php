<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PickupMethod;
use App\Seller;
use App\PickupLocation;
use App\PickupLocationMapping;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Service\LocationService;
use App\Http\Requests\PickupMethodCreateRequest;
use App\Http\Requests\PickupMethodUpdateRequest;

class PickupMethodController extends Controller
{
    var $table_name = 'pickup_location_mappings';
    var $weeks = array('1'=>'Sun', '2'=>'Mon', '3'=>'Tue', '4'=>'Wed', '5'=>'Thr', '6'=>'Fri', '7'=>'Sat');
    
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['viewBySeller']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @SWG\Post(path="/pickupMethods",
     *   tags={"14 Pickup Methods"},
     *   summary="Create a new pickup method for a seller",
     *   description="Create a new pickup method for a seller)",
     *   operationId="store",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="type", in="formData", required=true, type="string", enum={"DATE","WEEKDAY"}),
     *   @SWG\Parameter(name="date", in="formData", required=false, type="string", description="mm/dd/yyyy", format ="date"),
     *   @SWG\Parameter(name="weekday", in="formData", required=false, type="array", description="weekdays selected Sun=1 Mon=2 ... Sat=7", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   @SWG\Parameter(name="no_time", in="formData", required=true, type="string", enum={"0","1"}),
     *   @SWG\Parameter(name="start_time", in="formData", required=false, type="string", description="hh:ii"),
     *   @SWG\Parameter(name="end_time", in="formData", required=false, type="string", description="hh:ii"),
     *   @SWG\Parameter(name="loc_mappings", in="formData", required=true, type="array", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   @SWG\Parameter(name="new_loc", in="formData", required=false, type="array", description="JSON stirng, format={id:blabla, address:blabla, google_place_id:blabla, description:blabla}", @SWG\Items(type="string"), collectionFormat="multi"),
     *   @SWG\Parameter(name="delete_loc", in="formData", required=false, type="array", description="The pickupLocation's id to be deleted", @SWG\Items(type="integer"), collectionFormat="multi"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=404, description="locationMappings id doesn't exist"), 
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function store(PickupMethodCreateRequest $request)
    {
        $seller = $request->user()->seller;
        $request->merge(['seller_id'=> $seller->id]);
        
        // join weekday
        if($request->weekday){
            $request->merge(['weekday_msg'=> $this->getWeekdayMsg($request->weekday)]);
            $request->merge(['weekday'=> is_array($request->weekday)? implode(",", $request->weekday): $request->weekday]);
        }else{
            $day = date('w', strtotime($request->date));
            //$day = $day==0? 7: $day;
            $request->merge(['weekday_msg'=> $this->getWeekdayMsg($day)]);
            $request->merge(['weekday'=> $day]);
        }
        $disable_weekday = implode(",", array_diff([1,2,3,4,5,6,7], $request->weekday? explode(",", $request->weekday):[]));
        $request->merge(['disabled_weekday'=> $disable_weekday]);
        
        // add new PickupLocations
        $keys = $this->addPickupLocations($seller, $request->input('new_loc'));
        
        // delete unwanted PickupLocations
        $this->deletePickupLocations($seller, $request->input('delete_loc'));
        
        // Store this pickupMethod
        $pickupMethod = PickupMethod::create($request->except('loc_mappings', 'new_loc', 'delete_loc'));
        
        // store pickupLocationMapping
        $this->storePickupLocationMapping($pickupMethod, $request->input('loc_mappings'), $keys);
        
        return $pickupMethod;
    }

    /**
     * Display the specified resource. (This is used by web)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pickupmethod = PickupMethod::findOrFail($id);
        $this->authorize('view', $pickupmethod);
        return $pickupmethod;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @SWG\Put(path="/pickupMethods/{pickupMethodid}",
     *   tags={"14 Pickup Methods"},
     *   summary="Update one specific pickupMethod by ID",
     *   description="Update one specific pickupMethod by ID",
     *   operationId="update",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="pickupMethodid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="type", in="query", required=false, type="string", enum={"DATE","WEEKDAY"}),
     *   @SWG\Parameter(name="date", in="query", required=false, type="string", description="mm/dd/yyyy", format ="date"),
     *   @SWG\Parameter(name="weekday[]", in="query", required=false, type="array", description="weekdays selected Sun=1 Mon=2 ... Sat=7", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   @SWG\Parameter(name="no_time", in="query", required=false, type="string", enum={"0","1"}),
     *   @SWG\Parameter(name="start_time", in="query", required=false, description="hh:ii", type="integer"),
     *   @SWG\Parameter(name="end_time", in="query", required=false, description="hh:ii", type="integer"),
     *   @SWG\Parameter(name="loc_mappings[]", in="query", required=false, type="array", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   @SWG\Parameter(name="new_loc[]", in="query", required=false, type="array", description="JSON stirng, format={id:blabla, address:blabla, google_place_id:blabla, description:blabla}", @SWG\Items(type="string"), collectionFormat="multi"),
     *   @SWG\Parameter(name="delete_loc[]", in="query", required=false, type="array", description="The pickupLocation's id to be deleted", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"), 
     *   @SWG\Response(response=404, description="id doesn't exist"),  
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function update(PickupMethodUpdateRequest $request, $id)
    {
        $pickupMethod = PickupMethod::findOrFail($id);
        $this->authorize('update', $pickupMethod);

        $seller = $request->user()->seller;
        $request->merge(['seller_id'=> $seller->id]);
        
        // join weekday
        if($request->weekday){
            $request->merge(['weekday_msg'=> $this->getWeekdayMsg($request->weekday)]);
            $request->merge(['weekday'=> is_array($request->weekday)? implode(",", $request->weekday): $request->weekday]);
        }else{
            $day = date('w', strtotime($request->date));
            //$day = $day==0? 7: $day;
            $request->merge(['weekday_msg'=> $this->getWeekdayMsg($day)]);
            $request->merge(['weekday'=> $day]);
        }
        $disable_weekday = implode(",", array_diff([1,2,3,4,5,6,7], $request->weekday? explode(",", $request->weekday):[]));
        $request->merge(['disabled_weekday'=> $disable_weekday]);        
        
        // add new PickupLocations
        $keys = $this->addPickupLocations($seller, $request->input('new_loc'));
        
        // delete unwanted PickupLocations
        $this->deletePickupLocations($seller, $request->input('delete_loc'));
        
        // Update this pickupMethod
        $pickupMethod->fill($request->except('loc_mappings', 'new_loc', 'delete_loc'));
        $pickupMethod->save();
        
        // store pickupLocationMapping
        $this->storePickupLocationMapping($pickupMethod, $request->input('loc_mappings'), $keys);
        
        return $pickupMethod;
    }

    /**
     * @SWG\Delete(path="/pickupMethods/{pickupMethodid}",
     *   tags={"14 Pickup Methods"},
     *   summary="Delete one specific pickupMethod by ID",
     *   description="Delete one specific pickupMethod by ID",
     *   operationId="destroy",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="pickupMethodid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id doesn't exist"), 
     * )
     */
    public function destroy($id)
    {
        $pickupMethod = PickupMethod::findOrFail($id);
        $this->authorize('delete', $pickupMethod);
        
        // (1) Delete all pickupLocationMapping belongs to this pickupMathod
        // (2) Delete all location records for these pickupLocationMapping
        $mappings = $pickupMethod->pickupLocationMapping()->get();
        foreach($mappings as $map){
            LocationService::DeleteLocationByGP_id($this->table_name, $map->id);
        }
        $pickupMethod->pickupLocationMapping()->delete();
        PickupMethod::destroy($id);
    }
    
    /**
     * @SWG\Get(path="/pickupMethods/seller/{sellerid}",
     *   tags={"14 Pickup Methods"},
     *   summary="Returns all pickupMethods for the seller to edit",
     *   description="Returns all pickupMethods for the seller to edit",
     *   operationId="viewBySeller",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     * )
     */ 
    public function viewBySeller($id)
    {
        $this->authorize('viewBySeller', [PickupMethod::class, $id]);
        return PickupMethod::where('seller_id', '=', $id)->with('pickupLocationMapping')->paginate(40);
    }
    
    /**
     * @SWG\Get(path="/pickupMethods/menu/{sellerid}",
     *   tags={"14 Pickup Methods"},
     *   summary="Returns all pickupMethods menu for the seller",
     *   description="Returns all pickupMethods menu for the seller",
     *   operationId="viewMenu",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */     
    public function viewMenu($id)
    {
        $data = $this->viewByBuyer($id);
        $array = array(); 
        foreach($data as $idx => $d)
        {
            $menu = new menu;
            $menu->address = $d->address;
            $menu->google_place_id = $d->google_place_id;
            
            $date = $d->type=='DATE'? date('m/d/Y', strtotime($d->date)):'';
            
            $description = $date? $date.' | ': '';
            $description .= $d->weekday_msg.' | ';
            $description .= $d->no_time? 'Time To Be Decided | ' : $d->start_time.'-'.$d->end_time.' | ';
            $description .= $d->description.' | ';
            $description .= $d->address;
            $menu->description = $description;
            
            $menu->type = $d->type;
            $menu->date = $date;
            $menu->no_time = $d->no_time;
            $menu->weekday_msg = $d->weekday_msg;            
            $menu->weekday = $d->weekday;
            $menu->disabled_weekday = $d->disabled_weekday;
            $menu->start_time = $d->start_time;
            $menu->end_time = $d->end_time;
            $menu->location = \App\Location::where([['table_name', 'pickup_location_mappings'],['table_id', $d->pickup_location_mapping_id]])->first();
            $menu->pickup_location_mapping_id = $d->pickup_location_mapping_id;
            
            $array[$idx] = $menu;
        }
        
        return $array;
    }
    
    // Get valid(not expired yet) pickupMethods for a specific seller for buyer
    // Also used to check whether this seller has GROUP_PICKUP option
    public function viewByBuyer($id){
        $data = DB::table('pickup_location_mappings as map')
            ->join('pickup_methods as med', 'med.id', '=', 'map.pickup_method_id')    
            ->where('med.seller_id', $id)
            // Query pickup_methods where the type == 'WEEKDAY' or date is larger than current date
            ->where(function ($query) { $query->where('med.type', 'WEEKDAY')->orWhere([['med.type', 'DATE'],['med.date', '>', \Carbon\Carbon::now()->toDateTimeString()]]);})
            ->select('map.id as pickup_location_mapping_id', 'map.*', 'med.*')
            ->get();
        return $data;
    }
    
    private function storePickupLocationMapping($pickupMethod, $mappings, $keys)
    {
        if(!$mappings){ return; }
        if(!is_array($mappings)){ $mappings = explode(',', $mappings); }
        
        // Delete Location records for pickupLocationMapping
        $maps = $pickupMethod->pickupLocationMapping()->get();
        foreach($maps as $map){
            LocationService::DeleteLocationByGP_id($map->google_place_id, $this->table_name, $map->id);
        }
        
        // Delete PickupLocationMapping
        $pickupMethod->pickupLocationMapping()->delete();
        
        foreach($mappings as $mapping){
            $locid = $keys && $keys[$mapping]? $keys[$mapping]: $mapping;
            $pickupLoc = PickupLocation::findOrFail($locid);
            $mapping = PickupLocationMapping::create([
                'pickup_method_id' => $pickupMethod->id,
                'pickup_location_id' => $locid,
                'description' => $pickupLoc->description,
                'address' => $pickupLoc->address,
                'google_place_id' => $pickupLoc->google_place_id
            ]);
            
            // Store a location record
            LocationService::CreateLocationByGP_id($pickupLoc->google_place_id, $this->table_name, $mapping->id);
        }
    }
    
    private function getWeekdayMsg($weekday)
    {
        if(!$weekday){ return ''; }
        if(!is_array($weekday)){ $weekday = explode(',', $weekday); }
        $msg = '';
        foreach($weekday as $w){
            $msg .= $msg? ','.$this->weeks[$w] : $this->weeks[$w];
        }
        return $msg;
    }
    
    private function addPickupLocations($seller, $new_locs)
    {
        if(!$new_locs){ return; }
        if(!is_array($new_locs)){ $new_locs = explode(',', $new_locs); }
        
        $keys = [];
        foreach($new_locs as $loc){
            $locObj = json_decode($loc);
            $location = PickupLocation::create([
                'seller_id' => $seller->id,
                'description' => $locObj->description,
                'address' => $locObj->address,  
                'google_place_id' => $locObj->google_place_id,
                'order' => 0
            ]);
            $keys[$locObj->id] = $location->id;
            
            // Save an entry in Locations
            LocationService::CreateLocationByGP_id($location->google_place_id, 'pickup_locations', $location->id);    
        }
        return $keys;
    }
    
    private function deletePickupLocations($seller, $delete_locs)
    {
        if(!$delete_locs){ return; }
        if(!is_array($delete_locs)){ $delete_locs = explode(',', $delete_locs); }

        foreach($delete_locs as $loc){
            
            // update pickup_location_mapping's foreign key as null
            $mappings = PickupLocationMapping::where('pickup_location_id', $loc);
            $mappings->update(['pickup_location_id' => null]);
            
            PickupLocation::destroy($loc);
            
            // Delete an entry in Locations
            LocationService::DeleteLocationByGP_id($this->table_name, $loc);
        }
    }
  
}

class menu
{
    var $address;
    var $google_place_id;
    var $description;
    var $type;
    var $date;
    var $start_time;
    var $end_time;
    var $no_time;
    var $weekday_msg;
    var $weekday;
    var $disabled_weekday;
    var $location;
    var $pickup_location_mapping_id;
}  
