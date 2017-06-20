<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Seller;
use App\Dish;
use App\Wish;
use Illuminate\Support\Facades\Response;
use View;
use Auth;
use Storage;

class WebController extends Controller
{
    public function __construct() {
        //View::share('categories', \App\Category::all());
    }

    /*
    * Public
    */
    public function viewSellerDetail($id){
        $seller = Seller::findOrFail($id);
        return view('public.seller-detail', compact('seller'));
    }

    public function viewDishDetail($id){
        $dish = Dish::findOrFail($id);
        return view('public.dish-detail', compact('dish'));
    }

    // do keyword search
    public function search(Request $request){
        $keyword = $request->keyword;
        $type = $request->type;
        $longitude = $request->longitude;
        $latitude = $request->latitude;
        $view = $request->view? $request->view : 'public.search';

        if($request->type == "seller"){
            $sellers = \App::call('App\Http\Controllers\SearchController@getSellerListByKeyword');
            return view($view, compact('sellers','keyword','type','longitude','latitude'));
        }else{
            $dishes = \App::call('App\Http\Controllers\SearchController@getDishListByKeyword');
            return view($view, compact('dishes','keyword','type','longitude','latitude'));
        }
    }

    public function viewDishRating(Request $request){
        $dishes = \App::call('App\Http\Controllers\DishController@getListByRating');
        return view($request->view, compact('dishes'));
    }

    public function viewDishNewest(Request $request){
        $dishes = \App::call('App\Http\Controllers\DishController@getListByNewest');
        return view($request->view, compact('dishes'));
    }

    public function viewSellerRating(Request $request){
        $sellers = \App::call('App\Http\Controllers\SellerController@getListByRating');
        return view($request->view, compact('sellers'));
    }

    public function viewSellerNearby(Request $request){
        $sellers = \App::call('App\Http\Controllers\SellerController@getListByNearBy');
        return view($request->view, compact('sellers'));
    }

    public function viewWishes(Request $request){
        $wishes = \App::call('App\Http\Controllers\WishController@index');
        return view($request->view, compact('wishes'));
    }

    public function viewAbout(){
        return view('public.about');
    }

    public function viewFaq(){
        return view('public.faq');
    }

    public function viewContacts(){
        return $this->agent->isMobile()? view('mobile.contacts') : view('public.contacts');
    }

    public function viewDeliveryCancellation(){
        return view('public.delivery-cancellation');
    }

    public function viewTermsOfUse(){
        return view('public.terms-of-use');
    }

    public function viewNewsletter() {
        \App::call('App\Http\Controllers\SubscribeController@store');
        return redirect('/');
    }

    public function viewConfirmSuccess(){
        return view('public.confirm-success');
    }


    // Pages for mobile
    public function viewMobileAbout(){
        return view('mobile.about');
    }

    public function viewMobilePrivacy(){
        return view('mobile.privacy');
    }

    public function viewMobileTermOfService(){
        return view('mobile.term-of-service');
    }

    /**
     * Seller Management
     */

    // open add a new seller page
    public function sellerProfileAdd(Request $request){
        return view('seller.profile');
    }

    // open seller profile page with seller data filled
    public function sellerProfile(Request $request){
        $seller = $request->user()->seller;
        return view('seller.profile', compact('seller'));
    }

    // get POST data and modify seller data
    public function sellerProfileModify(Request $request){
        $seller = (!$request->id)?
                \App::call('App\Http\Controllers\SellerController@store', [$request]):
                \App::call('App\Http\Controllers\SellerController@update', [$request->id]);
        return view('seller.profile', compact('seller'));
    }

    // open seller dishlist page
    public function sellerDishList(Request $request){
        //$dishes = \App::call('App\Http\Controllers\SellerController@getDishesBySeller', [$request->user()->seller->id]);
        return view('seller.dish-list', compact('dishes'));
    }

    // open an empty new dish page
    public function sellerDishAdd(){
        return view('seller.dish-modify');
    }

    // get dish data from dishid and open dish modify page
    public function sellerDishData($id){
        $dish = Dish::findOrFail($id);
        return view('seller.dish-modify', compact('dish'));
    }

    // get POST data and modify dish
    public function sellerDishModify(Request $request){
        $dish = (!$request->id)?
                \App::call('App\Http\Controllers\DishController@store', [$request]):
                \App::call('App\Http\Controllers\DishController@update', [$request->id]);
        return view('seller.dish-modify', compact('dish'));
    }

    // Delete one dish image
    public function sellerDishImageDelete(Request $request){
        \App::call('App\Http\Controllers\DishController@destroyImage');
        return "{}";
    }

