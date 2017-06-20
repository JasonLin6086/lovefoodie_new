<div class="sp-slides">
@if (!empty($dish) && $dish->dishImage()->count()>0)

@foreach ($dish->dishImage()->get() as $dishImage)
<div class="sp-slide">
    <img alt="" class="sp-image" src="{{ $dishImage? url('storage/'.$dishImage->path) : '' }}"
         data-src="{{ $dishImage? url('storage/'.$dishImage->path) : '' }}"
         data-small="{{ $dishImage? url('storage/'.$dishImage->path) : '' }}"
         data-medium="{{ $dishImage? url('storage/'.$dishImage->path) : '' }}"
         data-large="{{ $dishImage? url('storage/'.$dishImage->path) : '' }}"
         data-retina="{{ $dishImage? url('storage/'.$dishImage->path) : '' }}">
</div>
@endforeach
@else
<div class="sp-slide">
    <img alt="" class="sp-image" src="{{ URL::asset('/image/blank.gif') }}" 
         data-src="{{ URL::asset('/image/slider_single_restaurant/1_medium.jpg') }}" 
         data-small="{{ URL::asset('/image/slider_single_restaurant/1_small.jpg') }}" 
         data-medium="{{ URL::asset('/image/slider_single_restaurant/1_medium.jpg') }}" 
         data-large="{{ URL::asset('/image/slider_single_restaurant/1_large.jpg') }}" 
         data-retina="{{ URL::asset('/image/slider_single_restaurant/1_large.jpg') }}">
</div>
@endif
</div>
@if (!empty($dish) && $dish->dishImage()->count()>0)
<div class="sp-thumbnails">
    @foreach ($dish->dishImage()->get() as $dishImage)
    <img alt="" class="sp-thumbnail" src="{{ $dishImage? url('storage/'.$dishImage->path) : '' }}">
    @endforeach
</div>
@else
<div class="sp-thumbnails">
    <img alt="" class="sp-thumbnail" src="{{ URL::asset('/image/slider_single_restaurant/1_medium.jpg') }}">
</div>
@endif

<script src="{{ URL::asset('/js/jquery-1.11.2.min.js') }}"></script>
<script src="{{ URL::asset('/js/jquery.sliderPro.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('#Img_carousel').sliderPro({
            width: 960,
            height: 500,
            fade: true,
            arrows: true,
            buttons: false,
            fullScreen: false,
            smallSize: 500,
            startSlide: 0,
            mediumSize: 1000,
            largeSize: 3000,
            thumbnailArrows: true,
            autoplay: false
        });
    });
</script>