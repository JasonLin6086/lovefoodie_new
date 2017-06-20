<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Seller;
use App\SellerCategory;
use App\Service\LocationService;
use App\Service\SellerStatusService;
use App\Service\ImageService;
use App\Service\PhoneVerifyService;
use App\Http\Requests\SellerCreateRequest;
use App\Http\Requests\SellerUpdateRequest;

class SellerController extends Controller
{
    var $table_name = 'sellers';
    
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
        //return Seller::paginate(40);
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
     * @SWG\Post(path="/sellers",
     *   tags={"02 Sellers"},
     *   summary="Create a new seller",
     *   description="Create a new seller",
     *   operationId="store",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"multipart/form-data", "application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="category_id", in="formData", required=true, type="array", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   @SWG\Parameter(name="icon", in="formData", required=true, type="file"),
     *   @SWG\Parameter(name="kitchen_name", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="phone_number", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="phone_verify_code", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="email", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="address", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="google_place_id", in="formData", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     * )
     */
    public function store(SellerCreateRequest $request)
    {
        $user = $request->user();
        
        // Verify the phone
        PhoneVerifyService::confirmPhoneNumber($request->phone_number, $request->phone_verify_code, $this->table_name, $user->id);
        
        $request->merge(['user_id'=> $user->id]);
        $seller = Seller::create($request->except('category_id', 'icon', 'phone_verify_code'));

        // Save an entry in Locations
        LocationService::CreateLocationByGP_id($seller->google_place_id, $this->table_name, $seller->id);        
        
        // Add sellercategory
        $this->storeSellerCategories($seller, $request->category_id);        
        
        // Upload seller icon image
        $this->storeSellerIcon($seller, Input::file('icon'));
        
        // Update user's "isseller" to be true
        $user->update(['isseller'=>1]);
        
        return $seller;
    }

    /**
     * @SWG\Get(path="/sellers/{sellerid}",
     *   tags={"02 Sellers"},
     *   summary="Returns one specific seller by id",
     *   description="Returns one specific seller info by id",
     *   operationId="show",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function show(Request $request, $id)
    {
        return Seller::with(['sellerCategory', 'deliverSetting', 'location'])->findOrFail($id);
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
     * @SWG\Post(path="/sellers/{sellerid}",
     *   tags={"02 Sellers"},
     *   summary="Update new info for specific seller",
     *   description="Update new info for specific seller",
     *   operationId="updates",
     *   produces={"application/xml", "application/json"},
     *   consumes={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="_method", in="formData", required=true, type="string", enum={"PUT"}),
     *   @SWG\Parameter(name="category_id", in="formData", required=false, type="array", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   @SWG\Parameter(name="icon", in="formData", required=false, type="file"),
     *   @SWG\Parameter(name="kitchen_name", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="phone_number", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="phone_verify_code", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="email", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="address", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="google_place_id", in="formData", required=false, type="string"), 
     *   @SWG\Parameter(name="isactive", in="formData", required=false, type="string", enum={"0","1"}), 
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function update(SellerUpdateRequest $request, $id)
    {
        $seller = Seller::findOrFail($id);
        $this->authorize('update', $seller);
        
        // If the phone number is modified, need to verify 
        if($request->phone_number && $seller->phone_number != $request->phone_number){
            PhoneVerifyService::confirmPhoneNumber($request->phone_number, $request->phone_verify_code, $this->table_name, $seller->user->id);    
        }

        // If google_place_id has changed, update the entry in Locations
        if($request->google_place_id){
            LocationService::UpdateLocationByGP_id($seller->google_place_id, $this->table_name, $id, $request->google_place_id);
        }
        
        // Update sellercategory
        $this->storeSellerCategories($seller, $request->input('category_id'));
        
        // Upload seller icon image
        $this->storeSellerIcon($seller, Input::file('icon'));
        
        // Update dish's "seller_isactvie" field
        if(isset($request->isactive)){ $this->checkActiveSeller($seller, $request->isactive); }
                
        $seller->update($request->except('category_id', 'icon', 'phone_verify_code'));
        return $seller;
    }

     /**
     * @SWG\Delete(path="/sellers/{sellerid}",
     *   tags={"02 Sellers"},
     *   summary="Inactive the seller",
     *   description="Inactive the seller",
     *   operationId="destroy",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     * )
     */
    public function destroy($id)
    {
        $seller = Seller::findOrFail($id);
        $this->authorize('delete', $seller);
        
        $seller->update(['isactive' => false]);
        $seller->dish()->update(['seller_isactive' => false]);
    }
    
    /**
     * @SWG\Get(path="/sellers/newest",
     *   tags={"02 Sellers"},
     *   summary="Returns sellers by desc created time",
     *   description="Returns newest sellers info by created time",
     *   operationId="getListByNewest",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     * )
     */
     public function getListByNewest(Request $request)
    {
        $sellers = Seller::orderBy('created_at', 'desc')
                ->active()
                ->with(['sellerCategory', 'dishPreviewActive'])
                ->paginate($this->getPageNo());
        return $sellers;
    }
    
    /**
     * @SWG\Get(path="/sellers/rating",
     *   tags={"02 Sellers"},
     *   summary="Returns sellers by desc overall_rating",
     *   description="Returns highest rating sellers info",
     *   operationId="getListByRating",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     * )
     */ 
    public function getListByRating(Request $request)
    {   
        $sellers = Seller::orderBy('rating', 'desc')
                ->active()
                ->with(['sellerCategory', 'dishPreviewActive'])
                ->paginate($this->getPageNo());
        return $sellers;
    }
    
    /**
     * @SWG\Get(path="/sellers/category/{categoryid}",
     *   tags={"02 Sellers"},
     *   summary="Returns sellers by category",
     *   description="Returns sellers info by category",
     *   operationId="getListByCategory",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="categoryid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function getListByCategory($id)
    {
        return Seller::join('seller_categories', 'sellers.id', '=', 'seller_categories.seller_id')
                ->active()
                ->where('seller_categories.category_id', '=', $id)
                ->with(['sellerCategory', 'dishPreviewActive'])
                ->paginate($this->getPageNo());
    }
    
     /**
     * @SWG\Get(path="/sellers/nearby",
     *   tags={"02 Sellers"},
     *   summary="Returns 40 sellers nearby",
     *   description="Returns sellers info neaby",
     *   operationId="getListByNearBy",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="target_lat", in="query", required=true, type="string"),
     *   @SWG\Parameter(name="target_lng", in="query", required=true, type="string"),
     *   @SWG\Parameter(name="distance", in="query", required=false, type="string"),
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     * )
     */
    public function getListByNearBy(Request $request)
    {
        $distance = $request->distance? $request->distance: 100;
        $ids =  LocationService::getLocationByRadius($request->target_lat, $request->target_lng, $distance, $this->table_name)->pluck('table_id');
        return Seller::whereIn('id', $ids)
                ->active()
                ->with(['sellerCategory', 'dishPreviewActive'])
                ->paginate($this->getPageNo())->appends(Input::except(['page']));
    }
    
    /**
     * @SWG\Get(path="/sellers/{sellerid}/dishes",
     *   tags={"02 Sellers"},
     *   summary="Returns 40 dishes by this seller",
     *   description="Returns 40 dishes by this seller",
     *   operationId="getDishesBySeller",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="include_inactive", in="query", required=false, type="string", enum={"0","1"}, description="For seller, s"),
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function getDishesBySeller(Request $request, $id)
    {
        $seller = Seller::findOrFail($id);
        $query = $seller->dish();
        if(!$request->include_inactive || $request->include_inactive==0){ $query = $query->where('isactive', true); }
        return $query->with('dishImage')->paginate(40);
    }
    
    /**
     * @SWG\Get(path="/sellers/{sellerid}/status",
     *   tags={"02 Sellers"},
     *   summary="Returns status for this seller",
     *   description="Returns status for this seller, possible status= <br>
                       'DELIVER_SETTING_MISSING', <br> 'DELIVER_SETTING_INVALID', <br>
                        'PAYMENT_SETTING_MISSING', <br> 'PAYMENT_IDENTITY_PENDING', <br>
                        'PAYMENT_IDENTITY_FAIL', <br> 'PAYMENT_BANK_PENDING', <br>
                        'PAYMENT_BANK_VERIFY_FAIL', <br> 'PAYMENT_BANK_ERROR' <br> 
                        'PHONE_VERIFY_REQUIRED' <br>  'OK'",
     *   operationId="getStatusBySeller",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function getStatusBySeller(Request $request, $id)
    {
        $seller = Seller::findOrFail($id);
        $this->authorize('getStatusBySeller', $seller);
        $status = SellerStatusService::getSellerStatus($seller);
        return response()->json($status); 
    }
    
     /**
     * @SWG\Get(path="/sellers/phone-verify-code/send",
     *   tags={"02 Sellers"},
     *   summary="Send phone verify SMS message",
     *   description="Send phone verify SMS message",
     *   operationId="getPhoneVerifyCode",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="phone_number", in="query", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     * )
     */
    public function getPhoneVerifyCode(Request $request)
    {
        $user = $request->user();
        return PhoneVerifyService::sendPhoneVerifyCode($user, $request->phone_number, $this->table_name);
    }
    
    private function storeSellerCategories($seller, $sellerCategories)
    {
        if(!$sellerCategories){ return; }
        if(!is_array($sellerCategories)){ $sellerCategories = explode(',', $sellerCategories); }
        SellerCategory::where('seller_id', '=', $seller->id)->delete();
        
        foreach($sellerCategories as $sellerCategory){
            SellerCategory::create([
                'seller_id' => $seller->id,
                'category_id' => $sellerCategory          
            ]);
        }
    }

    private function storeSellerIcon($seller, $file)
    {
        if($file && $file->isValid()){
            ImageService::storeSellerImage($seller, $file);
        }
    }
    
    private function checkActiveSeller($seller, $isactive){
        $seller->dish()->update(['seller_isactive' => $isactive]);
    }
    
}
