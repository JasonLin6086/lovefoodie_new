<div id="add_location_div">
    <div class="modal fade" id="add-loc-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="text-align:left;">
                <div class="modal-header">
                    <h2 class="modal-title">Add New Location</h2>
                </div>
                
                <div class="row">
                    <div class="col-sm-12">
                        <form id="add_loc_form" class="holder-control form-horizontal" role="form" data-toggle="validator" action="{{ url('/buyer/location') }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                        <input type="hidden" name="google_place_id" id='google_place_id' value='ChIJKZqLrjo1joARK4ljlvU5YLY'>
                        <input type="hidden" name="view" value='buyer.location-menu'>
                        <input id="address" class="controls" type="text" placeholder="Enter a location" value=''>
                        </form>
                    </div>
                
                    <div class="col-sm-12">
                        <div id="map"></div>
                    </div>
                    
                    <div class="col-sm-12 btn-row">
                        <div class="pull-right" style="margin-right:20px;">
                            <a id="submit_add_loc_btn" class="btn_full submit_btn" onclick="submitForm();">Submit</a>
                            <a id="cancel_add_loc_btn" class="btn_full cancel_btn" data-dismiss="modal">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End pickup table modal -->
</div>