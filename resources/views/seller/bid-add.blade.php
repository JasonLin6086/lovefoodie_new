<div id="bid_div">
    <div class="modal fade" id="bid-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-popup" style="text-align:left;">
                <h2 class="nomargin_top">Participate In Bidding</h2>

                <!--Area for wish-->
                @if (!empty($wish))
                <div class="strip_list" style="box-shadow:none;">
                    <div class="row">
                        <div class="col-md-9 col-sm-9" style="width: 100%;">
                            <table style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td style="width:60%;">
                                            <div class="desc">
                                                @foreach($wish->user()->get() as $user)
                                                <div class="thumb_strip">
                                                    <a href="#"><img class="wish_img" src="{{url('storage/'.$user->image)}}" alt=""></a> 
                                                </div>
                                                <h3 style="margin-bottom: 15px;padding-top: 10px;">{{$user->name}}</h3>
                                                @endforeach
                                                <div class="type">{{$wish->address}}</div>

                                            </div>

                                        </td>
                                        <td>
                                <li class="wish_data">Status: 
                                    @if($wish->status=="open")
                                    <a class="status_open">{{$wish->status}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                    @else
                                    <a class="status_close">{{$wish->status}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                    @endif
                                    Bidders:<a class="bidder_num" >{{$wish->bid()->count()}}</a></li>
                                <li class="wish_data" >Bidding End Date:<a style="color: red;margin-left: 45px;">{{$wish->end_date}}</a></li></li>
                                <li class="wish_data">Deliver/Pickup Date:<a  style="color: red;margin-left: 25px;" >{{$wish->pickup_time}}</a></li>
                                </td>
                                </tr>
                                <tr>
                                    <td colspan ="2">
                                        <div class="wishlist_cont">
                                            <h3 class="wishlist_topic">{{$wish->topic}}</h3>
                                            <table class="wishlist_info">
                                                <tr class="c_style">
                                                    <td style="width: 25%;text-align: left;"><pre>&bull; Category:{{$wish->category->name}}</pre></td>
                                                    <td style="width: 25%;"><pre>&bull; Quantity:{{$wish->quantity}}</pre></td>
                                                    <td style="width: 30%;"><pre>&bull; Price Range:${{$wish->price_from}} ~ ${{$wish->price_to}}</pre></td>
                                                    <td><pre>&bull; {{$wish->pickup_method}}</pre></td>
                                                </tr>
                                            </table>

                                            <li class="describe">{{$wish->description}}</li>

                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- End row-->
                </div><!-- End strip_list--> 
                @endif

                <div class="row">
                    <form  class="holder-control form-horizontal" id="modal_form" role="form" data-toggle="validator" method="post">
                        @if(!empty($bid))
                        <input type="hidden" name="id" value="{{$bid->id}}" />
                        <input type="hidden" name="wish_id" value="{{$bid->wish_id}}" />
                        @else
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="wish_id" value="{{$wish->id}}" />
                        @endif
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input type="hidden" name="view" value="public.home-data" />

                        <div class="form-group form-horizontal">
                            <label class="control-label col-sm-4" for="bid_price"><span class="glyphicon glyphicon-star"></span>My bidding price:</label>
                            <div class="col-sm-6">
                                @if(!empty($bid))
                                <input type="text" class="form-control holder-style holder-control" id="bid_price" name="bid_price" value="{{$bid->bid_price}}" required>
                                @else
                                <input type="text" class="form-control holder-style holder-control" id="bid_price" name="bid_price" value="" required>
                                @endif
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>
                        </div>

                        <div class="form-group form-horizontal">
                            <label class="control-label col-sm-4" for="bid_description"><span class="glyphicon glyphicon-star"></span>Description:</label>
                            <div class="col-sm-6">
                                @if(!empty($bid))
                                <textarea class="form-control" rows="5" id="bid_description" name="bid_description" >{{$bid->bid_description}}</textarea>
                                @else
                                <textarea class="form-control" rows="5" id="bid_description" name="bid_description" ></textarea>
                                @endif
                            </div>
                        </div>

                        <div class="pull-right">
                            <a id="submit_btn" class="btn_full" onclick="modifyBid();">Submit</a>
                            <a id="reset_btn" class="btn_full" onclick="hideBidModel();">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- End pickup table modal -->
</div>

