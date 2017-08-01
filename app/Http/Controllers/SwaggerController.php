<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\LocationService;
use Jenssegers\Agent\Agent;
use Storage;
use Auth;
use Notification;
use App\Order;
use Intervention\Image\ImageManagerStatic as Image;
use App\Service\ImageService;
use App\Dish;
use App\Service\UserAccountService;
use App\User;
use URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use App\Channels\FcmChannel;
use App\Notifications\OrderUpdated;
use App\OrderSupport;
use DB;
use Carbon\Carbon;
use App\Notifications\PhoneConfirmation;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Illuminate\Validation\ValidationException;
use App\Service\DateTimeFormatService;

/**
 * @SWG\Swagger(
 *     schemes={"http", "https"},
 *     basePath="/api",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Backend API for Lovefoodies",
 *         description="APIs for backend operations!!",
 *         termsOfService="",
 *         @SWG\Contact(
 *             email="contact@mysite.com"
 *         ),
 *         @SWG\License(
 *             name="Private License",
 *             url="URL to the license"
 *         )
 *     ),
 *     @SWG\ExternalDocumentation(
 *         description="Find out more about my website",
 *         url="http..."
 *     )
 * )
 */
class SwaggerController extends Controller
{
    use DateTimeFormatService;
    /**
     * @SWG\Post(path="/getLocationByRadius",
     *   tags={"99 Test"},
     *   summary="",
     *   description="table_name choice : sellers, orders, wishes, pickup_methods",
     *   operationId="getLocationByRadius",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="target_lat", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="target_lng", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="distance", in="formData", required=true, type="integer"),
     *   @SWG\Parameter(name="table_name", in="formData", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     * )
     */
    public function getLocationByRadius(Request $request)
    {
        $target_lat =$request->target_lat;
        $target_lng = $request->target_lng;
        $distance = $request->distance;
        $table_name = $request->table_name;
        
        return LocationService::getLocationByRadius($target_lat, $target_lng, $distance , $table_name);
    }
    /**
     * @SWG\Post(path="/CreateLocationByGPid",
     *   tags={"99 Test"},
     *   summary="",
     *   description="table_name choice : sellers, orders, wishes, pickup_methods",
     *   operationId="CreateLocationByGPid",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="google_place_id", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="table_name", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="table_id", in="formData", required=true, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     * )
     */
    
    public function CreateLocationByGPid(Request $request)
    {
        $google_place_id = $request->google_place_id;
        $table_name = $request->table_name;
        $table_id = $request->table_id;

        return LocationService::CreateLocationByGP_id($google_place_id, $table_name, $table_id);
    }
    
    /**
     * @SWG\Put(path="/UpdateLocationByGPid",
     *   tags={"99 Test"},
     *   summary="",
     *   description="table_name choice : sellers, orders, wishes, pickup_methods",
     *   operationId="UpdateLocationByGPid",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="old_GP_id", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="table_name", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="table_id", in="formData", required=false, type="integer"),
     *   @SWG\Parameter(name="google_place_id", in="formData", required=false, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     * )
     */
    
    public function UpdateLocationByGPid(Request $request)
    {
        $old_GP_id = $request->old_GP_id;
        $table_name = $request->table_name;
        $table_id = $request->table_id;
        $google_place_id = $request->google_place_id;

        return LocationService::UpdateLocationByGP_id($old_GP_id , $table_name, $table_id, $google_place_id );
    }
    
    /**
     * @SWG\Put(path="/DeleteLocationByGPid",
     *   tags={"99 Test"},
     *   summary="",
     *   description="table_name choice : sellers, orders, wishes, pickup_methods",
     *   operationId="DeleteLocationByGPid",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="table_name", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="table_id", in="formData", required=true, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     * )
     */
    
    public function DeleteLocationByGPid(Request $request)
    {
        $table_name = $request->table_name;
        $table_id = $request->table_id;

        return LocationService::DeleteLocationByGP_id( $table_name, $table_id);
    }
    
