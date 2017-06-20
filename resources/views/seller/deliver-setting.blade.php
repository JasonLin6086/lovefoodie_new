@extends('seller.master', ['tab1' => 'deliver-setting'])

@section('custom_css')
<!-- GOOGLE WEB FONT -->
<!--<link href='https://fonts.googleapis.com/css?family=Lato:400,700,900,400italic,700italic,300,300italic' rel='stylesheet' type='text/css'>-->
<link href="{{ URL::asset('/css/skins/square/grey.css')}}" rel="stylesheet">
<link href="{{ URL::asset('/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/bootstrap-formhelpers.min.css') }}" rel="stylesheet">
<!--<link href="{{ URL::asset('/css/bootstrap-glyphicons.css') }}" rel="stylesheet">-->
<link href="{{ URL::asset('/css/day-schedule-selector.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/custom/seller.deliver-setting.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/fonts/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/fontello/css/fontello.css') }}" rel="stylesheet">
<!--<link href="{{ URL::asset('/css/style.css') }}" rel="stylesheet">-->
@endsection

@section('content')
<div class="tab-container" id="info">
    <form id="deliver_setting_form"role="form" data-toggle="validator" action="{{ url('/seller/deliver-setting') }}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="container part" id="delivery">
            <h2>Delivery</h2>
            @if (!empty($seller) && !empty($deliverSetting))
            <div class="row" style="margin-bottom:20px;">
                <label class="control-label col-sm-3"><input type="checkbox" class="icheck" name="is_free_delivery" "{{ $deliverSetting->is_free_delivery? checked: ''}}"/> Free delivery from</label>
                <div class="col-sm-2">
                    <div class="input-group">
                        <div class="input-group-addon"><b>$</b></div>
                        <input type="text" class="form-control" pattern="^\d+\.?\d*$" name="free_delivery_price" value="{{$deliverSetting->free_delivery_price}}" style="text-align: center; font-weight: bold">
                    </div>
                </div>
                <label class="control-label col-sm-1">within</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" name="free_delivery_mile" value="{{$deliverSetting->free_delivery_mile}}" style="text-align: center; font-weight: bold"/>
                </div>
                <label class="control-label col-sm-1">miles</label>
            </div>

            <div class="row">
                @if ($deliverSetting->is_delivery_fee)
                <label class="control-label col-sm-3"><input type="checkbox" class="icheck" name="is_delivery_fee" checked/> Delivery fee</label>
                @else
                <label class="control-label col-sm-3"><input type="checkbox" class="icheck" name="is_delivery_fee"/> Delivery fee</label>
                @endif
                <div class="col-sm-5">
                    <table id="deliveryTable">
                        <thead>
                            <tr>
                                <td>Miles Within</td>
                                <td>Delivery Fee</td>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($seller->deliverFeeSetting()->count() > 0)
                            @foreach ($seller->DeliverFeeSetting()->get() as $DeliverFee)
                            <tr>
                                <td><input class="form-control" type="text" name="miles_within[]" value="{{ $DeliverFee->miles_within }}" style="text-align: center"/></td>
                                <td><input class="form-control" type="text" name="price[]" value="{{ $DeliverFee->price }}" style="text-align: center"/></td>
                                <td><input type="button" class="btn_full" id="delbutton" value="Delete" onclick="delRow(this)"/></td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td><input class="form-control" type="text" name="miles_within[]" value=""/></td>
                                <td><input class="form-control" type="text" name="price[]" value=""/></td>
                                <td><input type="button" class="btn_full" id="delbutton" value="Delete" onclick="delRow(this)"/></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                </div>
                <div class="col-sm-2" style="margin-top:20px;">
                    <input type="button" id="creatrow" class="btn_full" value="Add Row" onclick="insertRow()"/>
                </div>
            </div>
            @endif
        </div>

        <div class="container part" id="pickup">
            <h2 class="pickup_title">Group Pickup</h2>
            <div class="pull-right pickup_btn">
                <a id="add_btn" class="btn_full">New Pickup Method</a>
            </div>
            <div id='methods-div'>
            @include('seller.deliver-setting-data')
            </div>
        </div>
        <div class="container part" id="open-hours">
            <h2>Store Pickup</h2>
            <div class="row">
                <label class="control-label col-sm-3 open_hour">Store Open Hours:</label>
                <div id="day-schedule" style="margin-bottom:20px;"></div>
                <input type="hidden" id="store_open_hour" name="store_open_hour" value="{{$deliverSetting->store_open_hour}}">
            </div>
            <div class="row">
                @if ($deliverSetting->is_at_store)
                <label class="control-label col-sm-3"><input type="checkbox" class="icheck" name="is_at_store" checked/> Pickup at store, order before</label>
                @else
                <label class="control-label col-sm-3"><input type="checkbox" class="icheck" name="is_at_store"/> Pickup at store, order before</label>
                @endif
                <div class="col-sm-2">
                    <input type="text" class="form-control" name="order_before_hour" value="{{$deliverSetting->order_before_hour}}" style="text-align: center"/>
                </div>
                <label class="control-label col-sm-1">hours</label>
            </div>
        </div>
        <div class="container" id="">
            <div class="pull-right col-sm-2" style="margin-top:20px;">
                <input type="button" id="submit_btn" class="btn_full" value="Save" onclick="submitDeliverSetting();"/>
            </div>
        </div>
    </form>
