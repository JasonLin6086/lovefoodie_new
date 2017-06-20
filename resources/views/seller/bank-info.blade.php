@extends('seller.master', ['tab1' => 'bank'])

@section('custom_css')
<link href="{{ URL::asset('/css/bootstrap-datetimepicker.v4.min.css')}}" media="all" rel ="stylesheet" type="text/css" />
<link href="{{ URL::asset('/css/custom/seller.bank-info.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="tab-container" >

    <div class='pull-right'>
        <a id="submit_btn" class="btn_full" href="#" data-toggle="modal" data-target="#account-modal">Edit Bank Account</a>
    </div>     

    <div class="row">
        <div class="col-md-12">
            <h3 class="nomargin_top title-word">Payments</h3>
            <div class="panel-group" id="payment">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#payment" href="#collapseOne">03/15/2017 - 03/20/2017  <div class='pull-right'><label class='money'>$235.15</label><i class="indicator icon_plus_alt2 "></i></div></a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class='row'>
                                <div class="col-md-2">
                                    <span>03/05/2017 22:50</span>
                                </div>
                                <div class="col-md-2">
                                    <span>$35.17</span>
                                </div>
                                <div class="col-md-2">
                                    <span>User Name</span>
                                </div>
                                <div class="col-md-2">
                                    <span><a href='#' name='order_detail' data-orderid='15'>Detail</a></span>
                                </div>
                                <div class="col-md-2">
                                    <span>Status</span>
                                </div>
                                <div class="col-md-2">
                                    <span>Note</span>
                                </div>                        
                            </div>
                            <hr>
                            <div class='row'>
                                <div class="col-md-2">
                                    <span>03/05/2017 22:50</span>
                                </div>
                                <div class="col-md-2">
                                    <span>$35.17</span>
                                </div>
                                <div class="col-md-2">
                                    <span>User Name</span>
                                </div>
                                <div class="col-md-2">
                                    <span><a href='#' name='order_detail' data-orderid='44'>Detail</a></span>
                                </div>
                                <div class="col-md-2">
                                    <span>Status</span>
                                </div>
                                <div class="col-md-2">
                                    <span>Note</span>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End panel-group -->
        </div>
    </div><!-- End row --> 
</div><!-- End container --> 

<div id=''>
    @include('seller.bank-info-account-modal')
</div>

<div id='order-detail-modal-div'>
    @include('seller.bank-info-order-detail-modal')
</div>

@endsection

@section('custom_js')
<script src="{{ URL::asset('/js/moment-with-locales.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('/js/bootstrap-datetimepicker.4.17.43.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('/js/bootstrap-formhelpers.min.js') }}"></script>
<script src="http://parsleyjs.org/dist/parsley.js" type="text/javascript" ></script>
<script src="https://js.stripe.com/v2/" type="text/javascript" ></script>
<script src="{{ URL::asset('/js/custom/seller.bank-info-account-modal.js') }}"></script>

<script>
// This identifies your website in the createToken call below
Stripe.setPublishableKey('{!! env('STRIPE_PUBLISH_KEY') !!}');
    
$(document).on('ready', function () {
    $('a[name^=order_detail]').on('click', function (e) {
        e.preventDefault();
        var orderId = $(this).data('orderid');
        $('#order-detail-modal-div').load("{{url('/seller/order')}}" + "/" + orderId + "?_token=" + "{{ csrf_token() }}" + "&view=seller.bank-info-order-detail-modal", function () {
            $('#order_modal').modal('toggle', $(this));
        });
    });
    
    $('#date_picker').datetimepicker({
        format: 'MM/DD/YYYY',
        allowInputToggle: true
    });
    
    window.ParsleyConfig = {
        errorsWrapper: '<div></div>',
        errorTemplate: '<div class="alert alert-danger parsley" role="alert"></div>',
        errorClass: 'has-error',
        successClass: 'has-success'
    };
    
    $('#bank_modal_submit_btn').click(function () {
        StripeUtil.init('{!! env('STRIPE_TEST_USER_PUBLISH_KEY') !!}');
        StripeUtil.beforeSubmit();
    });

});

</script>
@endsection
