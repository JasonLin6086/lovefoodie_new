<div class="row" id="data-container">
    <div class="col-md-12">
        @if (!empty($orders))
        @foreach ($orders as $order)
        <div class="form-group strip_list">
            <img id="buyer_icon" class="seller_icon" src="{{ url('storage/'.$order->user->image) }}" alt="">
            <span id="buy_name">{{$order->user->name}}</span>

            <span class="order_time">
                <h4>{{str_replace('_', ' ', $order->pickup_type)}}</h4>
                {{$order->pickup_time}}<br>
                @if($order->pickup_type=="GROUP_PICKUP")
                {{$order->pickup_location_desc}}&nbsp;{{$order->address}}
                @else
                {{$order->address}}
                @endif
            </span>
            <table class="table table-condensed">
                <tbody>
                    @foreach ($order->orderDetail()->get() as $orderDetail)
                    @php ($dishImage = $orderDetail->dish->dishImage()->first())
                    <tr>
                        <td style="width:16%; "><a href="{{ url('/dishes/'.$orderDetail->dish_id) }}"><img class="dish_img" src="{{ $dishImage? url('storage/'.$dishImage->path) : ''}}" alt=""></a></td>
                        <td style="width:55%; "><h4 class="dish_name">{{$orderDetail->dish_name}}</h4></td>
                        <td style="text-align: right;">
                            $ {{$orderDetail->unit_price}}<br>
                            <strong>x{{$orderDetail->quantity}}</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="50"><span class="foot_format">Tax</span>
                            <span class="cost_format">${{$order->tax}}</span>
                        </td>
                    </tr>                      
                    <tr>
                        <td colspan="50" id="no_border"><span class="foot_format">Delivery Fee</span>
                            <span class="cost_format">${{$order->deliver_fee}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="50" id="no_border"><span class="foot_format">Total</span>
                            <span class="cost_format">${{$order->total}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="50" id="no_border">
                            @if($order->status=="REJECTED")
                            <span class="cost_format">Status: <span class="rejected">{{$order->status}}</span></span>
                            @elseif($order->status=="COMPLETED")
                            <span class="cost_format">Status: <span class="completed">{{$order->status}}</span></span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
            <table>
                <tr>
                    <td style="width: 88%;"></td>
                    @if($order->status=="NEW")
                    <td style="float: right;">
                        <a id="submit_btn" class="btn_full" href="javascript:void(0)" onclick="updateStatus('/seller/order/{{$order->id}}/accept');" style="float: right;margin-right: 10px;">Accept</a>  
                    </td>
                    <td>
                        <a id="cancel_btn" class="btn_full" href="javascript:void(0)" onclick="updateStatus('/seller/order/{{$order->id}}/reject');" style="float: right;">Reject</a>
                    </td>
                    @elseif($order->status=="ACCEPTED")
                    <td style="float: right;">
                        <a id="submit_btn" class="btn_full" href="javascript:void(0)" onclick="updateStatus('/seller/order/{{$order->id}}/deliver');" style="float: right;width:157px;">Out for Delivery</a>  
                    </td>
                    @elseif($order->status=="READY")
                    <td style="float: right;">
                        <a id="submit_btn" class="btn_full" href="javascript:void(0)" onclick="updateStatus('/seller/order/{{$order->id}}/complete');" style="float: right;width:157px;">Complete</a>  
                    </td>
                    @endif
                </tr>
            </table>
        </div><!-- End strip_list-->
        @endforeach
        @endif
        <!-- End strip_list-->
    </div>
</div><!-- End row -->   