    public function sellerDishImageData(Request $request, $id){
        if($id==-1){ return view('seller.dish-modify-data'); }
        $dish = Dish::findOrFail($id);
        return view('seller.dish-modify-data', compact('dish'));
    }

    // get all orders for this seller
    public function sellerOrderList(Request $request){
        $orders = \App::call('App\Http\Controllers\OrderController@viewBySeller', [$request->user()->seller->id]);
        return view('seller.order-list', compact('orders'));
    }

    // get a order by id
    public function sellerOrderById(Request $request, $id){
        $order = \App::call('App\Http\Controllers\OrderController@show', [$id]);
        return view($request->view, compact('order'));
    }

    // get filtered orders for this seller
    public function sellerOrderListFiltered(Request $request){
        $orders = \App::call('App\Http\Controllers\OrderController@viewBySeller', [$request->user()->seller->id]);
        return view('seller.order-list-data', compact('orders'));
    }

    // seller accept an order
    public function sellerOrderAccept(Request $request, $id){
        \App::call('App\Http\Controllers\OrderController@accept', [$id]);
        $orders = \App::call('App\Http\Controllers\OrderController@viewBySeller', [$request->user()->seller->id]);
        return view($request->view, compact('orders'));
    }

    // seller reject an order
    public function sellerOrderReject(Request $request, $id){
        \App::call('App\Http\Controllers\OrderController@reject', [$id]);
        $orders = \App::call('App\Http\Controllers\OrderController@viewBySeller', [$request->user()->seller->id]);
        return view($request->view, compact('orders'));
    }

    // seller deliver an order
    public function sellerOrderDeliver(Request $request, $id){
        \App::call('App\Http\Controllers\OrderController@deliver', [$id]);
        $orders = \App::call('App\Http\Controllers\OrderController@viewBySeller', [$request->user()->seller->id]);
        return view($request->view, compact('orders'));
    }

    // seller complete an order
    public function sellerOrderComplete(Request $request, $id){
        \App::call('App\Http\Controllers\OrderController@complete', [$id]);
        $orders = \App::call('App\Http\Controllers\OrderController@viewBySeller', [$request->user()->seller->id]);
        return view($request->view, compact('orders'));
    }

    // open deliver setting view
    public function sellerDeliverSetting(Request $request){
        $seller = $request->user()->seller;
        $deliverSetting = \App::call('App\Http\Controllers\DeliverSettingController@viewBySeller', [$seller->id]);
        return view('seller.deliver-setting', compact('seller', 'deliverSetting'));
    }

    // modify deliver setting data
    public function sellerDeliverSettingModify(Request $request){
        $deliverSetting = !$request->id?
                \App::call('App\Http\Controllers\DeliverSettingController@store'):
                \App::call('App\Http\Controllers\DeliverSettingController@update', [$request->id]);
        return redirect()->route('seller/deliver-setting');
    }

    // modify pickupMethod data
    public function sellerPickupMethodModify(Request $request){
        $pickupmethod = !$request->id?
                \App::call('App\Http\Controllers\PickupMethodController@store'):
                \App::call('App\Http\Controllers\PickupMethodController@update', [$request->id]);
        return redirect()->route('seller/deliver-setting');
    }

    // get pickupmethod data to modify
    public function sellerPickupMethodData(Request $request, $id){
        $seller = $request->user()->seller;
        if($id==-1){ return view($request->view, compact('seller')); }
        $pickupmethod = \App::call('App\Http\Controllers\PickupMethodController@show', [$id]);
        return view($request->view, compact('pickupmethod', 'seller'));
    }

    // delete a pickupmethod
    public function sellerPickupMethodDelete(Request $request, $id){
        \App::call('App\Http\Controllers\PickupMethodController@destroy', [$id]);
        $seller = $request->user()->seller;
        return view($request->view, compact('seller'));
    }

    // get a wish and bid for this seller
    public function sellerBidData(Request $request){
        $wish = \App::call('App\Http\Controllers\WishController@show', [$request->wish_id]);
        $bid = \App::call('App\Http\Controllers\BidController@showBidBySeller', [$request->wish_id]);
        return view($request->view, compact('wish', 'bid'));
    }

    // add or modify a bid
    public function sellerBidModify(Request $request){
        $bid = !$request->id?
                \App::call('App\Http\Controllers\BidController@store'):
                \App::call('App\Http\Controllers\BidController@update', [$request->id]);
        if(!$request->view){ $request->merge(['view'=> 'public.home-data']); }
        return $this->viewWishes($request);
    }

