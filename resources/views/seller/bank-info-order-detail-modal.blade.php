<div id="modal_div">
    <div class="modal fade" id="order_modal" tabindex="-1" role="dialog" aria-labelledby="myRegister" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="text-align:left;"> <!--class="modal-content modal-popup"-->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h2 class="nomargin_top">Order Detail</h2>
                </div>
                <div class="modal-body order-modal-body">
                    @if (!empty($order))
                    <!--<div class="form-group strip_list">-->
                        <a href="Mydishes.html">
                            <img id="buyer_icon" class="seller_icon" src="{{ url('storage/'.$order->user->image) }}" alt="">
                        </a><span id="buy_name">{{$order->user->name}}</span>

                        <span class="order_time">
                            <h4>{{$order->pickup_type}}</h4>
                            {{$order->pickup_time}}<br>
                            @if($order->pickup_type=="GROUP_PICKUP")
                            {{$order->location_desc}}{{$order->address}}
                            @else
                            {{$order->address}}
                            @endif
                        </span>
                        <table class="table table-condensed">
                            <tbody>
                                @foreach ($order->orderDetail()->get() as $orderDetail)
                                <tr>
                                    <td style="width:16%; "><a href="Mydishes.html"><img class="dish_img"  src="{{ $orderDetail->dish->dishImage()->first()? url('storage/'.$orderDetail->dish->dishImage()->first()->path) : ''}}" alt=""></a></td>
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
                                    <td colspan="50"><span class="foot_format">Deliver Fee</span>
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
                                        <span class="cost_format">Status: <span class="completed">{{$order->status}}</span></span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    <!--</div>--><!-- End strip_list-->
                    @endif
                </div>
            </div>
        </div>
    </div> <!-- End pickup table modal -->
</div><!-- End modal_div -->
