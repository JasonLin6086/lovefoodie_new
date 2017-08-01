<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShoppingCart;
use App\Dish;
use App\Seller;
use App\Service\DistanceService;
use App\ShoppingCartExtra;
use App\Service\TaxService;
use App\Service\DeliverFeeService;
use App\Http\Requests\ShoppingCartCreateRequest;
use App\Http\Requests\ShoppingCartUpdateRequest;

class ShoppingCartController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
     * @SWG\Post(path="/shoppingcarts",
     *   tags={"04 ShoppingCarts"},
     *   summary="Create a new shoopingCart item",
     *   description="Create a new shoopingCart item)",
     *   operationId="store",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="dish_id", in="formData", required=true, type="integer"),
     *   @SWG\Parameter(name="quantity", in="formData", required=true, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=404, description="dish_id does not exist"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function store(ShoppingCartCreateRequest $request)
    {
        $dish = Dish::findOrFail($request->dish_id);
        
        // If there is the same item in shoppingCart, add the quantity
        $shoppingCart_old = ShoppingCart::where('user_id', $request->user()->id)->where('dish_id', $dish->id)->first();
        if($shoppingCart_old){
            $request->merge(['quantity' => $request->quantity+$shoppingCart_old->quantity]);
            ShoppingCart::destroy($shoppingCart_old->id);
        }
        
        $shoppingCart = ShoppingCart::create([
            'user_id' => $request->user()->id,
            'seller_id' => $dish->seller_id,
            'dish_id' => $dish->id,
            'dish_name' => $dish->name,
            'quantity' => $request->quantity,
            'unit_price' => $dish->price,
            'total_price' => $dish->price*$request->quantity
        ]);
        return $shoppingCart;
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
     * @SWG\Put(path="/shoppingcarts/{shoppingcartid}",
     *   tags={"04 ShoppingCarts"},
     *   summary="Update one specific shoppingCart item by ID",
     *   description="Update one specific shoppingCart item by ID",
     *   operationId="update",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="shoppingcartid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="quantity", in="query", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"), 
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function update(ShoppingCartUpdateRequest $request, $id)
    {
        $shoppingCart = ShoppingCart::findOrFail($id);
        $this->authorize('update', $shoppingCart);
   
        $shoppingCart->update(['quantity'=> $request->quantity, 'total_price' => $request->quantity*$shoppingCart->unit_price]);
        return $shoppingCart;
    }

    
    /**
     * @SWG\Delete(path="/shoppingcarts/{shoppingcartid}",
     *   tags={"04 ShoppingCarts"},
     *   summary="Delete one specific shoppingCart item by ID",
     *   description="Delete one specific shoppingCart item by ID",
     *   operationId="destroy",
     *   produces={"application/json"},
     *  
     *   @SWG\Parameter(name="shoppingcartid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function destroy(Request $request, $id)
    {
        $shoppingCart = ShoppingCart::findOrFail($id);
        $this->authorize('delete', $shoppingCart);
        ShoppingCart::destroy($id);
        
        // If this user doesn't buy any of this seller's dish, delete shoppingCartExtra 
        $count = ShoppingCart::where([['seller_id', $shoppingCart->seller_id],['user_id', $shoppingCart->user_id]])->count();
        if($count==0){
            ShoppingCartExtra::where([['seller_id', $shoppingCart->seller_id],['user_id', $shoppingCart->user_id]])->delete();
        }
    } 
    
    /**
     * @SWG\Delete(path="/shoppingcarts/buyer/{buyerid}",
     *   tags={"04 ShoppingCarts"},
     *   summary="Delete all shoppingCart records for this buyer",
     *   description="Delete all shoppingCart records for this buyer",
     *   operationId="destroyByBuyer",
     *   produces={"application/json"},
     *  
     *   @SWG\Parameter(name="buyerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function destroyByBuyer(Request $request, $id)
    {
        $this->authorize('destroyByBuyer', [ShoppingCart::class, $id]);
        $user = $request->user();
        $user->shoppingCart()->delete();
        $user->shoppingCartExtra()->delete();
    }
    
    /**
     * @SWG\Delete(path="/shoppingcarts/buyer/{buyerid}/seller/{sellerid}",
     *   tags={"04 ShoppingCarts"},
     *   summary="Delete one particular seller's all shoppinCart data for the buyer",
     *   description="Delete one particular seller's all shoppinCart data for the buyer",
     *   operationId="destroyByBuyerSeller",
     *   produces={"application/json"},
     *  
     *   @SWG\Parameter(name="buyerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function destroyByBuyerSeller(Request $request, $buyerid, $sellerid)
    {
        $this->authorize('destroyByBuyerSeller', [ShoppingCart::class, $buyerid]);
        ShoppingCart::where([['user_id', $buyerid],['seller_id', $sellerid]])->delete();
        ShoppingCartExtra::where([['user_id', $buyerid],['seller_id', $sellerid]])->delete();
    }
    
    /**
     * @SWG\Get(path="/shoppingcarts/buyer/{buyerid}",
     *   tags={"04 ShoppingCarts"},
     *   summary="Returns all shoppingCarts for the user",
     *   description="Returns all shoppingCarts for the user",
     *   operationId="viewByBuyer",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="buyerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="keep_extra_settings", in="query", required=false, type="string", enum={"0","1"}, description="shoppinCartExtra data will be deleted by default, use 1 to keep them"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"), 
     * )
     */
    public function viewByBuyer(Request $request, $id)
    {
        $this->authorize('viewByBuyer', [ShoppingCart::class, $id]);  
        $user = $request->user();
        
        // If keep_extra_settings != 1, delete all shoppingCartExtra records for this user
        // This paramter is used to make sure that all shoppingCartExtra records are stored just before the user pay the money
        // So that all shoppingCartExtra records are valid
        if(!$request->keep_extra_settings){ $user->shoppingCartExtra()->delete(); }       
        
        // Use this user's default location to calculate the sales tax, 
        // this is not precise enough. Need to be modified in the future.
        $userLoc = $user->location()->first();
        if(!$userLoc){ abort(422, 'User location is missing'); }
        $zipcode = $userLoc->zipcode;
        $taxRate = TaxService::getTaxRate($zipcode);
        
        $sellers = Seller
                ::join(\DB::raw("(select seller_id, SUM(total_price) as subtotal, MAX(created_at) as last_update from shopping_carts where user_id =".$id." group by seller_id) as shop_sellers"), 'shop_sellers.seller_id', '=', 'sellers.id')
                ->with(['shoppingCart' => function($q) use ($id){ $q->where('user_id', $id); }])
                ->with(['shoppingCartExtra' => function($q) use ($id){ $q->where('user_id', $id); }])
                ->orderBy('shop_sellers.last_update', 'DESC')
                ->get();
            
        $summary = new summary;
        $summary->total_item_count = $this->getItemCount($request);
                
        // Calculate deliver fee, tax, and summary
        foreach($sellers as $seller){
            $summary->total_price += $seller->subtotal;
            
            $seller->expect_deliver_fee = DeliverFeeService::getExpectDeliveryFee($user, $seller);
            $seller->actual_deliver_fee = DeliverFeeService::getActualDeliveryFee($user, $seller);
            $seller->tax_rate = $taxRate.'';
            $seller->tax = round($seller->subtotal*$taxRate/100, 2).'';
            $seller->subtotal = ($seller->subtotal+$seller->tax+$seller->actual_deliver_fee).'';
            
            $summary->total_tax += $seller->tax;
            $summary->total_deliver_fee += $seller->actual_deliver_fee;
            $summary->total_total += $seller->subtotal;
        }
        
        return response()->json(['sellers' => $sellers, 'summary' => $summary ]);
    }
    
    // view shoppingCart data for web users
    public function viewByBuyer_old(Request $request, $id)
    {
        $this->authorize('viewByBuyer', [ShoppingCart::class, $id]);
        return ShoppingCart::where('shopping_carts.user_id', '=', $id)
                ->join('sellers', 'shopping_carts.seller_id', '=', 'sellers.id')
                ->select('shopping_carts.*', 'sellers.kitchen_name')
                ->orderBy('seller_id')->get();
    }

    /**
     * @SWG\Get(path="/shoppingcarts/deliver-fee",
     *   tags={"04 ShoppingCarts"},
     *   summary="Returns all seller's deliver fee in the user's shoppingcart",
     *   description="Returns all seller's deliver fee in the user's shoppingcart",
     *   operationId="viewDeliverFeeBySeller",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"), 
     * )
     */
    public function viewDeliverFeeBySeller(Request $request)
    {
        $user = $request->user();
        $sellers = Seller::whereIn('id', ShoppingCart::where('user_id', '=', $user->id)->pluck('seller_id'))->get();
        $res = [];       
        foreach($sellers as $seller){
            $res[$seller->id] = DeliverFeeService::getExpectDeliveryFee($user, $seller);
        } 
        return $res;
    }

    /**
     * @SWG\Get(path="/shoppingcarts/tax-rate",
     *   tags={"04 ShoppingCarts"},
     *   summary="Returns the user's tax rate",
     *   description="Returns the user's tax rate",
     *   operationId="viewTaxByUser",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"), 
     * )
     */
    public function viewTaxByUser(Request $request)
    {
        $userloc = $request->user()->location()->first();
        return TaxService::getTaxRate($userloc->zipcode);
    }
    
    /**
     * @SWG\Get(path="/shoppingcarts/item-count",
     *   tags={"04 ShoppingCarts"},
     *   summary="Returns this user's shoppingCart item count",
     *   description="Returns this user's shoppingCart item count",
     *   operationId="viewItemCount",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     * )
     */    
    public function viewItemCount(Request $request) {
        return response()->json(['count' => $this->getItemCount($request)]);
    }
    
    private function getItemCount(Request $request){
        return $request->user()->shoppingCart->sum('quantity');
    }
   
}

class summary
{
    var $total_item_count = 0;
    var $total_price = 0;
    var $total_tax = 0;
    var $total_deliver_fee = 0;
    var $total_total = 0;
}  
