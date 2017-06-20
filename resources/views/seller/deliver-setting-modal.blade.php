<div id="modal_div">
    <div class="modal fade" id="pickup_table" tabindex="-1" role="dialog" aria-labelledby="myRegister" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="text-align:left;"> <!--class="modal-content modal-popup"-->
                <div class="modal-header">
                    {{$seller}}
                @if (!empty($pickupmethod))
                <h2 class="nomargin_top">Modify Group Pickup Setting</h2>
                @else
                <h2 class="nomargin_top">New Group Pickup Setting</h2>
                @endif
                </div>
                
                <div class="modal-body">
                <form id="modal_form" role="form" data-toggle="validator" action="{{ url('/seller/pickupmethod-modify') }}" method="post">
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="form-group">
                        <div id="new_loc_hidden"></div>
                        <div id="delete_loc_hidden"></div>
                        <div class="row">
                            <div class="col-md-2">
                                Type:
                            </div>
                            <div class="col-md-3">
                                @if (!empty($pickupmethod))
                                    @if ($pickupmethod -> type == "DATE")
                                    <input type="radio" name="type" id="typeD" class='icheck' value="DATE" checked/>&nbsp;&nbsp;<label for="typeD"> by date</label>
                                    @else
                                    <input type="radio" name="type" id="typeD" class='icheck' value="DATE" />&nbsp;&nbsp;<label for="typeD"> by date</label>
                                    @endif
                                @else
                                    <input type="radio" name="type" id="typeD" class='icheck' value="DATE" />&nbsp;&nbsp;<label for="typeD"> by date</label>
                                @endif
                            </div>
                            <div class="col-md-7">
                                <div id="DATE" class="left_position">
                                    <div class="input-group date form_date col-md-5" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                        @if (!empty($pickupmethod))
                                        <input class="form-control" size="16" type="text" name="date" value="{{$pickupmethod -> date}}">
                                        @else
                                        <input class="form-control" size="16" type="text" name="date" value="">
                                        @endif
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-3">
                                @if (!empty($pickupmethod))
                                    @if ($pickupmethod->type == "WEEKDAY")
                                    <input type="radio" name="type" id="typeW" class='icheck' value="WEEKDAY" checked/>&nbsp;&nbsp;<label for="typeW"> by weekday</label>
                                    @else
                                    <input type="radio" name="type" id="typeW" class='icheck' value="WEEKDAY" />&nbsp;&nbsp;<label for="typeW"> by weekday</label>
                                    @endif
                                @else
                                    <input type="radio" name="type" id="typeW" class='icheck' value="WEEKDAY" />&nbsp;&nbsp;<label for="typeW"> by weekday</label>
                                @endif
                            </div>
                            <div class="col-md-7">
                                <div id="WEEKDAY" class="left_position">
                                    @php ($weekLabel = array('1'=>'Sun', '2'=>'Mon', '3'=>'Tue', '4'=>'Wed', '5'=>'Thr', '6'=>'Fri', '7'=>'Sat'))
                                    
                                    @if (!empty($pickupmethod))
                                        @php ($week = str_split($pickupmethod -> weekday))
                                        @foreach(range(1, 7) as $w)
                                            @if(in_array($w, $week))
                                            <label><input type="checkbox" name="weekday[]" value="{{$w}}" checked/> {{ $weekLabel[$w] }}</label>
                                            @else
                                            <label><input type="checkbox" name="weekday[]" value="{{$w}}"/> {{ $weekLabel[$w] }}</label>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach(range(1, 7) as $w)
                                            <label><input type="checkbox" name="weekday[]" value="{{$w}}"/> {{ $weekLabel[$w] }}</label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div id="time_part" class="row">
                            <div class="col-md-2">
                                Pickup Time:
                            </div>
                            
                            <div class="col-md-1">
                                <label>Start</label>
                            </div>
                            <div class="col-md-3" id="pickTime1">
                                <div class="bfh-timepicker" data-mode="12h" data-name="start_time" id="start_time" value="11"></div>
                            </div>

                            <div class="col-md-1">
                                <label>End</label>
                            </div>
                            <div class="col-md-3" id="pickTime2">
                                <div class="bfh-timepicker" data-mode="12h" data-name="end_time" id="end_time"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label><input type="checkbox" name="no_time" id="no_time" class='icheck' />&nbsp;&nbsp; Don't specify time</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                Locations:
                            </div>

                            
                            <div class="col-md-4">
                                @if (!empty($pickupmethod))
                                <span id='select'>Selected:</span>
                                <div class="ms-selectable">
                                    <ul id="selected" class="ms-list">
                                        @foreach ($pickupmethod->pickupLocationMapping()->get() as $pickuplocation)
                                        <li class="ms-elem-selectable" name="selected-opts" value="1">
                                            <label class="options">{{$pickuplocation->description}}</label>
                                            <input type="hidden" name="loc_mappings[]" value="1">
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @else
                                <span id='select'>Selected:</span>
                                <div class="ms-selectable">
                                    <ul id="selected" class="ms-list">
                                        <li class="ms-elem-selectable" name="selected-opts" value="1">
                                            <label class="options">No Locations</label>
                                            <input type="hidden" name="loc_mappings[]" value="1">
                                        </li>
                                    </ul>
                                </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <div class="row selectable">
                                    <span>Locations added before:</span> <a id="addloc_btn" class="btn_full"> Add Location </a>
                                </div>
                                <div id="new_loc">
                                    <table id="locationTable">
                                        <thead>
                                            <tr>
                                                <td>Description</td>
                                                <td>Address</td>
                                                <td></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input class="form-control newloc" type="text" id="add_description"/></td>
                                                <td><input class="form-control newloc" type="text" id="add_address"/>
                                                    <input class="form-control newloc" type="hidden" id="add_google_place_id"/>
                                                </td>
                                                <td><a id="add_loc" class="btn_full">Add</a>&nbsp;<a id="cancel_loc" class="btn_full">Cancel</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <ul id="selectable">
                                    @if (!empty($pickupmethod))
                                    @foreach ($pickupmethod->pickupLocationMapping()->get() as $locationmapping)
                                    --{{$locationmapping}}
                                    
                                        @if (!empty($pickuplocation))
                                            @foreach ($seller->pickuplocation()->get() as $location)
                                            ++{{$location}}
                                                @if (!in_array($location->id, str_split($pickuplocation->pickup_location_id)))
                                                <li class="ui-widget-content" name="selectable-opts" value="76" id="selectable_opts_5">
                                                    <label class="options">{{$location->description}}</label><i class="icon-cancel-5 pull-right" name="del_icon"></i>
                                                </li>
                                                @endif
                                            @endforeach
                                        @endif
                                        
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- **************************************************************************************************** -->
                    <div class="row">
                        <div class="pull-right" style="margin-right:10px;">
                            <a id="submit_btn" class="btn_full" onclick="document.getElementById('modal_form').submit();">Submit</a>
                            <a id="cancel_btn" class="btn_full"> Cancel </a>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div> <!-- End pickup table modal -->
</div><!-- End modal_div -->

<script src="{{ URL::asset('/js/bootstrap-formhelpers.min.js') }}"></script>