</div><!-- End of Page -->

<div id="modal_container">
@include('seller.deliver-setting-modal')
</div>

@endsection

@section('custom_js')
<script src="{{ URL::asset('/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="{{ URL::asset('/js/bootstrap-formhelpers.min.js') }}"></script>
<script src="{{ URL::asset('/js/day-schedule-selector.js') }}"></script>

<script>
                    $(document).on('ready', function () {
                        setupModal();   
                        $(".pickup_list").on('click', function(e) {
                            e.preventDefault();
                            var projectId = $(this).data('projectId');
                            var $target = $(e.target);
                            if ($target.is("input")) {
                                //alert("delete!!");
                                deletePickupMethod(projectId);
                            } else {
                                $('#modal_container').load("{{url('/seller/pcikup-method')}}" + "/" + projectId+"?_token="+"{{ csrf_token() }}"+"&view=seller.deliver-setting-modal", function(){
                                    setupModal();
                                    $('#pickup_table').modal('toggle', $(this));
                                });
                            }
                        });
                        
                        $("#add_btn").on('click', function(e) {
                            $('#modal_container').load("{{url('/seller/pcikup-method')}}" + "/" + -1+"?_token="+"{{ csrf_token() }}"+"&view=seller.deliver-setting-modal", function(){
                                setupModal();
                                $('#pickup_table').modal('toggle', $(this));
                            });
                        });
                    });
                    
                    function setupModal(){
                        $('#modal_div').unbind('click');
                        
                        $('#modal_div').on('click', 'input[name$="type"]', function (e) {
                            var test = $(this).val();
                            $("div.left_position").hide();
                            $("#" + test).show();
                        });

                        $('#modal_div').on('click', '#cancel_btn', function (e) {
                            $('#pickup_table').modal('hide');
                        });

                        $('#modal_div').on('click', '#addloc_btn', function (e) {
                            $("#locationTable").show();
                            $("#addloc_btn").hide();
                        });

                        $('#modal_div').on('click', '#add_loc', function (e) {
                            e.preventDefault();
                            addNewLoc();
                            $("#locationTable").hide();
                            $("#addloc_btn").show();
                        });

                        $('#modal_div').on('click', '#cancel_loc', function (e) {
                            e.preventDefault();
                            var desc = document.getElementById('add_description');
                            var addr = document.getElementById('add_address');
                            var placeId = document.getElementById('add_google_place_id');
                            desc.value = "";
                            addr.value = "";
                            placeId.value = "";
                            $("#locationTable").hide();
                            $("#addloc_btn").show();
                        });

                        $('#modal_div').on('click', 'li[name=selectable-opts]', function (e) {
                            e.preventDefault();
                            var $target = $(e.target);
                            if ($target.hasClass('icon-cancel-5')) {
                                deleteSelectable($(this));
                            } else {
                                selectableClicked($(this));
                            }
                        });

                        $('#modal_div').on('click', 'li[name=selected-opts]', function (e) {
                            selectedClicked($(this));
                        });

                        $('#modal_div').on('click', "#no_time", function (e) {
                            if ($('#no_time').is(':checked')) {
                                $("#pickTime1").addClass("disabledbutton");
                                $("#pickTime2").addClass("disabledbutton");
                            } else {
                                $("#pickTime1").removeClass("disabledbutton");
                                $("#pickTime2").removeClass("disabledbutton");
                            }
                        });
                        
                        $('.form_date').datetimepicker({
                            weekStart: 1,
                            todayBtn: 1,
                            autoclose: 1,
                            todayHighlight: 1,
                            startView: 2,
                            minView: 2,
                            forceParse: 0
                        });
                    }

                    function selectableClicked(e) {
                        var ul = document.getElementById('selected');
                        var li = document.createElement("li");
                        var lab = document.createElement("Label");
                        var hidden = document.createElement("input");
                        hidden.setAttribute("type", "hidden");
                        hidden.setAttribute("name", "loc_mappings[]");
                        hidden.setAttribute("value", e.attr("value"));
                        lab.innerHTML = e.find(".options").html();
                        lab.setAttribute("class", "options");
                        li.appendChild(lab);
                        li.appendChild(hidden);
                        li.setAttribute("class", "ms-elem-selectable");
                        li.setAttribute("name", "selected-opts");
                        li.setAttribute("value", e.attr("value"));
                        li.onclick = function () {
                            selectedClicked($(this));
                        };
                        ul.appendChild(li);
                        e.remove();
                    }

                    function selectedClicked(e) {
                        addSelectable(e.find(".options").html(), e.attr("value"));
                        e.remove();
                    }

                    function addSelectable(desc, val) {
                        var ul = document.getElementById('selectable');
                        var li = document.createElement("li");
                        var lab = document.createElement("Label");
                        var icon = document.createElement("i");
                        lab.innerHTML = desc;
                        lab.setAttribute("class", "options");
                        icon.setAttribute("class", "icon-cancel-5 pull-right");
                        icon.setAttribute("name", "del_icon");
                        li.appendChild(lab);
                        li.appendChild(icon);
                        li.setAttribute("id", "selectable_opts_" + val);
                        li.setAttribute("class", "ui-widget-content");
                        li.setAttribute("name", "selectable-opts");
                        li.setAttribute("value", val);
                        ul.appendChild(li);

                        $("#selectable_opts_" + val).on('click', function (e) {
                            e.preventDefault();
                            var $target = $(e.target);
                            if ($target.hasClass('icon-cancel-5')) {
                                deleteSelectable($(this));
                            } else {
                                selectableClicked($(this));
                            }
                        });
                    }

                    function addNewLoc() {
                        var parent = document.getElementById('new_loc_hidden');
                        var desc = document.getElementById('add_description');
                        var addr = document.getElementById('add_address');
                        var placeId = document.getElementById('add_google_place_id');
                        var hidden = document.createElement("input");
                        var id = new Date().valueOf();
                        hidden.setAttribute("type", "hidden");
                        hidden.setAttribute("name", "new_loc[]");
                        hidden.setAttribute("id", "new_loc_" + id);

                        var val = "{\"id\":\"" + id + "\"" +
                                ",\"description\":\"" + desc.value + "\"" +
                                ",\"address\":\"" + addr.value + "\"" +
                                ",\"google_place_id\":\"" + placeId.value + "\"}";
                        hidden.setAttribute("value", val);
                        parent.appendChild(hidden);
                        addSelectable(desc.value, id);

                        desc.value = "";
                        addr.value = "";
                        placeId.value = "";
                    }

                    function deleteSelectable(e) {
                        var del = document.getElementById("delete_loc_hidden");
                        var hidden = document.createElement("input");
                        var id = e.attr("value");

                        var add = document.getElementById("new_loc_" + id);
                        if (add) {
                            add.remove();
                        } else {
                            hidden.setAttribute("type", "hidden");
                            hidden.setAttribute("name", "delete_loc[]");
                            hidden.setAttribute("id", "delete_loc_" + id);
                            hidden.setAttribute("value", id);
                            del.appendChild(hidden);
                        }
                        e.remove();
                    }

                    function submitDeliverSetting() {
                        $("#store_open_hour").val(JSON.stringify($("#day-schedule").data('artsy.dayScheduleSelector').serialize()));
                        $("#deliver_setting_form").submit();
                    }

                    $(document).on('ready', function () {
                        (function ($) {
                            $("#day-schedule").dayScheduleSelector({
                                 days: [1, 2, 3, 4, 5, 6, 7],
                                 interval: 30,
                                 startTime: '00:00',
                                 endTime: '24:00'
                            });
                            $("#day-schedule").on('selected.artsy.dayScheduleSelector', function (e, selected) {
                                console.log(selected);
                            })
                            $("#day-schedule").data('artsy.dayScheduleSelector').deserialize(
                                "{{$deliverSetting->store_open_hour}}"
                            );
                        })($);
                    });

                    function insertRow()
                    {
                        var x = document.getElementById('deliveryTable');
                        // deep clone the targeted row
                        var new_row = x.rows[1].cloneNode(true);
                        // get the total number of rows
                        var len = x.rows.length;
                        // set the innerHTML of the first row 
                        // new_row.cells[0].innerHTML = len;

                        // grab the input from the first cell and update its ID and value
                        var inp1 = new_row.cells[0].getElementsByTagName('input')[0];
                        inp1.id += len;
                        inp1.value = '';

                        // grab the input from the first cell and update its ID and value
                        var inp2 = new_row.cells[1].getElementsByTagName('input')[0];
                        inp2.id += len;
                        inp2.value = '';

                        // append the new row to the table
                        x.appendChild(new_row);
                    }

                    function delRow(row)
                    {
                        var i = row.parentNode.parentNode.rowIndex;
                        if (i > 1) {
                            document.getElementById('deliveryTable').deleteRow(i);
                        }
                        else if (i = 1) {
                            document.getElementById('deliveryTable').deleteRow(i);
                            document.getElementById('deliveryTable').insertRow();
                        }
                    }

                    $("#removePickup").click(function () {
                        $(this).parent().closest('a').hide();
                    });

</script>

<script>
    function deletePickupMethod(methodid) {
        var url = "{{ url('/seller/pickup-method') }}" +"/"+ methodid;
        $.ajax({
            type: 'DELETE',
            url: url,
            data: {_token: "{{ csrf_token() }}", view: 'seller.deliver-setting-data'},
            success: function (data) {
                $('#methods-div').hide().html(data).fadeIn();
            },
            error: function (data) {
                alert("Error Occurred!");
            }
        });
    }
</script>
@endsection
