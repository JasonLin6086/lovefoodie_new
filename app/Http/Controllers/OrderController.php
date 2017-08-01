<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Order;
use App\Service\LocationService;
use App\Service\PaymentService;
use DateTime;
use \Illuminate\Support\Facades\Input;
use App\Service\ImageService;
use App\Http\Requests\OrderCompleteRequest;

class OrderController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return Order::paginate($this->getPageNo());
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$order = Order::create($request->except('orderdetail'));
        //return $order;
    }

    /**
     * @SWG\Get(path="/orders/{orderid}",
     *   tags={"05 Orders"},
     *   summary="Returns a order by order id",
     *   description="Returns a order by order id",
     *   operationId="show",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="orderid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function show($id)
    {
        $order = Order::with(['orderDetail', 'seller', 'user', 'location'])->findOrFail($id);
        $this->authorize('view', $order);
        return $order;
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
     * @SWG\Get(path="/orders/buyer/{buyerid}",
     *   tags={"05 Orders"},
     *   summary="Returns 40 orders by buyer id",
     *   description="Returns 40 orders by buyer id",
     *   operationId="viewByBuyer",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="buyerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="status[]", in="query", required=false,  type="array", @SWG\Items(type="string"), collectionFormat="multi", enum={"NEW", "ACCEPTED", "REJECTED", "READY", "COMPLETED"}),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function viewByBuyer(Request $request, $id)
    {
        $this->authorize('viewByBuyer', [Order::class, $id]);
        $orders = Order::where('user_id', '=', $id);
        $orders = $request->status? $orders->whereIn('status', $request->status) : $orders;
        return $orders->orderBy('pickup_time')->with('seller')->paginate($this->getPageNo())->appends(Input::except(['page'])); 
    }
    
    /**
     * @SWG\Get(path="/orders/seller/{sellerid}",
     *   tags={"05 Orders"},
     *   summary="Returns 40 orders by seller id with optional filters",
     *   description="Returns 40 orders by seller id with optional filters",
     *   operationId="viewBySeller",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="sellerid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="type[]", in="query", required=false, type="array", @SWG\Items(type="string"), collectionFormat="multi", enum={"REGULAR","BID"}),
     *   @SWG\Parameter(name="pickup_type[]", in="query", required=false, type="array", @SWG\Items(type="string"), collectionFormat="multi", enum={"DELIVER", "GROUP_PICKUP", "STORE_PICKUP"}),
     *   @SWG\Parameter(name="status[]", in="query", required=false,  type="array", @SWG\Items(type="string"), collectionFormat="multi", enum={"NEW", "ACCEPTED", "REJECTED", "READY", "COMPLETED"}),
     *   @SWG\Parameter(name="support_status", in="query", required=false, type="string", enum={"NOT_EMPTY", "TO_SELLER"}),
     *   @SWG\Parameter(name="pickup_date", in="query", required=false, type="string", description="mm/dd/yyyy", format ="date-time"),
     *   @SWG\Parameter(name="orderby", in="query", required=false, type="string", enum={"created_at_desc","pickup_time"}),
     *  
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function viewBySeller(Request $request, $id)
    {
        $this->authorize('viewBySeller', [Order::class, $id]);
        
        $orders = Order::where('seller_id', '=', $id);
        $orders = $request->type? $orders->whereIn('type', $request->type) : $orders;
        $orders = $request->pickup_type? $orders->whereIn('pickup_type', $request->pickup_type) : $orders;
        $orders = $request->status? $orders->whereIn('status', $request->status) : $orders;
        if($request->support_status){
            switch($request->support_status){
                case 'NOT_EMPTY': $orders = $orders->whereNotNull('support_status'); break;
                case 'TO_SELLER': $orders = $orders->where('support_status', 'TO_SELLER'); break;
            }
        }
        
        // Add pickup_time filter
        $user = $request->user();
        if($request->pickup_date){
            $date_filter_start = DateTime::createFromFormat('m/d/Y H:i:s', $request->pickup_date.' 00:00:00')
                    ->modify(($user->utc_offset>0?'-':'+').abs($user->utc_offset).' minutes');
            
            $date_filter_end = DateTime::createFromFormat('m/d/Y H:i:s', $request->pickup_date.' 00:00:00')
                    ->modify(($user->utc_offset>0?'-':'+').abs($user->utc_offset).' minutes')->modify('+1 day');

            $orders = $orders->whereBetween('pickup_time', [$date_filter_start->format('Y-m-d H:i:s'), $date_filter_end->format('Y-m-d H:i:s')]);
        }
        
        switch($request->orderby){
            case 'created_at_desc': $orders = $orders->orderBy('created_at', 'DESC'); break;
            case 'pickup_time': $orders = $orders->orderBy('pickup_time'); break;
            default: $orders = $orders->orderBy('pickup_time');
        }
        
        return $orders->with('user')
                ->paginate($this->getPageNo())
                ->appends(Input::except(['page']));
    }
    
    /**
     * @SWG\Put(path="/orders/accept/{orderid}",
     *   tags={"05 Orders"},
     *   summary="Update order status to ACCEPTED",
     *   description="Update order status to ACCEPTED",
     *   operationId="accept",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="orderid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */     
    public function accept($id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('accept', $order);
        $order->status = "ACCEPTED";
        $order->save();
        return $order;
    }    

    /**
     * @SWG\Put(path="/orders/reject/{orderid}",
     *   tags={"05 Orders"},
     *   summary="Update order status to REJECTED",
     *   description="Update order status to REJECTED",
     *   operationId="reject",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="orderid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */    
    public function reject($id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('reject', $order);
        
        // Return the money to buyer
        PaymentService::refund($order, $order->transfer_amount);
        
        $order->status = "REJECTED";
        $order->save();
        return $order;
    }

    /**
     * @SWG\Put(path="/orders/ready/{orderid}",
     *   tags={"05 Orders"},
     *   summary="Update order status to READY",
     *   description="Update order status to READY",
     *   operationId="ready",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="orderid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function ready($id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('ready', $order);
        $order->status = "READY";
        $order->save();
        return $order;   
    }

    /**
     * @SWG\Post(path="/orders/complete/{orderid}",
     *   tags={"05 Orders"},
     *   summary="Update order status to COMPLETED",
     *   description="Update order status to COMPLETED",
     *   operationId="complete",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="orderid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="_method", in="formData", required=true, type="string", enum={"PUT"}),
     *   @SWG\Parameter(name="buyer_signature", in="formData", required=true, type="file"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function complete(OrderCompleteRequest $request, $id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('complete', $order);
        
        // Store signature image
        ImageService::storeOrderSignature($order, Input::file('buyer_signature'));
        
        // Transfer the money to seller
        PaymentService::completeChargeProcess($order, 0);         
        
        $order->status = "COMPLETED";
        $order->complete_time = \Carbon\Carbon::now();
        $order->save();

        return $order;
    }
}
