<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//URL::forceSchema('https');

//Route::

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers'], function ($api) {

    // public
    $api->post('/newsletter', 'SubscribeController@store');


    // 01 users
    $api->post('register', 'Auth\RegisterController@registerForApp');
    $api->post('loginWithToken', 'Auth\SocialAuthController@loginWithToken');
    $api->post('loginWithPassword', 'Auth\LoginController@loginWithPassword');
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->get('users/me', 'UserController@showMe');
        $api->put('users/{id}', 'UserController@update');
        $api->post('users/{userid}/location', 'UserController@addLocation');
        $api->delete('users/{userid}/location/{locationid}', 'UserController@deleteLocation');
        $api->put('users/{userid}/location/{locationid}/default', 'UserController@assignDefaultLocation');
        $api->get('users/{userid}/location/default', 'UserController@viewDefaultLocation');
        $api->put('users/{userid}/phone-number', 'UserController@updatePhoneNumber');
        $api->put('users/{userid}/phone-number/confirm', 'UserController@confirmPhoneNumber');
        $api->post('logout', 'Auth\LoginController@logoutApp');
    });

    // 02 sellers
    $api->group(['middleware' => 'public'], function ($api){
        $api->get('sellers/nearby', 'SellerController@getListByNearBy');
        $api->get('sellers/newest', 'SellerController@getListByNewest');
        $api->get('sellers/rating', 'SellerController@getListByRating');
        $api->get('sellers/category/{id}', 'SellerController@getListByCategory');
        $api->get('sellers/{sellerid}', 'SellerController@show');
        $api->get('sellers/{sellerid}/dishes', 'SellerController@getDishesBySeller');
    });
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->resource('sellers', 'SellerController');
        $api->get('sellers/{sellerid}/status', 'SellerController@getStatusBySeller');
        $api->get('/sellers/phone-verify-code/send', 'SellerController@getPhoneVerifyCode');
    });

    // 03 dishes
    $api->group(['middleware' => 'public'], function ($api){
        $api->get('dishes/newest', 'DishController@getListByNewest');
        $api->get('dishes/rating', 'DishController@getListByRating');
        $api->get('dishes/category/{categoryid}', 'DishController@getListByCategory');
        $api->get('dishes/{dishid}', 'DishController@show');
    });
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->resource('dishes', 'DishController');
        $api->delete('/dishes/dishimages/{dishimageid}', 'DishController@destroyImage');
    });

    // 04 shoppingCart
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->get('shoppingcarts/buyer/{buyerid}', 'ShoppingCartController@viewByBuyer');
        $api->get('shoppingcarts/deliver-fee', 'ShoppingCartController@viewDeliverFeeBySeller');
        $api->get('shoppingcarts/tax-rate', 'ShoppingCartController@viewTaxByUser');
        $api->get('shoppingcarts/item-count', 'ShoppingCartController@viewItemCount');
        $api->resource('shoppingcarts', 'ShoppingCartController');
        $api->delete('shoppingcarts/buyer/{buyerid}', 'ShoppingCartController@destroyByBuyer');
        $api->delete('shoppingcarts/buyer/{buyerid}/seller/{sellerid}', 'ShoppingCartController@destroyByBuyerSeller');
        $api->resource('shoppingcartextras', 'ShoppingCartExtraController');
        $api->get('shoppingcartextras/{userid}/{sellerid}', 'ShoppingCartExtraController@viewShoppingCartExtraBySeller');
    });

    // 05 Orders
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->get('orders/buyer/{buyerid}', 'OrderController@viewByBuyer');
        $api->get('orders/seller/{sellerid}', 'OrderController@viewBySeller');
        $api->put('orders/accept/{orderid}', 'OrderController@accept');
        $api->put('orders/reject/{orderid}', 'OrderController@reject');
        $api->put('orders/ready/{orderid}', 'OrderController@ready');
        $api->put('orders/complete/{orderid}', 'OrderController@complete');
        $api->resource('orders', 'OrderController');
    });

    // 06 favorites
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->get('favorites/user/{userid}', 'FavoriteController@viewByUser');
        $api->get('favorites/isfavorite/{userid}/{sellerid}', 'FavoriteController@isMyFavorite');
        $api->delete('favorites/user/{userid}/seller/{sellerid}', 'FavoriteController@destroyByUserSeller');
        $api->resource('favorites', 'FavoriteController');
    });

    // 07 wishes
    $api->group(['middleware' => 'public'], function ($api){
        $api->get('wishes', 'WishController@index');
        $api->get('wishes/{wishid}', 'WishController@show');
    });
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->get('wishes/buyer/{buyerid}', 'WishController@getWishesByUser');
        $api->get('wishes/seller/{sellerid}', 'WishController@getWishesBySeller');
        $api->get('wishes/{wishid}/bids', 'WishController@getBidsByWishid');
        $api->resource('wishes', 'WishController', ['except' => ['index', 'show']]);
    });

    // 08 bids
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->put('bids/{bidid}/assign', 'BidController@assignBidStatus');
        $api->resource('bids', 'BidController');
    });

    // 09 deliverSettings
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->resource('deliverSettings', 'DeliverSettingController');
        $api->get('deliverSettings/seller/{sellerid}', 'DeliverSettingController@viewBySeller');
    });

    // 10 reviews
    $api->group(['middleware' => 'public'], function ($api){
        $api->get('reviews/dish/{dishid}', 'ReviewController@viewreviewsbyDishId');
        $api->get('reviews/seller/{sellerid}', 'ReviewController@viewreviewsbySellerId');
        $api->get('reviews/{reviewid}', 'ReviewController@show');
    });
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->resource('reviews', 'ReviewController');
        $api->post('reviews/order/{orderid}', 'ReviewController@storeReviewsByOrder');
    });

    // 11 order supports
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->get('ordersupports/solutions', 'OrderSupportController@viewSolutions');
        $api->get('ordersupports/order/{orderid}', 'OrderSupportController@viewOrderSuportsByOrder');
        $api->post('ordersupports/follow-up', 'OrderSupportController@storeFollowup');
        $api->resource('ordersupports', 'OrderSupportController');
    });

    // 12 problems
    $api->get('problems/parentcode/{parent_code}', 'ProblemCodeController@viewProblems');
    $api->get('problems/{problemid}', 'ProblemCodeController@show');

    // 13 categories
    $api->resource('categories', 'CategoryController');

    // 14 pickupMethods
    $api->group(['middleware' => 'public'], function ($api){
        $api->get('pickupMethods/menu/{sellerid}', 'PickupMethodController@viewMenu');
    });
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->get('pickupMethods/seller/{sellerid}', 'PickupMethodController@viewBySeller');
        $api->resource('pickupMethods', 'PickupMethodController');
    });

    // 15 pickupLocations
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->get('pickupLocations/seller/{sellerid}', 'PickupLocationController@viewBySeller');
        $api->resource('pickupLocations', 'PickupLocationController');
    });

    // 16 searches
    $api->group(['middleware' => 'public'], function ($api){
        $api->get('/searches/dishes', 'SearchController@getDishListByKeyword');
        $api->get('/searches/sellers', 'SearchController@getSellerListByKeyword');
    });

    // 17 payments
    $api->group(['middleware' => 'auth:api'], function ($api){
        $api->post('/payments/cards', 'PaymentController@storeCard');
        $api->get('/payments/cards', 'PaymentController@getCards');
        $api->put('/payments/cards/{cardid}/default', 'PaymentController@setDefaultCard');
        $api->delete('/payments/cards/{cardid}', 'PaymentController@deleteCard');
        $api->post('/payments/accounts', 'PaymentController@storeAccount');
        $api->get('/payments/accounts', 'PaymentController@viewAccount');
        $api->put('/payments/accounts/bank', 'PaymentController@updateBank');
        $api->put('/payments/accounts/identity', 'PaymentController@updateIdentity');
        $api->put('/payments/accounts/identity/advance', 'PaymentController@updateIdentityAdvance');
        $api->post('/payments/pay', 'PaymentController@processPurchase');
        $api->get('/payments/transfers', 'PaymentController@getTransfers');
    });



    // 98 Images
    $api->group(['middleware' => 'public'], function ($api){
        $api->get('/images/private/order_supports/{supportid}/{filename}', 'ImageController@viewOrderSupportImage');
    });

    // 99 test
    $api->group(['middleware' => 'public'], function ($api){
        $api->post('getLocationByRadius', 'SwaggerController@getLocationByRadius');
        $api->post('CreateLocationByGPid', 'SwaggerController@CreateLocationByGPid');
        $api->put('UpdateLocationByGPid', 'SwaggerController@UpdateLocationByGPid');
        $api->put('DeleteLocationByGPid', 'SwaggerController@DeleteLocationByGPid');
        $api->get('testenv', 'SwaggerController@testEnv');
        $api->get('testdist', 'SwaggerController@testDist');
        $api->post('test/image', 'SwaggerController@testImage');
        $api->get('test/time-offset', 'SwaggerController@testTimeOffset');
        $api->post('test/fcm', 'SwaggerController@testFCM');
    });

    // Webhook routes
    $api->post('/webhook/stripe/account/update', 'Webhooks\StripeWebhookController@handleAccountUpdated');
    $api->post('/webhook/stripe/external-account/update', 'Webhooks\StripeWebhookController@handleAccountExternalAccountUpdated');
});


app('api.exception')->register(function (Exception $exception) {
    $request = Illuminate\Http\Request::capture();
    return app('App\Exceptions\Handler')->render($request, $exception);
});
