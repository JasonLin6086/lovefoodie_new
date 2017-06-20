@extends('seller.master', ['tab1' => 'dish'])

@section('custom_css')
<!-- Gallery -->
<!--<link rel="stylesheet" type="text/css" media="screen" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">-->
<link href="{{ URL::asset('/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" >
<link href="{{ URL::asset('/css/style.css')}}" rel="stylesheet" type="text/css" >
<link href="{{ URL::asset('/css/slider-pro.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/bootstrap-imgupload.min.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ URL::asset('/css/bootstrap-tagsinput.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/fileinput.min.css') }}" rel ="stylesheet" type="text/css" media="all">
<link href="{{ URL::asset('/css/day-schedule-selector.css') }}" rel ="stylesheet" type="text/css" media="all">
<link href="{{ URL::asset('/css/custom/seller.dish-modify.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/fonts/font-awesome.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/fonts/font-awesome.css') }}" rel="stylesheet">

@endsection

@section('content')

<div class="container margin_60_35">
    <div class="row">
        <div class="col-md-12" id="detail">
            <div class="box_style_2">
                <h2 class="inner" id="edit_box">Edit Dishes</h2>
                <form class="holder-control form-horizontal" role="form" id="info" data-toggle="validator" class="form-control" action="{{ url('/seller/dish-modify') }}" method="post" enctype="multipart/form-data">
                    @if(!empty($dish))
                    <input type="hidden" name="id" value="{{$dish->id}}">
                    @else
                    <input type="hidden" name="id" value="">
                    @endif
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />                

                    <div class="row">
                        <div class="col-md-6">
                            <!--img start -->
                            <div id="Img_carousel" class="slider-pro">
                                @include('seller.dish-modify-data')
                            </div>
                            <!--img end-->
                        </div>
                        <div class="col-md-6">
                            <div class="form-group required has-feedback">
                                <label class="control-label col-sm-3" for="name">Name:</label>
                                <div class="col-sm-9">
                                    @if (!empty($dish))
                                    <input class="form-control holder-style holder-control" id="name" name="name" value="{{$dish->name}}"required>
                                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                    @else
                                    <input class="form-control holder-style holder-control" style="padding-left:10px;padding-right:23px;" id="name" placeholder="name" name="name" required>
                                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group required has-feedback">
                                <label class="control-label col-sm-3" for="price">Price:</label>
                                <div class="col-sm-9">
                                    @if (!empty($dish))
                                    <div class="input-group">
                                        <div class="input-group-addon">$</div>
                                        <input class="form-control" id="price" name="price" pattern="^\d+\.?\d*$" placeholder="10" value="{{$dish->price}}" required>
                                    </div>
                                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                    @else
                                    <div class="input-group">
                                        <div class="input-group-addon">$</div>
                                        <input class="form-control" id="price" name="price" pattern="^\d+\.?\d*$" placeholder="10" required>
                                    </div>
                                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="category">Category:</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="category">
                                        @if(!empty($dish))
                                        @foreach($categories as $category)
                                        @if(in_array($dish->category_id,$category->get()->pluck('id')->toArray()))
                                        <option value="{{$category->name}}" selected="selected">{{$category->name}}</option>
                                        @else
                                        <option value="{{$category->name}}"></option>
                                        @endif
                                        @endforeach
                                        @endif 
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="ingredients">Ingredients:</label>
                                <div class="col-sm-9">
                                    @if(!empty($dish))
                                    <input class="form-control input_s1" type="text" name="ingredients[]" id="ingredients" tabindex="2" value="{{implode(',',$dish->ingredient()->get()->pluck('word')->toArray())}}" data-role="tagsinput"/>
                                    @else
                                    <input class="form-control input_s1" type="text" name="ingredients[]" id="ingredients" tabindex="2" value="" data-role="tagsinput"/>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="description">Description:</label>
                                <div class="col-sm-9">
                                    @if(!empty($dish))
                                    <textarea class="form-control" rows="5" id="description" name="description" placeholder="">{{$dish->description}}</textarea>
                                    @else
                                    <textarea class="form-control" rows="5" id="description" name="description" placeholder=""></textarea>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="keywords">Keywords:</label>
                                <div class="col-sm-9">
                                    @if(!empty($dish))
                                    <input class="form-control input_s1" type="text" name="keywords" id="keywords" data-role="tagsinput" value="{{implode(',',$dish->keyword()->get()->pluck('word')->toArray())}}"/>
                                    @else
                                    <input class="form-control input_s1" type="text" name="keywords" id="keywords" value="" data-role="tagsinput"/>
                                    @endif
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="file-0a">Images:</label>
                                <div class="col-sm-10">
                                    <input id="file-0a" type="file" multiple data-min-file-count="1" name="image[]" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pull-right">
                        <a id="submit_btn" class="btn_full" onclick="document.getElementById('info').submit();">Confirm</a>
                        <a id="cancel_btn" class="btn_full" href="{{ url('/seller/dish-list') }}">Cancel</a>
                    </div>
                </form>
                <div id="summary_review">
                    <div id="general_rating">
                        11 Reviews
                        <hr>
                        @if(!empty($dish))
                        @foreach ($dish->review()->get() as $review)
                        <div class="review_strip_single">
                            <img src="{{ url('/').'/storage/'.$review->user['image']}}" alt="" class="img-circle">
                            <small> {{ $review->created_at}}</small>
                            <h3>
                                <div class="rating pull-right" id="rating">
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
                            </h3>

                            <h4>{{$review->user['name']}}</h4>

                            <p>
                                {{ $review->description}}
                            </p>
                        </div><!-- End review strip -->
                        @endforeach
                        @endif
                    </div><!-- End summary_review -->
                </div><!-- End box_style_1 -->
            </div>
        </div><!-- End row -->
    </div><!-- End container -->
    <!-- End Content =============================================== -->
    @endsection

    @section('custom_js')
    <script src="{{ URL::asset('/js/jquery.sliderPro.min.js') }}"></script>
    <script src="{{ URL::asset('/js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('/js/validator.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::asset('/js/fileinput.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('/js/bootstrap-imgupload.min.js') }}" type="text/javascript" ></script>
    <script src="{{ URL::asset('/js/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ URL::asset('/js/dishmodify-js/app.js') }}"></script>

    <script type="text/javascript">
                            $(document).ready(function ($) {
                            $("#file-0a").fileinput({
                            initialPreview: [
                                    @if(!empty($dish))
                                    @foreach($dish->dishImage()->get() as $image)
                                    '{{ url("/")."/storage/".$image->path }}',
                                    @endforeach
                                    @endif
                            ],
                                    initialPreviewAsData: true,
                                    initialPreviewConfig: [
                                            @if(!empty($dish))
                                            @foreach($dish->dishImage()->get() as $image)
                                    { key:  {{ $image->id }} },
                                            @endforeach
                                            @endif
                                    ],
                                    deleteUrl: "{{ url('/') }}/seller/dish-image/delete?_token={{ csrf_token() }}",
                                    previewFileType: "image",
                                    browseClass: "btn btn-success",
                                    browseLabel: "Pick Image",
                                    browseIcon: "<i class=\"glyphicon glyphicon-picture\"></i> ",
                                    removeClass: "btn btn-danger",
                                    removeLabel: "Delete",
                                    removeIcon: "<i class=\"glyphicon glyphicon-trash\"></i> ",
                                    uploadClass: "btn btn-info",
                                    uploadLabel: "Upload",
                                    uploadIcon: "<i class=\"glyphicon glyphicon-upload\"></i> "
                            });
                            $('#file-0a').on('filedeleted', function(event) {
                            @if(!empty($dish))
                                    @php ($dishid = $dish->id)
                                    @else
                                    @php ($dishid = -1)
                                    @endif

                                    $('#Img_carousel').load('/seller/dish-image-data/{{ $dishid }}', function(){}).hide().fadeIn("slow");
                            });
                            });
    </script>
    @endsection
