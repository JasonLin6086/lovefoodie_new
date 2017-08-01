<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PickupLocation;
use App\Service\LocationService;
use App\PickupLocationMapping;
use App\Http\Requests\PickupLocationCreateRequest;

class PickupLocationController extends Controller
{
    var $table_name = 'pickup_locations';
    
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
     * @SWG\Post(path="/pickupLocations",
     *   tags={"15 Pickup Locations"},
     *   summary="Create a new pickup location for a seller",
     *   description="Create a new pickup location for a seller",
     *   operationId="store",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="description", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="address", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="google_place_id", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="order", in="formData", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function store(PickupLocationCreateRequest $request)
    {
        $seller = $request->user()->seller;
        $request->merge(['seller_id'=> $seller->id]);
        $pickupLocation = PickupLocation::create($request->all());
        
        // Save an entry in Locations
        LocationService::CreateLocationByGP_id($pickupLocation->google_place_id, $this->table_name, $pickupLocation->id);        
        
        return $pickupLocation;
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    
    // Update not using
    public function update(Request $request, $id)
    {
        /*
        $pickupLocation = PickupLocation::findOrFail($id);
        $this->authorize('update', $pickupLocation);
        
        // If google_place_id has changed, update the entry in Locations
        LocationService::UpdateLocationByGP_id($pickupLocation->google_place_id, $this->table_name, $id);        

        $pickupLocation->fill($request->all());
        $pickupLocation->save();
        return $pickupLocation;
         */
    }

    /**
     * @SWG\Delete(path="/pickupLocations/{pickupLocationid}",
     *   tags={"15 Pickup Locations"},
     *   summary="Delete one specific pickupLocation by ID",
     *   description="Delete one specific pickupLocation by ID",
     *   operationId="destroy",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="pickupLocationid", in="path", required=true, type="integer"),
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
        $pickupLocation = PickupLocation::findOrFail($id);
        $this->authorize('delete', $pickupLocation);

        // update pickup_location_mapping's foreign key as null
        $mappings = PickupLocationMapping::where('pickup_location_id', $pickupLocation->id);
        $mappings->update(['pickup_location_id' => null]);
            
        PickupLocation::destroy($pickupLocation->id); 
        
        // Delete an entry in Locations
        LocationService::DeleteLocationByGP_id($this->table_name, $id);
    }
    
    /**
     * @SWG\Get(path="/pickupLocations/seller/{sellerid}",
     *   tags={"15 Pickup Locations"},
     *   summary="Returns all pickupLocations for the seller",
     *   description="Returns all pickupLocations for the seller",
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
        $this->authorize('viewBySeller', [PickupLocation::class, $id]);
        return PickupLocation::where('seller_id', '=', $id)->paginate(40);
    }
}
