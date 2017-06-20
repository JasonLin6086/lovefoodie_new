<div id="modal_div">
    <div class="modal fade" id="account-modal" tabindex="-1" role="dialog" aria-labelledby="myRegister" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="text-align:left;">
                <div class="modal-header">
                    <h2 class="nomargin_top">Bank Account Info</h2>
                </div>

                <div class="modal-body account-modal-body">
                    <fieldset class="form-group fieldset holder-control form-horizontal">
                        <legend>Bank Info</legend>
                        <div class="form-group required has-feedback">
                            <label class="control-label col-sm-4" for="account_holder_name"><span class="glyphicon glyphicon-star"></span>Account Holder Name:</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control holder-style holder-control" id="account_holder_name" name="account_holder_name" required>
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>
                        </div>                        

                        <div class="form-group required has-feedback">
                            <label class="control-label col-sm-4" for="account_number"><span class="glyphicon glyphicon-star"></span>Account Number:</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control holder-style holder-control" id="account_number" name="account_number" required>
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>
                        </div>

                        <div class="form-group required has-feedback">
                            <label class="control-label col-sm-4" for="routing_number"><span class="glyphicon glyphicon-star"></span>Routing Number:</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control holder-style holder-control" id="routing_number" name="routing_number" required>
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-group fieldset holder-control form-horizontal">
                        <legend>Identity Info</legend>

                        <form id="bank_form" class="holder-control form-horizontal" role="form" data-toggle="validator" action="{{ url('/seller/payment/account') }}" method="post">
                            <input type="hidden" name="id" value="" />
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="view" value="seller.bank-info" />
                            <input type="hidden" id='identity_document_token' name="identity_document_token" value="" />
                            <input type="hidden" id='bank_account_token' name="bank_account_token" value="" />
                            <input type="hidden" id='social_security_number_token' name="social_security_number_token" value="" />                                  
                            <div class="form-group required has-feedback">
                                <label class="control-label col-sm-4" for="first_name"><span class="glyphicon glyphicon-star"></span>Legal Name:</label>
                                <div class="col-sm-7">
                                    <div class="col-sm-6 nopadding" id="first-name-group">
                                        {!! Form::label('firstName', 'First Name:') !!}
                                        {!! Form::text('first_name', null, [
                                        'class'                         => 'form-control',
                                        'required'                      => 'required',
                                        'data-parsley-required-message' => 'First name is required',
                                        'data-parsley-trigger'          => 'change focusout',
                                        'data-parsley-pattern'          => '/^[a-zA-Z]*$/',
                                        'data-parsley-minlength'        => '2',
                                        'data-parsley-maxlength'        => '32',
                                        'data-parsley-class-handler'    => '#first-name-group'
                                        ]) !!}
                                    </div>

                                    <div class="col-sm-6 nopadding" id="last-name-group">
                                        {!! Form::label('lastName', 'Last Name:') !!}
                                        {!! Form::text('last_name', null, [
                                        'class'                         => 'form-control',
                                        'required'                      => 'required',
                                        'data-parsley-required-message' => 'Last name is required',
                                        'data-parsley-trigger'          => 'change focusout',
                                        'data-parsley-pattern'          => '/^[a-zA-Z]*$/',
                                        'data-parsley-minlength'        => '2',
                                        'data-parsley-maxlength'        => '32',
                                        'data-parsley-class-handler'    => '#last-name-group'
                                        ]) !!}
                                    </div>                                
                                </div>
                            </div>

                            <div class="form-group required has-feedback">
                                <label class="control-label col-sm-4" for="date_of_birth"><span class="glyphicon glyphicon-star"></span>Date Of Birth:</label>
                                <div class="col-sm-7">
                                    <div class='input-group date' id='date_picker' name="date_of_birth">
                                        <input type='text' class="form-control" name='date_of_birth' id='time_pickers_date_' required/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                </div>
                            </div>
                        </form>

                        <div class="form-group required has-feedback" id="ssn-group">
                            <label class="control-label col-sm-4" for="social_security_number"><span class="glyphicon glyphicon-star"></span>Social Security Number:</label>
                            <div class="col-sm-7">
                                <!--<input type="text" class="form-control holder-style holder-control" id="social_security_number" name="social_security_number" maxlength='9' required>-->
                                <!--<span class="glyphicon form-control-feedback" aria-hidden="true"></span>-->
                                {!! Form::text('social_security_number', null, [
                                'id'                            => 'social_security_number',
                                'class'                         => 'form-control',
                                'required'                      => 'required',
                                'data-parsley-required-message' => 'Social Security Number is required',
                                'data-parsley-trigger'          => 'change focusout',
                                'data-parsley-minlength'        => '9',
                                'data-parsley-maxlength'        => '9',
                                'data-parsley-class-handler'    => '#ssn-group'
                                ]) !!}
                            </div>
                        </div>

                        <div class="form-group required has-feedback">
                            <label class="control-label col-sm-4" for="identity_document"><span class="glyphicon glyphicon-star"></span>Identity Document:</label>
                            <div class="col-sm-7">
                                <form method="post" id="fileinfo" name="fileinfo" ajax="true">
                                    <input type="file" id="identity_document" name="file" required />
                                </form>
                            </div>
                        </div>

                    </fieldset>

                    <div class="row">
                        <div class="col-md-12">
                            <span class="bank-errors" style="color: red;margin-top:10px;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="pull-right col-sm-4 col-md-4 btn-row">
                            <a id="bank_modal_submit_btn" class="btn_full submit_btn">Submit</a>
                            <a id="bank_modal_cancel_btn" class="btn_full cancel_btn" data-dismiss="modal"> Cancel </a>
                        </div>
                    </div>


                    {{-- Show $request errors after back-end validation --}}
                    <div class="col-md-6 col-md-offset-3">
                        @if($errors->has(''))
                        <div class="alert alert-danger fade in">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4>The following errors occurred</h4>
                            <ul>
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div> <!-- End pickup table modal -->
</div><!-- End modal_div -->

