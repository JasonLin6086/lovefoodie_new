<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

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

});

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
