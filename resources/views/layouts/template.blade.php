<!DOCTYPE html>
<!--[if IE 9]><html class="ie ie9"> <![endif]-->
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="pizza, delivery food, fast food, sushi, take away, chinese, italian food">

        <title>LoveFoodies - Love your foodies</title>

        <!-- Favicons-->
        <link rel="shortcut icon" href="{{ URL::asset('/image/favicon.ico') }}" type="image/x-icon">
        <link rel="apple-touch-icon" type="image/x-icon" href="{{ URL::asset('/image/apple-touch-icon-57x57-precomposed.png') }}">
        <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="{{ URL::asset('/image/apple-touch-icon-72x72-precomposed.png') }}">
        <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="{{ URL::asset('/image/apple-touch-icon-114x114-precomposed.png') }}">
        <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="{{ URL::asset('/image/apple-touch-icon-144x144-precomposed.png') }}">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        <script>
            window.Laravel = <?php
                echo json_encode([
                    'csrfToken' => csrf_token(),
                ]);
            ?>
        </script>

        <!-- Template CSS =============================================== -->
            @yield('template_css')
        <!-- End Template CSS =========================================== -->

        <link href="{{ URL::asset('/css/custom/template.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('/css/custom/public.search-input.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('/css/custom/map-location-add.css') }}" rel="stylesheet">
    </head>
    <body>

        <!--[if lte IE 8]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a>.</p>
        <![endif]-->
        <div id="app">
            <div id = "preloader">
                <div class="sk-spinner sk-spinner-wave" id="status">
                    <div class="sk-rect1"></div>
                    <div class="sk-rect2"></div>
                    <div class="sk-rect3"></div>
                    <div class="sk-rect4"></div>
                    <div class="sk-rect5"></div>
                </div>
            </div><!-- End Preload -->

            <!-- Header ================================================== -->
            <header id="header-bar">
                <div class="container-fluid" class="has-search-bar">
                    <div class="row">
                        <div class="col--md-1 col-sm-1 col-xs-1">
                            <a href="/" id="logo">
                                <img src="{{ URL::asset('/image/lovefoodiesnewlogo.png') }}" width="50" height="50" alt="" data-retina="true" class="hidden-xs">
                                <img src="{{ URL::asset('/image/lovefoodiesnewlogo.png') }}" width="30" height="30" alt="" data-retina="true" class="hidden-lg hidden-md hidden-sm">
                            </a>
                        </div>
                        <div id="search-bar-top" class="col--md-7 col-sm-7 col-xs-7">
                           {{-- @include('public.search-input') --}}
                        </div>
                        <nav class="col--md-4 col-sm-4 col-xs-4">
                            <a class="cmn-toggle-switch cmn-toggle-switch__htx open_close" href="javascript:void(0);"><span>Menu mobile</span></a>
                            <div class="main-menu">
                                <ul>
                                    <!-- Authentication Links -->
                                    @if (Auth::guest())
                                    <!--li><input type="submit" value="Become a Seller" class="btn_1" id="submit-newsletter_2" onclick="location.href='{{ url('seller/profile') }}';"></li-->
                                    <li><a href="{{ url('/login') }}">Login</a></li>
                                    <li><a href="{{ url('/register') }}">Sign Up</a></li>
                                    @else
                                        @if(!Auth::user()->isseller)
                                            <!--li><input type="submit" value="Become a Seller" class="btn_1" id="submit-newsletter_2" onclick="location.href='{{ url('seller/profile') }}';"></li-->
                                        @else
                                            <!--li><input type="submit" value="Manage My Store" class="btn_1" id="submit-newsletter_2" onclick="location.href='{{ url('seller/dish-list') }}';"></li-->
                                        @endif
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
@php
    $usericon = Auth::user()->image;
    $usericon = is_null($usericon) ? "/image/defaultusericon.png" : "/storage/".$usericon;