    // get wish list for this seller
    public function sellerWishList(Request $request){
        $wishes = Wish::all();
        return view('seller.wish-list', compact('wishes'));
    }

    // get bank info for this seller
    public function sellerBankInfo(Request $request){
        return view('seller.bank-info');
    }

    // get bank info for this seller
    public function sellerBankAccountAdd(Request $request){
        \App::call('App\Http\Controllers\PaymentController@storeAccount');
        return view($request->view);
    }


    /**
     * Buyer Management
     */
    public function buyerProfile(){
        $user = Auth::user();
        return view('buyer.profile', compact('user'));
    }

    // update user profile
    public function buyerProfileModify(Request $request){
        $user = \App::call('App\Http\Controllers\UserController@update', [$request->id]);
        return view('buyer.profile', compact('user'));
    }

    // add a new location
    public function buyerLocationAdd(Request $request){
        $user = $request->user();
        \App::call('App\Http\Controllers\UserController@addLocation', [$user->id]);
        return view($request->view, compact('user'));
    }

    // remove a location
    public function buyerLocationDelete(Request $request, $id){
        $user = $request->user();
        \App::call('App\Http\Controllers\UserController@deleteLocation', [$user->id, $id]);
        return view($request->view, compact('user'));
    }

    // get order list for this buyer
    public function buyerOrderList(Request $request){
        $orders = $request->user()->order()->get();
        return view('buyer.order-list', compact('orders'));
    }

    // open buyer review view
    public function buyerReviewData(Request $request, $id){
        $order = \App::call('App\Http\Controllers\OrderController@show', [$id]);
        return view('buyer.review-add', compact('order'));
    }

    // add/update review for an order
    public function buyerReviewModify(Request $request, $id){
        $order = \App::call('App\Http\Controllers\ReviewController@storeReviewsByOrder', [$id]);
        $orders = $request->user()->order()->get();
        return view('buyer.order-list', compact('orders'));
        //return view('buyer.review-add', compact('request'));
    }

    // get problems
    public function buyerProblem(Request $request, $id){
        $problems = \App::call('App\Http\Controllers\ProblemCodeController@viewProblemsById', [$id]);

        // If this is the final problem
        if(sizeof($problems)==0){
            $finalproblem = \App::call('App\Http\Controllers\ProblemCodeController@show', [$id]);
            return view($request->view, compact('finalproblem'));
        }
        return view($request->view, compact('problems'));
    }

    // view order-support problems
    public function buyerOrderSupport(Request $request, $id){
        $order = \App::call('App\Http\Controllers\OrderController@show', [$id]);
        $problems = \App::call('App\Http\Controllers\ProblemCodeController@viewProblems');
        return view('buyer.order-support', compact('order', 'problems'));
    }

    // add an order support
    public function buyerOrderSupportAdd(Request $request){
        $order = \App::call('App\Http\Controllers\OrderSupportController@store');
        return view('buyer.order-support', compact('order'));
    }

    // get favorite sellers for a buyer
    public function buyerFavorite(Request $request){
        $sellers = \App::call('App\Http\Controllers\FavoriteController@viewByUser', [$request->user()->id]);
        return view('buyer.favorite', compact('sellers'));
    }

    // add a favorite seller
    public function buyerFavoriteAdd(Request $request){
        \App::call('App\Http\Controllers\FavoriteController@store');
        return view($request->view);
    }

    // delete a favorite seller
    public function buyerFavoriteDelete(Request $request, $id){
        \App::call('App\Http\Controllers\FavoriteController@destroy', [$id]);
        $sellers = \App::call('App\Http\Controllers\FavoriteController@viewByUser', [$request->user()->id]);
        return view('buyer.favorite-data', compact('sellers'));
    }

    // show all wishes for this buyer
    public function buyerWishList(Request $request){
        $wishes = Wish::where('user_id', '=', $request->user()->id)->get();
        return view('buyer.wish-list', compact('wishes'));
    }

    // open add new wish page for this buyer
    public function buyerWishAdd(){
        return view('buyer.wish-add');
    }

    // open wish detail page for this wish
    public function buyerWishDetail(Request $request, $id){
        $wish = Wish::findOrFail($id);
        return view('buyer.wish-detail', compact('wish'));
    }

    // open wish detail page for this wish
    public function buyerWishModify(Request $request){
        $wish = (!$request->id)?
                \App::call('App\Http\Controllers\WishController@store', [$request]):
                \App::call('App\Http\Controllers\WishController@update', [$request->id]);
        return view('buyer.wish-detail', compact('wish'));
    }

