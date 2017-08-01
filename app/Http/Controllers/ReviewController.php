<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Review;
use App\Dish;
use App\Seller;
use App\Order;
use App\Http\Requests\ReviewCreateRequest;
use App\Http\Requests\ReviewUpdateRequest;

class ReviewController extends Controller
{
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        //
        //return Review::paginate(40);
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
     * @SWG\Post(path="/reviews",
     *   tags={"10 Reviews"},
     *   summary="Create a new review",
     *   description="Create a new review",
     *   operationId="store",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="dish_id", in="formData", required=true, type="integer"),
     *   @SWG\Parameter(name="rating", in="formData", required=true, type="integer"),
     *   @SWG\Parameter(name="description", in="formData", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=404, description="dish_id does not exist"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */ 
    public function store(ReviewCreateRequest $request)
    {
        $dish = Dish::findOrFail($request->dish_id);
        $request->merge(['user_id' => $request->user()->id]);
        $request->merge(['seller_id' => $dish->seller_id]);
        $review = Review::create($request->all());
        return $review;
    }
 
    /**
     * @SWG\Get(path="/reviews/{reviewid}",
     *   tags={"10 Reviews"},
     *   summary="Returns a review by review ID",
     *   description="Returns a review info by review ID",
     *   operationId="show",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="reviewid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */ 
    public function show($id)
    {
        return Review::with('user')->findOrFail($id);
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
     * @SWG\Put(path="/reviews/{reviewid}",
     *   tags={"10 Reviews"},
     *   summary="Update a review by ID",
     *   description="Update a review by ID",
     *   operationId="update",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="reviewid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="rating", in="query", required=false, type="integer"),
     *   @SWG\Parameter(name="description", in="query", required=false, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */ 
    public function update(ReviewUpdateRequest $request, $id)
    {
        $review = Review::findOrFail($id);
        $this->authorize('update', $review);
        
        $review->update($request->except(['seller_id', 'user_id', 'dish_id']));
        return $review;
    }

    /**
     * @SWG\Delete(path="/reviews/{reviewid}",
     *   tags={"10 Reviews"},
     *   summary="Delete review by using ID",
     *   description="Delete review info by ID",
     *   operationId="destroy",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="reviewid", in="path", required=true, type="integer"),
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
        $this->authorize('delete', Review::findOrFail($id));
        Review::destroy($id);
    }
    
    /**
     * @SWG\Get(path="/reviews/dish/{dishid}",
     *   tags={"10 Reviews"},
     *   summary="Returns reviews by dish ID",
     *   description="Returns reviews info by dish ID",
     *   operationId="viewreviewsbyDishId",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="dishid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */ 
    public function viewreviewsbyDishId($id)
    {
        // if dish_id doesn't exist, return 404 not found
        Dish::findOrFail($id);
        return Review::where('dish_id', '=', $id)->with('user')->paginate($this->getSmallPageNo());
    }
    
    
    /**
     * @SWG\Get(path="/reviews/seller/{sellerid}",
     *   tags={"10 Reviews"},
     *   summary="Returns reviews by seller ID",
     *   description="Returns reviews info by seller ID",
     *   operationId="viewreviewsbySellerId",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */ 
    public function viewreviewsbySellerId($id)
    {
        // if seller_id doesn't exist, return 404 not found
        Seller::findOrFail($id);
        return Review::where('seller_id', '=', $id)->with('user')->paginate($this->getSmallPageNo());
    }
    
    
    /**
     * @SWG\Post(path="/reviews/order/{orderid}",
     *   tags={"10 Reviews"},
     *   summary="Create reviews for an order",
     *   description="Create reviews for an order",
     *   operationId="storeReviewsByOrder",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="orderid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="dish_id", in="formData", required=true, type="array", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   @SWG\Parameter(name="rating", in="formData", required=true, type="array", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   @SWG\Parameter(name="description", in="formData", required=true, type="array", @SWG\Items(type="string"), collectionFormat="multi"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */ 
    public function storeReviewsByOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('storeReviewsByOrder', [Review::class, $order]);
        
        $dishids = $this->toArray($request->input('dish_id'));
        $ratings = $this->toArray($request->input('rating'));
        $descriptions = $this->toArray($request->input('description'));        
        
        if(sizeof($dishids) != sizeof($ratings) || sizeof($ratings) != sizeof($descriptions)){
            // return some error.
        }
        
        foreach($dishids as $idx => $dishid){
            // If the user had reviewed this dish before, update the review. Otherwise, create a new review
            $review = Review::where('user_id', $order->user_id)->where('dish_id', $dishid)->first();
            if(!$review){
                $review = Review::create([
                    'dish_id' => $dishid,
                    'user_id' => $order->user_id,
                    'seller_id' => $order->seller_id,
                    'rating' => $ratings[$idx],
                    'description' => $descriptions[$idx]
                ]);
            }else{
                $review->update([
                    'rating' => $ratings[$idx],
                    'description' => $descriptions[$idx]
                ]);
            }
        }
        
        return $order;
    }
    
    private function toArray($input){
        return is_array($input)? $input: explode(',', $input);
    }

}
