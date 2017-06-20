@extends('layouts.template-manage')

@section('t1_content')
<div class="container margin_60">
    <ul class="nav nav-tabs tab_size">
        <li class="{{ $tab1=='profile' ? 'active' : '' }}"><a href="{{ url('/buyer/profile') }}" aria-expanded="{{ $tab1=='profile' ? 'true' : 'false' }}">Profile</a></li>
        <li class="{{ $tab1=='order' ? 'active' : '' }}"><a href="{{ url('/buyer/order-list') }}" aria-expanded="{{ $tab1=='order' ? 'true' : 'false' }}">Order</a></li>
        <li class="{{ $tab1=='favorite' ? 'active' : '' }}"><a href="{{ url('/buyer/favorite') }}" aria-expanded="{{ $tab1=='favorite' ? 'true' : 'false' }}">Favorite Seller</a></li>
        <li class="{{ $tab1=='wish' ? 'active' : '' }}"><a href="{{ url('/buyer/wish-list') }}" aria-expanded="{{ $tab1=='wish' ? 'true' : 'false' }}">My Wishes</a></li>
    </ul> 
    @yield('content')
</div>
@endsection