<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Favorite;
use App\Seller;

class FavoriteController extends Controller
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
     * @SWG\Post(path="/favorites",
     *   tags={"06 Favorites"},
     *   summary="Create a new favorite",
     *   description="Create a new favorite)",
     *   operationId="store",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="seller_id", in="formData", required=true, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function store(Request $request)
    {
        $request->merge(['user_id'=> $request->user()->id]);
        $favorite = Favorite::create($request->all());   
        return $favorite;
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
     * @SWG\Delete(path="/favorites/{favoriteid}",
     *   tags={"06 Favorites"},
     *   summary="Delete one specific favorite by ID",
     *   description="Delete one specific favorite info by ID",
     *   operationId="destroy",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="favoriteid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     * )
     */
    public function destroy($id)
    {
        $this->authorize('delete', Favorite::findOrFail($id));
        Favorite::destroy($id);
    }
    
    /**
     * @SWG\Delete(path="/favorites/user/{userid}/seller/{sellerid}",
     *   tags={"06 Favorites"},
     *   summary="Delete one specific favorite by user and seller id",
     *   description="Delete one specific favorite by user and seller id",
     *   operationId="destroyByUserSeller",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="userid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     * )
     */
    public function destroyByUserSeller($userid, $sellerid)
    {
        $this->authorize('destroyByUserSeller', [Favorite::class, $userid]);
        Favorite::where([['user_id', $userid],['seller_id', $sellerid]])->delete();
    }
    
    /**
     * @SWG\Get(path="/favorites/user/{userid}",
     *   tags={"06 Favorites"},
     *   summary="Returns favorites for the user",
     *   description="Returns favorites for the user",
     *   operationId="viewByBuyer",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="userid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"), 
     * )
     */  
    public function viewByUser(Request $request, $id)
    {
        $this->authorize('viewByUser', [Favorite::class, $id]);
        $sellers = Seller::join('favorites', 'sellers.id', '=', 'favorites.seller_id')
                ->where('favorites.user_id', '=', $id)
                ->select('sellers.*', 'favorites.id as favorite_id')
                ->with('dishPreviewActive')
                ->paginate(40);
        return $sellers;
    }
    
    
    /**
     * @SWG\Get(path="/favorites/isfavorite/{userid}/{sellerid}",
     *   tags={"06 Favorites"},
     *   summary="Returns favorites for the user",
     *   description="Returns favorites for the user",
     *   operationId="isMyFavorite",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="userid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"), 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"), 
     * )
     */  
    public function isMyFavorite(Request $request, $userid, $sellerid){
        $this->authorize('isMyFavorite', [Favorite::class, $userid]);
        $favorites = Favorite::where('user_id', $userid)->where('seller_id', $sellerid)->get();
        return response()->json(['isfavorite' => sizeof($favorites)==0? false: true, 'favorite_id' => sizeof($favorites)==0? 0: $favorites[0]->id]);
    }
    
}