    // get all shooppingCart items for this buyer
    public function buyerShoppingCartList(Request $request){
        $shoppingcarts = \App::call('App\Http\Controllers\ShoppingCartController@viewByBuyer_old', [$request->user()->id]);
        $deliverfees = \App::call('App\Http\Controllers\ShoppingCartController@viewDeliverFeeBySeller');
        $taxrate = \App::call('App\Http\Controllers\ShoppingCartController@viewTaxByUser');
        $user = $request->user();
        return view('buyer.shoppingcart', compact('shoppingcarts', 'deliverfees', 'user', 'taxrate'));
    }

    // update a shopping cart entry
    public function buyerShoppingCartUpdate(Request $request, $id){
        \App::call('App\Http\Controllers\ShoppingCartController@update', [$id]);
        $shoppingcarts = \App::call('App\Http\Controllers\ShoppingCartController@viewByBuyer_old', [$request->user()->id]);
        $deliverfees = \App::call('App\Http\Controllers\ShoppingCartController@viewDeliverFeeBySeller');
        $taxrate = \App::call('App\Http\Controllers\ShoppingCartController@viewTaxByUser');
        $user = $request->user();
        return view($request->view, compact('shoppingcarts', 'deliverfees', 'user', 'taxrate'));
    }

    // delete a shopping cart entry
    public function buyerShoppingCartDelete(Request $request, $id){
        \App::call('App\Http\Controllers\ShoppingCartController@destroy', [$id]);
        $shoppingcarts = \App::call('App\Http\Controllers\ShoppingCartController@viewByBuyer_old', [$request->user()->id]);
        $deliverfees = \App::call('App\Http\Controllers\ShoppingCartController@viewDeliverFeeBySeller');
        $taxrate = \App::call('App\Http\Controllers\ShoppingCartController@viewTaxByUser');
        $user = $request->user();
        return view($request->view, compact('shoppingcarts', 'deliverfees', 'user', 'taxrate'));
    }

    // add a shopping cart entry
    public function buyerShoppingCartAdd(Request $request){
        \App::call('App\Http\Controllers\ShoppingCartController@store');
        return view($request->view);
    }

    // open payment method page
    public function buyerPaymentList(Request $request){
        \App::call('App\Http\Controllers\ShoppingCartExtraController@storeShoppingCartExtraWeb');
        $request->merge(['keep_extra_settings'=> true]);
        $shoppingcarts = \App::call('App\Http\Controllers\ShoppingCartController@viewByBuyer', [$request->user()->id]);
        $carts = json_decode($shoppingcarts->getContent());

        $sellers = $carts->sellers;
        $summary = $carts->summary;
        $user = $request->user();
        $cards = \App::call('App\Http\Controllers\PaymentController@getCards');
        return view('buyer.payment-list', compact('sellers', 'user', 'summary', 'cards'));
    }

    // add new credit card for payment
    public function buyerAddCard(Request $request){
        \App::call('App\Http\Controllers\PaymentController@storeCard');
        $cards = \App::call('App\Http\Controllers\PaymentController@getCards');
        return view($request->view? $request->view: 'buyer.payment-card', compact('cards'));
    }

    // set default credit card for payment
    public function buyerSetDefaultCard(Request $request, $id){
        \App::call('App\Http\Controllers\PaymentController@setDefaultCard', [$id]);
        $cards = \App::call('App\Http\Controllers\PaymentController@getCards');
        return view($request->view? $request->view: 'buyer.payment-card', compact('cards'));
    }

    // delete credit card
    public function buyerDeleteCard(Request $request, $id){
        \App::call('App\Http\Controllers\PaymentController@deleteCard', [$id]);
        $cards = \App::call('App\Http\Controllers\PaymentController@getCards');
        return view($request->view? $request->view: 'buyer.payment-card', compact('cards'));
    }

    // This is a tmp route for test only
    /*
    public function testAddCard() {
        return view('pages.order');
    }
    public function testAddCard2(Request $request) {
        //return \App::call('App\Http\Controllers\PaymentController@storeCard');
        return $request->stripeToken;
    }*/

    // Checkout everything in the shoppingCart
    public function buyerPay(Request $request){
        \App::call('App\Http\Controllers\PaymentController@processPurchase');
        return view('buyer.payment-complete');
    }


    /*
     * Helper Routes
     */

    public function helperOrderSupportDetail(Request $request, $id){
        $order = \App::call('App\Http\Controllers\OrderController@show');
        return view('helper.order-support', compact('order'));
    }
}