    /**
     * @SWG\Get(path="/testenv",
     *   tags={"99 Test"},
     *   summary="",
     *   description="test env",
     *   operationId="testEnv",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */    
    public function testEnv(Request $request){
        $agent = new Agent();
        return response()->json([
            'val' => $agent->platform()." ".($agent->isMobile()?$agent->device(): "Not device")
        ]);
    }
    
    /**
     * @SWG\Get(path="/testdist",
     *   tags={"99 Test"},
     *   summary="",
     *   description="test dist",
     *   operationId="testDist",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="lat1", in="query", required=true, type="number"),
     *   @SWG\Parameter(name="lon1", in="query", required=true, type="number"),
     *   @SWG\Parameter(name="lat2", in="query", required=true, type="number"),
     *   @SWG\Parameter(name="lon2", in="query", required=true, type="number"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */    
    public function testDist(Request $request){
        return \App\Service\DistanceService::getDistance($request->lat1, $request->lon1, $request->lat2, $request->lon2);
    }
    
    /**
     * @SWG\Post(path="/test/image",
     *   tags={"99 Test"},
     *   summary="",
     *   description="test image",
     *   operationId="testImage",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="url", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="file", in="formData", required=false, type="file"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */  
    public function testImage(Request $request){
        //$user = User::find(3);
        //return Image::make(Storage::get('/public/'.$user->image))->getWidth();
        return UserAccountService::storeDefaultImage(User::find(1), Input::file('file'));
    }
    
    
    /**
     * @SWG\Get(path="/test/time-offset",
     *   tags={"99 Test"},
     *   summary="",
     *   description="test time offset",
     *   operationId="testTimeOffset",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */  
    public function testTimeOffset(Request $request){  
        //$t = \Carbon\Carbon::now();
        
        
        //$dish = Dish::find(420);
        
        $t = '05/26/2017 01:07:11';
        $datetime = Carbon::createFromFormat('m/d/Y H:i:s', $t); // or your $datetime of course
        return $datetime->diffForHumans();  // "1 week ago"        
    }
    
    /**
     * @SWG\Post(path="/test/fcm",
     *   tags={"99 Test"},
     *   summary="",
     *   description="test fcm",
     *   operationId="testFCM",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="to", in="formData", required=true, type="string", description="fcm registration token"),
     *   @SWG\Parameter(name="title", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="body", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="type", in="formData", required=true, type="string", description="NEW_ORDER, UPDATE_ORDER, NEW_ORDER_SUPPORT, UPDATE_ORDER_SUPPORT"),
     *   @SWG\Parameter(name="id", in="formData", required=true, type="string", description="order_id or order_support_id"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */  
    public function testFCM(Request $request){
        //$user = User::find(1);
        //$user->notify(new OrderUpdated(Order::find(122)));
        $arr = ['type' => $request->type, 'id' => $request->id];
        return var_dump(FcmChannel::sendNotification($request->to, $request->title, $request->body, $arr));
        
        
        $order = Order::find(122);
        $user = User::find(134);
        $user->notify(new OrderUpdated($order));
        
        /*
        $order = Order::find(122);
        $order->status = "REJECTED";
        $order->save();
        */
        
        //$user = $order->user;
        //$user->notify(new OrderUpdated($order));
        /*
        Order::create([
            'user_id' => $order->user_id,
            'seller_id' => $order->seller_id,
            'type' => $order->type,
            'total' => $order->total,
            'deliver_fee' => $order->deliver_fee,
            'pickup_time' => $order->pickup_time,
            'pickup_location_desc' => $order-> pickup_location_desc,
            'address' => $order->address,
            'google_place_id' => $order->google_place_id,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
        ]);*/
        
        /*
        OrderSupport::create([
            'order_id' => 122,
            'problem_code_id' => 103,
            'solution_id' => 1,
            'status' => 'NEW',
            'user_description' => '123',
        ]);*/
        
        /*
        $orderSupport = OrderSupport::find(55);
        $orderSupport->update([
            'seller_description' => 'ABCD'
        ]);*/
    }
}
