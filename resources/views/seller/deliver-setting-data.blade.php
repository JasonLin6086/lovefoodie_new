@if (!empty($seller))
@foreach ($seller->pickupMethod()->get() as $pickupmethod)
<div class="col-sm-12">
    <a href="#" class="pickup_list" data-project-id="{{$pickupmethod->id}}" >
        <div class="col-sm-10">
            <table class="pickup_list_tbl" id="test">
                <tr>
                    @if ($pickupmethod->type === 'WEEKDAY') 
                    <td width="50%">{{ $pickupmethod->weekday_msg }}</td>
                    @elseif ($pickupmethod->type === 'DATE')
                    <td width="50%">{{ $pickupmethod->date }}&nbsp;({{ $pickupmethod->weekday_msg }})</td>
                    @endif

                    @if ($pickupmethod->no_time === 0) 
                    <td width="50%">{{ $pickupmethod->start_time }} - {{ $pickupmethod->end_time }}</td>
                    @else
                    <td width="50%">Time To Be Decided</td>
                    @endif
                </tr>

                @foreach ($pickupmethod->PickupLocationMapping()->get() as $locationmapping)
                <tr>
                    <td colspan="2">{{$locationmapping->description}}&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;{{$locationmapping->address}}</td>
                </tr>
                @endforeach

            </table>
        </div>
        <div class="col-sm-2 pull-right">
            <input type="button" class="btn_full" id="removePickup" value="Delete"/>
        </div>
    </a>
</div>
@endforeach
@endif
