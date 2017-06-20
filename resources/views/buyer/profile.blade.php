@extends('buyer.master', ['tab1' => 'profile'])

@section('custom_css')
<!--<link rel="stylesheet" type="text/css" media="screen" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">-->
<link href="{{ URL::asset('/css/day-schedule-selector.css') }}" media="all" rel ="stylesheet" type="text/css" />
<link href="{{ URL::asset('/css/jasny-bootstrap.min.css') }}" media="all" rel ="stylesheet" type="text/css"/>
<link href="{{ URL::asset('/css/bootstrap-select.min.css') }}" media="all" rel ="stylesheet" type="text/css"/>
<link href="{{ URL::asset('/css/custom/buyer.profile.css') }}" rel="stylesheet">
<!--<link href="{{ URL::asset('/css/style.css')}}" rel="stylesheet" type="text/css">-->
@endsection

@section('content')

<div class="main_title title_paddingto">
    <h2 class="nomargin_topp" style="padding-top:0">Edit Your Profile</h2>
</div>
<form class="holder-control form-horizontal" id="info"role="form" data-toggle="validator" action="{{ url('/buyer/profile') }}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value='{{ $user->id }}'>
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

    <div>
        <label class="control-label col-sm-3">Icon:</label>
        <div class="form-group form-horizontal" id="icon_form">
            <div class="fileinput fileinput-new fileinput-style" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                    @if (!empty($user))
                    <img src="{{ $user? url('storage/'.$user->image) : '' }}" alt="..."> 
                    @else
                    <img src="" alt="...">
                    @endif
                </div>
                <div id="icon" class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                <div>
                    <span class="btn btn-default btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="image"></span>
                    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group required has-feedback">
        <label class="control-label col-sm-3" for="name"><span class="glyphicon glyphicon-star"></span>User Name:</label>
        <div class="col-sm-7 col-md-7" >
            @if (!empty($user))
            <input type="text" class="form-control holder-style holder-control" id="name" name="name" value="{{$user->name}}" required>
            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
            @else
            <input type="text" class="form-control holder-style holder-control" id="name" name="name" required>
            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
            @endif
        </div>
    </div>

    <div class="form-group required has-feedback">
        <label class="control-label col-sm-3" for="email"><span class="glyphicon glyphicon-star"></span>Email:</label>
        <div class="col-sm-7 col-md-7">
            @if (!empty($user))
            <input type="text" id="email" name="email" class="form-control holder-control" value="{{$user->email}}" required>
            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
            @else
            <input type="text" id="email" name="email" class="form-control holder-control" required>
            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
            @endif
        </div>
    </div>

    @if ($user->provider === "password")
    <div class="form-group required has-feedback">
        <label class="control-label col-sm-3" for="password">Password:</label>
        <div class="col-sm-7 col-md-7">
            <input type="checkbox" onclick="var input = document.getElementById('password');
                    if (this.checked) {
                        input.disabled = false;
                        input.focus();
                    } else {
                        input.disabled = true;
                    }" >Change Password
            <input type="text" id="password" name="password" class="form-control holder-control" value="" disabled>
        </div>
    </div>
    @endif

    <div class="form-group required has-feedback">
        <label class="control-label col-sm-3" for="phone_number">Contact Number:</label>
        <div class="col-sm-7 col-md-7">
            @if (!empty($user))
            <input type="text" id="phone_number" name="phone_number" class="form-control holder-control" value="{{$user->phone_number}}">
            @else
            <input type="text" id="phone_number" name="phone_number" class="form-control holder-control">
            @endif
        </div>
    </div>

    <div class="form-group required has-feedback">
        <label class="control-label col-sm-3" for="address">My Address:</label>
        <div class="col-sm-7 col-md-7">
            <input type="hidden" name="google_place_id" id='google_place_id' value='ChIJKZqLrjo1joARK4ljlvU5YLY'>
            <input id="address" class="controls" type="text" placeholder="Enter a location" value='{{$user->address}}'>
            <div id="map"></div>
        </div>
    </div>

    <div class="pull-right col-sm-4 col-md-4">
        <a id="submit_btn" class="btn_full" onclick="document.getElementById('info').submit();">Submit</a>
        <a id="reset_btn" class="btn_full" href="{{ url('/buyer/profile') }}" >Cancel</a>
    </div>
</form>
@endsection

@section('custom_js')
<!--<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>-->
<script src="{{ URL::asset('/js/bootstrap-select.min.js')}}" type="text/javascript" ></script>
<script src="{{ URL::asset('/js/moment.min.js')}}" type="text/javascript" ></script>
<script src="{{ URL::asset('/js/day-schedule-selector.js')}}" type="text/javascript" ></script>
<script src="{{ URL::asset('/js/validator.min.js')}}" type="text/javascript" ></script>
<script src="{{ URL::asset('/js/jquery.validate.min.js')}}" type="text/javascript" ></script>
<script src="{{ URL::asset('/js/jasny-bootstrap.js')}}" type="text/javascript" ></script>
<script src="{{ URL::asset('/js/jquery.magnific-popup.js')}}" type="text/javascript" ></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_PLACES_API_KEY')}}&libraries=places&callback=initMap" async defer></script>
<script src="{{ URL::asset('/js/custom/map-location-add.js')}}" type="text/javascript"></script>
<!--
<script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.js"></script>
<script src="//cdn.bootcss.com/magnific-popup.js/1.1.0/jquery.magnific-popup.js"></script>-->
@endsection
