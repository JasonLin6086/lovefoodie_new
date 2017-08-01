<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrderSupport;

class ImageController extends Controller
{

    /**
     * @SWG\Get(path="/images/private/order_supports/{supportid}/{filename}",
     *   tags={"98 Images"},
     *   summary="Get a auth image",
     *   description="Get a auth image",
     *   operationId="viewOrderSupportImage",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     *   
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="supportid", in="path", required=true, type="string"),
     *   @SWG\Parameter(name="filename", in="path", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function viewOrderSupportImage(Request $request, $supportid, $filename)
    {
        $ordersupport = OrderSupport::findOrFail($supportid);
        $this->authorize('view', [$ordersupport, $ordersupport->order]);
        $path = '/private/order_supports/'.$supportid.'/'.$filename;
        return response()->download(storage_path('app').$path);
    }
}
