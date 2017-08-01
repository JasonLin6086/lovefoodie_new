<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();
Route::get('/home', 'HomeController@index');
Route::get('tmp', function () {
    //$arr = DB::table('categories')->get([$columnName])->toArray();
    return view('tmp');
});

Route::get('/images/{filepath}', 'ImageController@show')->where('url', '(.*)');

//===== Public =====
Route::get('/', 'WebController@viewHome');
Route::get('login/{provider}', 'Auth\SocialAuthController@login');
Route::get('callback/{provider}', 'Auth\SocialAuthController@callback');
Route::get('register/confirm/{verify_token}', 'Auth\RegisterController@confirmEmail');

// Home page
Route::get('/sellers/rating', 'WebController@viewSellerRating');
Route::get('/sellers/nearby', 'WebController@viewSellerNearby');
Route::get('/dishes/rating', 'WebController@viewDishRating');
Route::get('/dishes/newest', 'WebController@viewDishNewest');
Route::get('/wishes', 'WebController@viewWishes');

Route::get('/sellers/{sellerid}', 'WebController@viewSellerDetail');
Route::get('/dishes/{dishid}', 'WebController@viewDishDetail');

Route::get('/search', 'WebController@search');
Route::get('/about', 'WebController@viewAbout');
Route::get('/faq', 'WebController@viewFaq');
Route::get('/contacts', 'WebController@viewContacts');
Route::get('/delivery-cancellation', 'WebController@viewDeliveryCancellation');
Route::get('/terms-of-use', 'WebController@viewTermsOfUse');
//Route::post('/', 'WebController@viewNewsletter');

// Mobile
Route::get('/mobile/about', 'WebController@viewMobileAbout');
Route::get('/mobile/privacy', 'WebController@viewMobilePrivacy');
Route::get('/mobile/term-of-service', 'WebController@viewMobileTermOfService');

//===== Seller Management =====
Route::group(['middleware' => ['auth']], function () {
    // 02 sellers
    Route::get('/seller/profile', 'WebController@sellerProfile');
    Route::post('/seller/profile', 'WebController@sellerProfileModify');

    // 03 dishes
    Route::get('/seller/dish-list', 'WebController@sellerDishList');
    Route::get('/seller/dish-add', 'WebController@sellerDishAdd');
    Route::get('/seller/dish/{dishid}', 'WebController@sellerDishData');
    Route::get('/seller/dish-image-data/{dishid}', 'WebController@sellerDishImageData');
    Route::post('/seller/dish-modify', 'WebController@sellerDishModify');
    Route::post('/seller/dish-image/delete', 'WebController@sellerDishImageDelete');

    // 05 orders
    Route::get('/seller/order-list', 'WebController@sellerOrderList');
    Route::get('/seller/order/{orderid}', 'WebController@sellerOrderById');
    Route::get('/seller/order-list/filter', 'WebController@sellerOrderListFiltered');
    Route::put('/seller/order/{orderid}/accept', 'WebController@sellerOrderAccept');
    Route::put('/seller/order/{orderid}/reject', 'WebController@sellerOrderReject');
    Route::put('/seller/order/{orderid}/deliver', 'WebController@sellerOrderDeliver');
    Route::put('/seller/order/{orderid}/complete', 'WebController@sellerOrderComplete');

    // 07 wishes / 08 bids
    Route::get('/seller/bid-modify', 'WebController@sellerBidData');
    Route::post('/seller/bid-modify', 'WebController@sellerBidModify');
    Route::get('/seller/wish-list', 'WebController@sellerWishList');

    // 09 deliver settings
    Route::get('/seller/deliver-setting', ['as' => 'seller/deliver-setting', 'uses' => 'WebController@sellerDeliverSetting']);
    Route::get('/seller/pcikup-method/{pickupmethodid}', 'WebController@sellerPickupMethodData');
    Route::delete('/seller/pickup-method/{pickupmethodid}', 'WebController@sellerPickupMethodDelete');
    Route::post('/seller/deliver-setting', 'WebController@sellerDeliverSettingModify');
    Route::post('/seller/pickupmethod-modify', 'WebController@sellerPickupMethodModify');

    // 17 payments
    Route::get('/seller/bank-info', 'WebController@sellerBankInfo');
    Route::post('/seller/payment/account', ['as' => '/seller/payment/account', 'uses' => 'WebController@sellerBankAccountAdd']);
});

//===== Buyer Management =====
Route::get('/confirm-success', 'WebController@viewConfirmSuccess');

Route::group(['middleware' => ['auth']], function () {
    // 01 users
    Route::get('/buyer/profile', 'WebController@buyerProfile');
    Route::post('/buyer/profile', 'WebController@buyerProfileModify');
    Route::post('/buyer/location', 'WebController@buyerLocationAdd');
    Route::delete('/buyer/location/{locationid}', 'WebController@buyerLocationDelete');

    // 04 shoppingcarts / 05 orders
    Route::get('/buyer/shoppingcart', 'WebController@buyerShoppingCartList');
    Route::post('/buyer/shoppingcart', 'WebController@buyerShoppingCartAdd');
    Route::put('/buyer/shoppingcart/{cartId}', 'WebController@buyerShoppingCartUpdate');
    Route::delete('/buyer/shoppingcart/{cartId}', 'WebController@buyerShoppingCartDelete');
    Route::get('/buyer/order-list', 'WebController@buyerOrderList');
    Route::post('/buyer/payment-list', 'WebController@buyerPaymentList');
    Route::get('/buyer/payment-list', 'WebController@buyerPaymentList');

    // 06 favorites
    Route::get('/buyer/favorite', 'WebController@buyerFavorite');
    Route::post('/buyer/favorite', 'WebController@buyerFavoriteAdd');
    Route::delete('/buyer/favorite/{favoriteid}', 'WebController@buyerFavoriteDelete');

    // 07 wishes
    Route::get('/buyer/wish-list', 'WebController@buyerWishList');
    Route::get('/buyer/wish-add', 'WebController@buyerWishAdd');
    Route::get('/buyer/wish/{wishid}', 'WebController@buyerWishDetail');
    Route::post('/buyer/wish-modify', 'WebController@buyerWishModify');

    // 10 reviews
    Route::get('/buyer/review/{orderid}', 'WebController@buyerReviewData');
    Route::post('/buyer/review/order/{orderid}', 'WebController@buyerReviewModify');

    // 11 order supports
    Route::get('/buyer/problem/{problemcode}', 'WebController@buyerProblem');
    Route::get('/buyer/order-support/{orderid}', 'WebController@buyerOrderSupport');
    Route::post('/buyer/order-support-add', 'WebController@buyerOrderSupportAdd');

    // 17 payments
    Route::post('/buyer/payment/card', ['as' => 'buyer/payment/card', 'uses' => 'WebController@buyerAddCard']);
    Route::put('/buyer/payment/card/{cardid}/default', 'WebController@buyerSetDefaultCard');
    Route::delete('/buyer/payment/card/{cardid}', 'WebController@buyerDeleteCard');
    Route::post('/buyer/payment/pay', 'WebController@buyerPay');

    //Route::get('/buyer/payment/pay', 'WebController@buyerPay');
    //Route::get('/buyer/payment/card', 'WebController@testAddCard');
    //Route::post('/buyer/payment/card2', ['as' => 'buyer/payment/add-card2', 'uses' => 'WebController@testAddCard2']);
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/helper/order-support/{orderid}', 'WebController@helperOrderSupportDetail');
});
