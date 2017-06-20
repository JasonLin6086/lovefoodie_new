@extends('seller.master', ['tab1' => 'dish'])

@section('custom_css')
<link href="{{ URL::asset('/css/custom/seller.dish-list.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/fonts/font-awesome.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="tab-container">
    <div class="row part">
        <div class="col-md-12">
            <div class='pull-right'>
                <a id="submit_btn" class="btn_full" href="/seller/dish-add"><i class="icon_plus"></i> Create new dishes</a>
            </div>
        </div>
    </div>

    <div class="row">
        @if (!empty($dishes))
        @foreach ($dishes as $dish)
        <div class="col-md-3">
            <a href="/seller/dish/{{$dish->id}}" class="strip_list">
                <!-- <div class="ribbon_1">Popular</div> -->
                <div class="desc" id="icon_position">
                    <div class="thumb_strip" id="text_position">
                        <img src="{{ $dish->dishImage()->first()? url('storage/'.$dish->dishImage()->get()[0]->path) : '' }}" alt="">
                    </div>
                    <h4>{{$dish->name}}</h4>
                    <div class="rating" id="rating" >
                        <input type="hidden" id="rate" value="{{$dish->rating}}">
                        <li class="undisplay icon-star-half-alt half_star_position" id="half_star0" ></li>
                        <i class="icon_star"></i>
                        <li class="undisplay icon-star-half-alt half_star_position" id="half_star1" ></li>
                        <i class="icon_star"></i>
                        <li class="undisplay icon-star-half-alt half_star_position" id="half_star2" ></li>
                        <i class="icon_star" ></i>
                        <li class="undisplay icon-star-half-alt half_star_position" id="half_star3" ></li>
                        <i class="icon_star"  ></i>
                        <li class="undisplay icon-star-half-alt half_star_position" id="half_star4" ></li>
                        <i class="icon_star"></i>
                    </div>

                    <div class="type">
                        @foreach($dish->category()->get() as $category)
                        {{$category->name}}
                        @endforeach
                    </div>
                    <span class="price_font">${{$dish->price}}</span><sapn class="storage_position">10 left</sapn>
                    <ul>
                        <li>Take away<i class="icon_check_alt2 ok"></i></li>
                        <li>Delivery<i class="icon_check_alt2 ok"></i></li>
                    </ul>
                </div><!-- End desc-->
            </a><!-- End strip_list-->
        </div>
        @endforeach
        @endif
    </div><!-- End row -->   
</div><!-- End container -->
@endsection

@section('custom_js')
<!--<script src="http://libs.useso.com/js/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>-->
<!--<script src="{{ URL::asset('/js/jquery-1.11.2.min.js') }}"></script>-->
<script src="{{ URL::asset('/js/dishmodify-js/app.js') }}"></script>
<script src="{{ URL::asset('/js/icon_star.js') }}"></script>
@endsection
