@extends('layouts.template-manage')
@section('custom_css')
<link href="{{ URL::asset('/css/custom/homepagestyle.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/bootstrap-social.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/custom/style.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/css/skins/square/grey.css')}}" rel="stylesheet">
@endsection

@section('t1_content')
<div id="position">
        <div class="container">
            <ul>
                <li><a href="#0">Home</a></li>
                <li><a href="#0">Category</a></li>
                <li>Page active</li>
            </ul>
        </div>
    </div><!-- Position -->
    <div class="container" style="margin-top: 20px;">
    <div class="row">
        <div class="col-md-8 col-md-offset-2" >
            <div class="panel panel-default" style="border-radius: 5px;">
                <div class="panel-heading" style="text-align: center;font-size: 30px;">Login</div>

                @include('partials.messages')

                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

<!--                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>-->

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4" style="width: 100%;">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                            </div>
                            <div style="width: 80%; margin-left:232px;">
                                <a class="btn btn-link" href="{{ url('/register') }}">Register</a>
                                <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                        <div style="position: relative; left: 232px;">
                            <a class="btn btn-block btn-social btn-google" href="{{ url('login/google') }}" ></a>

                            </br>
                            <a class="btn btn-block btn-social btn-facebook" href="{{ url('login/facebook') }}"></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
