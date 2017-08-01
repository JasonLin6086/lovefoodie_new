<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Wish;
use App\Bid;
use App\Service\LocationService;
use DateTime;
use App\Http\Requests\WishCreateRequest;
use App\Http\Requests\WishUpdateRequest;
    
class WishController extends Controller
{
    var $table_name = 'wishes';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * @SWG\Get(path="/wishes",
     *   tags={"07 Wishes"},
     *   summary="Returns wishes",
     *   description="Returns all wishes info",
     *   operationId="index",
     *   produces={"application/json"},
     *   @SWG\Response(response=200, description="success"),
     * )
     */
    public function index()
    {
        $wishes = Wish::join('users', 'users.id', 'wishes.user_id')
                ->join('locations', 'locations.table_id', 'wishes.id')
                ->where('locations.table_name', 'wishes')
                ->select('wishes.*','users.name','users.image','locations.city', 'locations.state')
                ->orderBy('end_date', 'DESC')
                ->paginate($this->getPageNo());
        return $wishes;
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
     * @SWG\Post(path="/wishes",
     *   tags={"07 Wishes"},
     *   summary="Create a new wish",
     *   description="Create a new wish)",
     *   operationId="store",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="category_id", in="formData", required=true, type="integer"),
     *   @SWG\Parameter(name="topic", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="description", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="pickup_time", in="formData", required=true, type="string", description="mm/dd/yyyy H:i", format ="date-time"),
     *   @SWG\Parameter(name="pickup_method", in="formData", required=true, type="string", enum={"DELIVER","PICKUP"}),
     *   @SWG\Parameter(name="address", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="google_place_id", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="quantity", in="formData", required=true, type="integer"),
     *   @SWG\Parameter(name="end_date", in="formData", required=true, type="string", description="mm/dd/yyyy H:i", format ="date-time"),
     *   @SWG\Parameter(name="price_from", in="formData", required=true, type="number", format ="float"),
     *   @SWG\Parameter(name="price_to", in="formData", required=true, type="number", format ="float"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function store(WishCreateRequest $request)
    {
        $request->merge(['user_id'=> $request->user()->id]);        
        $wish = Wish::create($request->all());
        
        // Save an entry in Locations
        LocationService::CreateLocationByGP_id($wish->google_place_id, $this->table_name, $wish->id);

        return $wish;
    }

    /**
     * @SWG\Get(path="/wishes/{wishid}",
     *   tags={"07 Wishes"},
     *   summary="Returns a wish",
     *   description="Returns a wish by id",
     *   operationId="index",
     *   produces={"application/json"},
     *   @SWG\Parameter(name="wishid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function show($id)
    {
        $wish = Wish::join('users', 'users.id', 'wishes.user_id')
                ->join('locations', 'locations.table_id', 'wishes.id')
                ->where('wishes.id', $id)
                ->where('locations.table_name', 'wishes')
                ->select('wishes.*','users.name','users.image','locations.city', 'locations.state')->first();   
        return $wish;
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
     * @SWG\Put(path="/wishes/{wishid}",
     *   tags={"07 Wishes"},
     *   summary="Update one specific wish by ID",
     *   description="Update one specific wish info by ID",
     *   operationId="update",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="wishid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="topic", in="query", required=false, type="string"),
     *   @SWG\Parameter(name="description", in="query", required=false, type="string"),
     *   @SWG\Parameter(name="pickup_time", in="query", required=false, type="string", description="mm/dd/yyyy H:i", format ="date-time"),
     *   @SWG\Parameter(name="pickup_method", in="query", required=false, type="string", enum={"DELIVER","PICKUP"}),
     *   @SWG\Parameter(name="address", in="query", required=false, type="string"),
     *   @SWG\Parameter(name="google_place_id", in="query", required=false, type="string"),
     *   @SWG\Parameter(name="quantity", in="query", required=false, type="integer"),
     *   @SWG\Parameter(name="end_date", in="query", required=false, type="string", description="mm/dd/yyyy H:i", format ="date-time"),
     *   @SWG\Parameter(name="price_from", in="query", required=false, type="number", format ="float"),
     *   @SWG\Parameter(name="price_to", in="query", required=false, type="number", format ="float"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function update(WishUpdateRequest $request, $id)
    {
        $wish = Wish::findOrFail($id);
        $this->authorize('update', $wish);
        
        // If google_place_id has changed, update the entry in Locations
        if($request->google_place_id){
            LocationService::UpdateLocationByGP_id($wish->google_place_id, $this->table_name, $id, $request->google_place_id);
        }

        $wish->update($request->all());
        return $wish;
    }
    
    /**
     * @SWG\Delete(path="/wishes/{wishid}",
     *   tags={"07 Wishes"},
     *   summary="Delete one specific wish by ID",
     *   description="Delete one specific wish info by ID",
     *   operationId="destroy",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="wishid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function destroy($id)
    {
        $wish = Wish::findOrFail($id);
        $this->authorize('delete', $wish);
        
        // Delete an entry in Locations
        LocationService::DeleteLocationByGP_id($this->table_name, $id);
        
        $wish->update(['status' => 'close']);
        $wish->save();
        return $wish;
    }
    
    /**
     * @SWG\Get(path="/wishes/{wishid}/bids",
     *   tags={"07 Wishes"},
     *   summary="Returns all bids for a wish",
     *   description="Returns all bids for a wish",
     *   operationId="getBidsByWishid",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="wishid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */    
    public function getBidsByWishid($id)
    {
        $wish = Wish::findOrFail($id);
        $this->authorize('getBidsByWishid', $wish);
        return Bid::join('sellers', 'sellers.id', 'bids.seller_id')
                ->select('bids.*', 'sellers.kitchen_name', 'sellers.icon', 'sellers.rating')
                ->where('wish_id', '=', $id)->paginate(40);
    }
    
     /**
     * @SWG\Get(path="/wishes/buyer/{buyerid}",
     *   tags={"07 Wishes"},
     *   summary="Returns all wishes for a user",
     *   description="Returns all wishes for a user",
     *   operationId="getWishesByUser",
     *   produces={"application/json"},
     *
     *   @SWG\Parameter(name="buyerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     * )
     */ 
    public function getWishesByUser(Request $request, $id)
    {
        $this->authorize('getWishesByUser', [Wish::class, $id]);
        $wishes = $request->user()->wish()->paginate($this->getPageNo());
        return $wishes;
    }
    
     /**
     * @SWG\Get(path="/wishes/seller/{sellerid}",
     *   tags={"07 Wishes"},
     *   summary="Returns all wishes this seller has bidded",
     *   description="Returns all wishes this seller has bidded",
     *   operationId="getWishesBySeller",
     *   produces={"application/json"},
     *
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="status", in="query", required=false, type="string", enum={"OPEN","END","CLOSE"}),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     * )
     */ 
    public function getWishesBySeller(Request $request, $id)
    {
        $this->authorize('getWishesBySeller', [Wish::class, $id]);
        
        $wishes = Wish::join('bids', 'wishes.id', '=', 'bids.wish_id')
                ->join('users', 'users.id', 'wishes.user_id')
                ->join(\DB::raw("(select distinct wish_id from bids where seller_id =".$id.") as mybid"), 'mybid.wish_id', '=', 'wishes.id');
        
        // Add status filter
        if($request->status=='END'){
            $now = \Carbon\Carbon::now()->toDateTimeString();
            $wishes = $wishes->where('wishes.end_date', '<=', $now);
        }else if($request->status){
            $wishes = $wishes->where('wishes.status', $request->status);
        }       
                
        $wishes = $wishes->with(['bid' => function($q) use ($id){
                     $q->where('seller_id', $id);
                  }])
                ->select('wishes.*', 'users.image as user_image', 'users.name as user_name', \DB::raw("MIN(bid_price) AS min_bid_price"))
                ->withCount('bid')
                ->groupBy('wishes.id')
                ->paginate($this->getPageNo());
                  
        return $wishes;
    }
}
