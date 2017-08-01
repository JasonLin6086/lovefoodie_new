<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\OrderSupport;
use App\Order;
use App\OrderSupportImage;
use App\Solution;
use App\Http\Requests\OrderSupportCreateRequest;
use App\Http\Requests\OrderSupportCreateFollowupRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Service\ImageService;
use App\Service\PaymentService;

class OrderSupportController extends Controller
{
    public function index()
    {
        //
        //return OrderSupport::paginate(40);
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
     * @SWG\Post(path="/ordersupports",
     *   tags={"11 Order Supports"},
     *   summary="Create a new orderSupport as buyer",
     *   description="Create a new orderSupport as buyer",
     *   operationId="store",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     *   
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="order_id", in="formData", required=true, type="integer"),
     *   @SWG\Parameter(name="problem_code_id", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="description", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="image[]", in="formData", required=false, type="file"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"), 
     *   @SWG\Response(response=404, description="id doesn't exist"), 
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function store(OrderSupportCreateRequest $request)
    {
        $order = Order::findOrFail($request->order_id);
        $this->authorize('create', [OrderSupport::class, $order]);
        
        $ordersupport = OrderSupport::create($request->except(['image']));
        
        // Store order_support images
        ImageService::storeOrderSupportImages($ordersupport, Input::file('image'));
        
        $order->support_status = 'TO_SELLER';
        $order->save();
        
        return $ordersupport;
    }
    

    /**
     * @SWG\Get(path="/ordersupports/{supportid}",
     *   tags={"11 Order Supports"},
     *   summary="Get one specific Support",
     *   description="Get one specific Support)",
     *   operationId="show",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     *   
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="supportid", in="path", required=true, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"), 
     *   @SWG\Response(response=404, description="id doesn't exist"), 
     * )
     */
    public function show($id)
    {                                      
        $ordersupport = OrderSupport::with('orderSupportImage')->findOrFail($id);
        $this->authorize('view', [$ordersupport, Order::findOrFail($ordersupport->order_id)]);
        return $ordersupport;
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
     * @SWG\Post(path="/ordersupports/follow-up",
     *   tags={"11 Order Supports"},
     *   summary="Create a new orderSupport fellow up",
     *   description="Create a new orderSupport fellow up",
     *   operationId="storeFollowup",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     *   
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="order_id", in="formData", required=true, type="integer"),
     *   @SWG\Parameter(name="role", in="formData", required=true, type="string", enum={"BUYER","SELLER", "HELPER"}),
     *   @SWG\Parameter(name="solution_id", in="formData", required=true, type="integer"),
     *   @SWG\Parameter(name="description", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="refund", in="formData", required=false, type="number", description="required if the solution is refund"),
     *   @SWG\Parameter(name="signature", in="formData", required=false, type="file", description="required if the solution need to be signed"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"), 
     *   @SWG\Response(response=404, description="id doesn't exist"), 
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function storeFollowup(OrderSupportCreateFollowupRequest $request)
    {        
        $order = Order::findOrFail($request->order_id);
        $this->authorize('storeFollowup', [OrderSupport::class, $order]);
        
        $ordersupport = OrderSupport::create($request->all());
        
        // Store the signature
        ImageService::storeOrderSupportSignature($ordersupport, Input::file('signature'));
        
        // Check the action of solution and update order status or inform next actioner
        $solution = Solution::findOrFail($request->solution_id);
        $ordersupport->update(['solution_description' => $solution->description, 'action' => $solution->action]);
        
        $order->support_status = $solution->action;
        $order->refund = $request->refund+$solution->refund;
        $order->save();
        
        // If the solution is refund, trigger a refund request to stripe
        if($solution->require_refund && $request->refund){
            PaymentService::refund($order, $request->refund);
        }
        
        return $ordersupport;
    }
    
    /**
     * @SWG\Get(path="/ordersupports/solutions",
     *   tags={"11 Order Supports"},
     *   summary="Get solution options for a role",
     *   description="Get solution options for a role",
     *   operationId="viewSolutions",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     *   
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="role", in="query", required=true, type="string", enum={"BUYER","SELLER", "HELPER"}),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user is invalid"),
     * )
     */
    public function viewSolutions(Request $request){
        // need validation??
        return Solution::where([['role', $request->role],['issue', 'ORDER_SUPPORT']])->get();
    }
   
    /**
     * @SWG\Get(path="/ordersupports/order/{orderid}",
     *   tags={"11 Order Supports"},
     *   summary="Get order support history for a specific order",
     *   description="Get order support history for a specific order",
     *   operationId="viewOrderSuportsByOrder",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     *   
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="orderid", in="path", required=true, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user is invalid"),
     * )
     */
    public function viewOrderSuportsByOrder(Request $request, $id){
        $order = Order::with('orderSupport')->findOrFail($id);
        $this->authorize('viewOrderSuportsByOrder', [OrderSupport::class, $order]);
        return $order;
    }
}
