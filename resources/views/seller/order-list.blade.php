@extends('seller.master', ['tab1' => 'order'])

@section('custom_css')
    <link href="{{ URL::asset('/css/skins/square/grey.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('/css/custom/seller.order-list.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="tab-container" >
    <div class="row">
        <div class="col-md-12">
            <div id="filters_col">
                <a data-toggle="collapse" href="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters" id="filters_col_bt">Filters <i class="icon-plus-1 pull-right"></i></a>
                <div class="collapse" id="collapseFilters">
                    <form class="holder-control form-horizontal" id="info"role="form" data-toggle="validator" action="{{ url('/seller/order-list/filter') }}" method="get">
                    <hr>
                    <div class="filter_type">
                        <p>
                        <div class="col-md-2" style="width: 13%;">
                                <span>Order Type:</span>
                            </div>
                            <label class='filter'><input type="checkbox" name="type[]" value="REGULAR" checked class="icheck">Regular Order <small>(49)</small></label>
                            <label class='filter'><input type="checkbox" name="type[]" value="BID" checked class="icheck">Bid Order <small>(49)</small></label>
                        </p>                   
                    </div>
                    <div class="filter_type">
                        <p>
                            <div class="col-md-2"style="width: 13%;">
                                <span>Delivery Type:</span>
                            </div>
                            <label class='filter'><input type="checkbox" name="pickup_type[]" value="DELIVER" checked class="icheck">Deliver <small>(49)</small></label>
                            <label class='filter'><input type="checkbox" name="pickup_type[]" value="GROUP_PICKUP" checked class="icheck">Group Pickup <small>(49)</small></label>
                            <label class='filter'><input type="checkbox" name="pickup_type[]" value="STORE_PICKUP" checked class="icheck">Store Pickup <small>(49)</small></label>
                        </p>                        
                    </div>            
                    <div class="filter_type">
                        <p>
                            <div class="col-md-2"style="width: 13%;">
                                <span>Status:</span>
                            </div>
                            <label class='filter'><input type="checkbox" name="status[]" value="NEW" checked class="icheck">New <small>(49)</small></label>
                            <label class='filter'><input type="checkbox" name="status[]" value="ACCEPTED" checked class="icheck">Accepted <small>(49)</small></label>
                            <label class='filter'><input type="checkbox" name="status[]" value="REJECTED" checked class="icheck">Rejected <small>(49)</small></label>
                            <label class='filter'><input type="checkbox" name="status[]" value="READY" checked class="icheck">Ready <small>(49)</small></label>
                            <label class='filter'><input type="checkbox" name="status[]" value="COMPLETED" checked class="icheck">Completed <small>(49)</small></label>
                        </p>
                    </div>
                    
                    <div class="filter_type" style="height: 20px;">
                        <div class="pull-right">
                            <a id="submit_btn" class="btn_full" href="#"  onclick="filterOrders();return false;">Query</a>
                        </div>
                    </div>
                    </form>
                </div><!--End collapse -->
            </div><!--End filters col-->
        </div>
    </div><!-- End row -->   

    @include('seller.order-list-data')
    
</div>
@endsection

@section('custom_js')
<script>
    function getValueArray(name){
        var arr = [];
        $("input[name='"+name+"']").each(function() {
            if($(this).prop("checked")){
                arr.push($(this).val());
            }
        });
        return arr;
    }
    
    function filterOrders() {
        var formData = {
            'type'          : getValueArray('type[]'),
            'pickup_type'   : getValueArray('pickup_type[]'),
            'status'        : getValueArray('status[]'),
            '_token'        : "{{ csrf_token() }}",
            'view'          : 'seller.order-list-data'
        }; 
        
        $.ajax({
            type: 'GET',
            url: "{{url('/')}}" + '/seller/order-list/filter',
            data: formData,
            success: function (data) {
                $('#data-container').hide().html(data).fadeIn();
            },
            error: function () {
                $('#data-container').hide().html(data).fadeIn();
            }
        });
    }
</script>

<script>
    function updateStatus(URL) {
        $.ajax({
            type: 'PUT',
            url: "{{url('/')}}" + URL,
            data: {_token: "{{ csrf_token() }}", view: 'seller.order-list-data'},
            success: function (data) {
                $('#data-container').hide().html(data).fadeIn();
            },
            error: function (data) {
                //alert(JSON.stringify(data));
            }
        });
    }
</script>

@endsection