@endphp
                                                <img src="{{ url('/').$usericon }}" width="30" height="30" alt="" data-retina="true" style="border-radius:50%;margin-right:20px;" data-toggle="dropdown" >
                                                {{ Auth::user()->name }} <span class="caret"></span>
                                            </a>

                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="{{ url('/buyer/order-list') }}">My order</a></li>
                                                <li><a href="{{ url('/buyer/profile') }}">My profile</a></li>
                                                <li><a href="{{ url('/buyer/favorite') }}">My favorite</a></li>
                                                <li><a href="{{ url('/buyer/wish-list') }}">My wishes</a></li>
                                                <li>
                                                    <a href="{{ url('/logout') }}"
                                                       onclick="event.preventDefault();
                                                                document.getElementById('logout-form').submit();">
                                                        Logout
                                                    </a>

                                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                                        {{ csrf_field() }}
                                                    </form>
                                                </li>
                                            </ul>
                                        </li>
                                        <li id="cart" class="btn-group">
                                        @include('layouts.shopping-cart-icon')
                                        </li>
                                    @endif
                                </ul>
                            </div><!-- End main-menu -->
                        </nav>
                    </div><!-- End row -->
                </div><!-- End container -->
            </header>
            <!-- End Header =============================================== -->

            <!-- SubHeader =============================================== -->
                @yield('subheader')
            <!-- End SubHeader ============================================ -->

            <!-- Page Content ============================================= -->
                @yield('t1_content')
            <!-- End Page Content ========================================= -->

            <footer>
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <h3>Secure payments with</h3>
                            <div>
                                <img src="{{ URL::asset('/image/cards.png') }}" alt="" class="img-responsive">
                                <img style="margin-top: 10px" src="{{ URL::asset('/image/Applepay') }}" width="120px" >
                                <img style="margin-top: 10px" src="{{ URL::asset('/image/Googlepay') }}" width="120px">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <h3>About</h3>
                            <div><a href="{{ url('/about') }}">About us</a></div>
                            <div><a href="{{ url('/faq') }}">Faq</a></div>
                            <div><a href="{{ url('/contacts') }}">Contacts</a></div>
                            <!--div><a href="{{ url('/login') }}">Login</a></div>
                            <div><a href="{{ url('/register') }}">Register</a></div-->
                            <div><a href="{{ url('/terms-of-use') }}">Terms of Use</a></div>
                            <div><a href="{{ url('/delivery-cancellation') }}">Cancellation & Refund Policy</a></div>
                        </div>
                        <div class="col-md-4 col-sm-4" id="newsletter">
                            <h3>Newsletter</h3>
                            <p>
                                Join our newsletter to keep be informed about offers and news.
                            </p>
                            <div id="message-newsletter_2">
                            </div>

                            <form method="post" name="newsletter_2" id="newsletter_2">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <input name="emails" id="email_newsletter" type="email" value="" placeholder="Your email" class="form-control">
                                </div>
                                <!--a href="{{ url('/newsletter') }}" class="btn_1" id="submit-newsletter_2">Subscribe</a-->
                                <input type="submit" value="Subscribe" class="btn_1" id="submit-newsletter_2">
                            </form>

                        </div>
                        <!--div class="col-md-2 col-sm-3">
                            <h3>Settings</h3>
                            <div class="styled-select">
                                <select class="form-control" name="lang" id="lang">
                                    <option value="English" selected>English</option>
                                    <option value="French">French</option>
                                    <option value="Spanish">Spanish</option>
                                    <option value="Russian">Russian</option>
                                </select>
                            </div>
                            <div class="styled-select">
                                <select class="form-control" name="currency" id="currency">
                                    <option value="USD" selected>USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="RUB">RUB</option>
                                </select>
                            </div>
                        </div-->
                    </div><!-- End row -->
                    <div class="row">
                        <div class="col-md-12">
                            <div id="social_footer">
                                <ul>
                                    <li><a href="https://www.google.com"><i class="icon-facebook"></i></a></li>
                                    <li><a href="#0"><i class="icon-twitter"></i></a></li>
                                    <li><a href="#0"><i class="icon-youtube-play"></i></a></li>
                                    <!--li><a href="#0"><i class="icon-google"></i></a></li>
                                    <li><a href="#0"><i class="icon-instagram"></i></a></li>
                                    <li><a href="#0"><i class="icon-pinterest"></i></a></li>
                                    <li><a href="#0"><i class="icon-vimeo"></i></a></li-->
                                </ul>
                                <p>
                                    © Lovefoodies 2017
                                </p>
                            </div>
                        </div>
                    </div><!-- End row -->
                </div><!-- End container -->
            </footer>
            <!-- End Footer =============================================== -->
        </div>

        <!-- Google Map and select location modal -->
        <script src="{{ URL::asset('/js/custom/map-location-add.js')}}" type="text/javascript"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_PLACES_API_KEY')}}&libraries=places&callback=initMap"></script>
        <script src="{{ URL::asset('/js/custom/buyer.location-add-modal.js') }}"></script>

        <!-- Search Dropdown Menu -->
        <script src="{{ URL::asset('/js/custom/public.search-input.js') }}"></script>

        <!-- Template JS ================================================ -->
            @yield('template_js')
        <!-- End Template JS ============================================ -->

    </body>
</html>
