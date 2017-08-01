<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Seller;
use App\User;
use App\ShoppingCartExtra;
use App\ShoppingCart;
use App\Service\DeliverFeeService;
use App\Service\TaxService;
use App\Http\Requests\ShoppingCartExtraCreateRequest;

class ShoppingCartExtraController extends Controller
{
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
     * @SWG\Post(path="/shoppingcartextras",
     *   tags={"04 ShoppingCarts"},
     *   summary="Create a new shoopingCart extra",
     *   description="Create a new shoopingCart extra)",
     *   operationId="store",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="seller_id", in="formData", required=true, type="integer"),
     *   @SWG\Parameter(name="pickup_type", in="formData", required=true, type="string", enum={"DELIVER","GROUP_PICKUP", "STORE_PICKUP"}),
     *   @SWG\Parameter(name="pickup_time", in="formData", required=true, type="string", description="mm/dd/yyyy hh:ii"),
     *   @SWG\Parameter(name="pickup_location_mapping_id", in="formData", required=false, type="integer", description="Required only when pickup_type=GROUP_PICKUP"),
     *   @SWG\Parameter(name="description", in="formData", required=false, type="string"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=404, description="dish_id does not exist"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function store(ShoppingCartExtraCreateRequest $request)
    {
        $user = $request->user();
        $seller = Seller::findOrFail($request->seller_id);
        return $this->storeShoppingCartExtra($user, $seller, $request->pickup_type, $request->pickup_time, $request->pickup_location_mapping_id, $request->description);
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @SWG\Get(path="/shoppingcartextras/{userid}/{sellerid}",
     *   tags={"04 ShoppingCarts"},
     *   summary="",
     *   description="Returns 40 dishes by keyword",
     *   operationId="getDishListByKeyword",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="userid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */
    public function viewShoppingCartExtraBySeller(Request $request, $user_id, $seller_id)
    {
        $this->authorize('viewShoppingCartExtraBySeller', [ShoppingCartExtra::class, $user_id]);
        $shoppingCartExtra = ShoppingCartExtra::where([['user_id', $user_id],['seller_id', $seller_id]])->first();
        return !$shoppingCartExtra? response()->json(['shopping_cart_extra' => null]): $shoppingCartExtra;
    } 
    
    // Store the shoppingCartExtra settings for the user
    // Triggered when the user is going to leave shoppingCart page (Website used only)
    public function storeShoppingCartExtraWeb(Request $request) {
        $user = $request->user();
        $sellerids = ShoppingCart::where('user_id', $user->id)->select('seller_id')->groupBy('seller_id')->get();

        foreach($sellerids as $sellerid){
            $pickup_type = $request->pickup_type_.$sellerid;
            if($pickup_type){
                $seller = Seller::findOrFail($sellerid->seller_id);
                $pickup_time_time = $request->input('pickup_time_time_'.$seller->id);
                $pickup_time = $request->input('pickup_time_date_'.$seller->id).' '.(!$pickup_time_time? '00:00': $pickup_time_time);
                
                $this->storeShoppingCartExtra($user, $seller, $request->input('pickup_type_'.$seller->id), $pickup_time, 
                            $request->input('pickup_location_mapping_id_'.$seller->id) , $request->input('description_'.$seller->id));
            }
        }
    }
    
    // Deal with the storage logic for shooppingCartExtra Model
    private function storeShoppingCartExtra(User $user, Seller $seller, $pickup_type, $pickup_time, $pickup_location_mapping_id, $description){
        
        // If there is old shoppingCartExtra record in DB, delete it first
        ShoppingCartExtra::where([['user_id', $user->id],['seller_id', $seller->id]])->delete();
        
        $extra = new ShoppingCartExtra();
        $extra->user_id = $user->id;
        $extra->seller_id = $seller->id;
        $extra->pickup_time = $pickup_time;
        $extra->pickup_type = $pickup_type;
        $extra->description = $description;
        $extra->deliver_fee = 0;

        // (1) If pickup_type == 'DELIVER', use user's default location as address/google_place_id
        // (2) If pickup_type == 'GROUP_PICKUP', use the selected pickupLocationMapping's address/google_place_id
        // (3) If pickup_type == 'STORE_PICKUP', use seller's location as address/google_place_id
        switch($pickup_type){
            case 'DELIVER':
                $userLoc = $user->location()->first();
                $extra->pickup_location_desc = 'User\'s address';
                $extra->address = $userLoc->address;
                $extra->google_place_id = $userLoc->google_place_id;
                $extra->deliver_fee = DeliverFeeService::getExpectDeliveryFee($user, $seller);
                break;
           
            case 'GROUP_PICKUP':
                $pickupLoc = \App\PickupLocationMapping::find($pickup_location_mapping_id);
                $extra->pickup_location_desc = $pickupLoc->description;
                $extra->address = $pickupLoc->address;
                $extra->google_place_id = $pickupLoc->google_place_id;
                $extra->pickup_location_mapping_id = $pickupLoc->id;
                break;
            
            case 'STORE_PICKUP':
                $sellerLoc = $seller->location()->first();
                $extra->pickup_location_desc = 'Seller\'s store';
                $extra->address = $sellerLoc->address;
                $extra->google_place_id = $sellerLoc->google_place_id;
                break;
        }
        
        // Calculate total_price, tax, and total
        $taxRate = TaxService::getTaxRate($user->location()->first()->zipcode);
        $sum = ShoppingCart::where([['user_id', $user->id],['seller_id', $seller->id]])->sum('total_price');
        $extra->total_price = $sum;
        $extra->tax = round($sum*$taxRate/100, 2);
        $extra->total = $extra->total_price + $extra->tax + $extra->deliver_fee;
        
        // Calculate Stripe transaction fee
        $app_fee = $extra->total*0.029+0.3;
        $extra->transfer_amount = $extra->total-$app_fee;
        $extra->app_fee = $app_fee;
        
        $extra->save();
        return $extra;
    }
}
