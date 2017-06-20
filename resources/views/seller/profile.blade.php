@extends('seller.master', ['tab1' => 'profile'])

@section('custom_css')
<!--<link rel="stylesheet" type="text/css" media="screen" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">-->
<link href="{{ URL::asset('/css/jasny-bootstrap.min.css') }}" media="all" rel ="stylesheet" type="text/css"/>
<link href="{{ URL::asset('/css/bootstrap-select.min.css') }}" media="all" rel ="stylesheet" type="text/css"/>
<link href="{{ URL::asset('/css/style.css')}}" rel="stylesheet" type="text/css" >
<link href="{{ URL::asset('/css/sumoselect.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/custom/seller.profile.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="tab-container">
    <div class="main_title title_paddingto">
        <h2 class="nomargin_topp" style="padding-top:0">Edit Your Profile</h2>
    </div>

    <form class="holder-control form-horizontal" id="info"role="form" data-toggle="validator" action="{{ url('/seller/profile') }}" method="post" enctype="multipart/form-data">
        @if (!empty($seller))
        <input type="hidden" name="id" value="{{ $seller->id }}">
        @endif
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div>
            <label class="control-label col-sm-3">Icon:</label>
            <div class="form-group form-horizontal" id="icon_form">
                <div class="fileinput fileinput-new fileinput-style" data-provides="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                        @if (!empty($seller))
                        <img src="{{ url('storage/'.$seller->icon) }}" alt="...">
                        @else
                        <img src="" alt="...">
                        @endif
                    </div>
                    <div id="icon" class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                    <div>
                        <span class="btn btn-default btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="icon"></span>
                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group required has-feedback">
            <label class="control-label col-sm-3" for="kitchen_name"><span class="glyphicon glyphicon-star"></span>Kitchen Name:</label>
            <div class="col-sm-7">
                @if (!empty($seller))
                <input type="text" class="form-control holder-style holder-control" id="kitchen_name" name="kitchen_name" value="{{$seller->kitchen_name}}" required>
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                @else
                <input type="text" class="form-control holder-style holder-control" id="kitchen_name" name="kitchen_name" required>
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                @endif
            </div>
        </div>
        <div class="holder-control form-group required">
            <label class="control-label col-sm-3" for="category_id"><span class="glyphicon glyphicon-star"></span>Cuisine:</label>
            <div class="col-sm-7" id="checkbox_font">
                @if (!empty($seller))
                @php ($cateStr = $seller->sellerCategory()->get()->pluck('id')->toArray())
                @else
                @php ($cateStr = [])
                @endif
                <select class="form-control category-select" multiple="multiple" id="category_id" name="category_id[]">
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ in_array($category->id, $cateStr)? 'selected':''}}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group required has-feedback">
            <label class="control-label col-sm-3" for="phone_number"><span class="glyphicon glyphicon-star"></span>Contact Number:</label>
            <div class="col-sm-7">
                @if (!empty($seller))
                <input type="tel" id="phone_number" class="form-control holder-control" name="phone_number" value="{{$seller->phone_number}}">
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                @else
                <input type="tel" id="phone_number" class="form-control holder-control" name="phone_number">
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                @endif
            </div>
        </div>
        <div class="form-group required has-feedback">
            <label class="control-label col-sm-3" for="email"><span class="glyphicon glyphicon-star"></span>Email:</label>
            <div class="col-sm-7">
                @if (!empty($seller))
                <input type="text" id="email" name="email" class="form-control holder-control" value="{{$seller->email}}" required>
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                @else
                <input type="text" id="email" name="email" class="form-control holder-control" required>
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                @endif
            </div>
        </div>
        <div class="form-group required has-feedback">
            <label class="control-label col-sm-3" for="address"><span class="glyphicon glyphicon-star"></span>Store Address:</label>
            <div class="col-sm-7">
                @if (!empty($seller))
                <input type="hidden" name="google_place_id" id='google_place_id' value='{{$seller->google_place_id}}'>
                <input id="address" name="address" class="controls" type="text" placeholder="Enter a location" value='{{$seller->address}}'>
                @else
                <input type="hidden" name="google_place_id" id='google_place_id' value='ChIJKZqLrjo1joARK4ljlvU5YLY'>
                <input id="address" name="address" class="controls" type="text" placeholder="Enter a location" value=''>
                @endif
                <div id="map"></div>
            </div>
        </div> 
        <div class="form-group">
            <label class="control-label col-sm-3" for="description">Description:</label>
            <div class="col-sm-7">
                @if (!empty($seller))
                <textarea class="form-control" rows="5" id="description" name="description" >{{$seller->description}}</textarea>
                @else
                <textarea class="form-control" rows="5" id="description" name="description" ></textarea>
                @endif
            </div>
        </div> 
        <div class="pull-right col-sm-4 col-md-4">
            <a id="submit_btn" class="btn_full" onclick="document.getElementById('info').submit();">Submit</a>
            <a id="reset_btn" class="btn_full" href="{{ url('/seller/profile') }}" >Cancel</a>
        </div>
    </form>
</div>

@if (!empty($seller))
@php ($loc = $seller->location()->first())
<input type="hidden" name="latitude" id='latitude' value='{{$loc->latitude}}'>
<input type="hidden" name="longitude" id='longitude' value='{{$loc->longitude}}'>
@endif

@endsection

@section('custom_js')
<script src="{{ URL::asset('/js/validator.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('/js/jasny-bootstrap.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('/js/jquery.sumoselect.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('/js/custom/map-location-add.js')}}" type="text/javascript"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_PLACES_API_KEY')}}&libraries=places&callback=initMap" async defer></script>
<script>
$(function () {
    $('.category-select').SumoSelect({
        placeholder: 'Select Your Store Categories',
        csvDispCount: 10
    });
});
</script>
@endsection