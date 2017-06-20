@extends('layouts.template-manage')

@section('t1_content')
<div class="container margin_60">
    <ul class="nav nav-tabs tab_size">
        <li class="{{ $tab1=='dish' ? 'active' : '' }}"><a href="{{ url('/seller/dish-list') }}" aria-expanded="{{ $tab1=='dish' ? 'true' : 'false' }}">Dishes</a></li>
        <li class="{{ $tab1=='profile' ? 'active' : '' }}"><a href="{{ url('/seller/profile') }}" aria-expanded="{{ $tab1=='profile' ? 'true' : 'false' }}">Profile</a></li>
        <li class="{{ $tab1=='order' ? 'active' : '' }}"><a href="{{ url('/seller/order-list') }}" aria-expanded="{{ $tab1=='order' ? 'true' : 'false' }}">Orders</a></li>
        <li class="{{ $tab1=='deliver-setting' ? 'active' : '' }}"><a href="{{ url('/seller/deliver-setting') }}" aria-expanded="{{ $tab1=='deliver-setting' ? 'true' : 'false' }}">Delivery Setting</a></li>
        <li class="{{ $tab1=='wish' ? 'active' : '' }}"><a href="{{ url('/seller/wish-list') }}" aria-expanded="{{ $tab1=='wish' ? 'true' : 'false' }}">Wishes</a></li>
        <li class="{{ $tab1=='bank' ? 'active' : '' }}"><a href="{{ url('/seller/bank-info') }}" aria-expanded="{{ $tab1=='bank' ? 'true' : 'false' }}">Bank Info</a></li>
    </ul>   
    @yield('content')
</div>
@endsection

