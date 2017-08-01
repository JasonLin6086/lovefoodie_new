<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SellerPaymentAccount;
use App\User;
use App\Seller;
use App\Order;
use App\OrderDetail;
use App\ShoppingCart;
use App\ShoppingCartExtra;
use DateTime;
use App\Service\PaymentService;
use App\Service\LocationService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaymentController extends Controller {

    /**
     * @SWG\Post(path="/payments/cards",
     *   tags={"17 Payments"},
     *   summary="Add New Payment Card",
     *   description="Create a new Stripe Customer, save Stripe customer_id into database",
     *   operationId="storeCard",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"multipart/form-data", "application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="stripeToken", in="formData", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */
    public function storeCard(Request $request) {
        return PaymentService::storeCard($request->user(), $request->stripeToken);
    }

    /**
     * @SWG\Get(path="/payments/cards",
     *   tags={"17 Payments"},
     *   summary="Get Card List",
     *   description="Get Card List from Stripe, *delete the same card ",
     *   operationId="getCards",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"multipart/form-data", "application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * 
     * )
     */
    //get Card List by hidden input user_id, return card list which is represent as card last four digits
    public function getCards(Request $request) {
        $cards = PaymentService::getCards($request->user());
        if(!$cards && $this->agent->isMobile()){ return response()->json(['sources' => null]); }
        return $cards;
    }
    
    /**
     * @SWG\Put(path="/payments/cards/{cardid}/default",
     *   tags={"17 Payments"},
     *   summary="Set Default Card",
     *   description="Set Default Card by using card_id",
     *   operationId="setDefaultCard",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     *
     *   @SWG\Parameter(name="cardid", in="path", required=true, type="string"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */
    public function setDefaultCard(Request $request, $id) {
        return PaymentService::setDefaultCard($request->user(), $id);
    }

    /**
     * @SWG\Delete(path="/payments/cards/{cardid}",
     *   tags={"17 Payments"},
     *   summary="Delete Card By ID",
     *   description="Delete the card By cardID ",
     *   operationId="deleteCard",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"multipart/form-data", "application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="cardid", in="path", required=true, type="string"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * 
     * )
     */
    public function deleteCard(Request $request, $id) {
        return PaymentService::deleteCard($request->user(), $id);
    }

    /**
     * @SWG\Post(path="/payments/pay",
     *   tags={"17 Payments"},
     *   summary="Process the Purchase",
     *   description="Process the Purchase by using user_id",
     *   operationId="processPurchase",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=400, description="nothing to pay or seller payment account does not exist"),
     *   @SWG\Response(response=422, description="card error"),
     * )
     */
    public function processPurchase(Request $request) {
        $this->verifyPurchase($request->user());
        
        $charge = PaymentService::processPurchase($request->user());
        $this->storeOrders($request->user(), $charge['id']);
        return $charge;
    }
    
    
    private static function verifyPurchase(User $user){
        $shoppingCartSellers = ShoppingCart::where('user_id', $user->id)->select('seller_id')->distinct('seller_id')->get();
        foreach($shoppingCartSellers as $seller){
            // Check if all shoppingCart sellers has a shoppingCartExtra setting
            $extra = ShoppingCartExtra::where([['seller_id', $seller->seller_id],['user_id', $user->id]])->count();
            if ($extra == 0) {
                $sellerName = Seller::find($seller->seller_id)->kitchen_name;
                throw new BadRequestHttpException('ShoppingCartExtra data is empty ('.$sellerName.') !');
            }
            
            // Ccheck if all seller's status is good and has seller account
            $sellerAcc = SellerPaymentAccount::where('seller_id', $seller->seller_id)->first();
            if (!$sellerAcc) {
                throw new BadRequestHttpException('Seller payment account does not exist');
            }
        }
        return true;
    }    

    /**
     * @SWG\Post(path="/payments/accounts",
     *   tags={"17 Payments"},
     *   summary="Create payment account for this seller",
     *   description="Create payment account for this seller. <br> 
                      Test account_number: 000123456789, test routing_number: 110000000 <br>
                      A debit card token is also acceptable",
     *   operationId="storeAccount",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="bank_account_token", in="formData", required=true, type="string", description="create bank account token from stripe"),
     *   @SWG\Parameter(name="first_name", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="last_name", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="date_of_birth", in="formData", required=true, type="string", description="mm/dd/yyyy"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=404, description="account already exist"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function storeAccount(Request $request) {
        // Check if the seller already has stripe account. If yes, return the account.
        $seller = $request->user()->seller;
        
        $acct_id = PaymentService::storeAccount($seller, 
            $request->bank_account_token,
            $request->first_name, $request->last_name, 
            $request->date_of_birth);
            
        // Store the seller's stripe acount info
        $paymentAcct = SellerPaymentAccount::create([
                'seller_id' => $seller->id,
                'account_id' => $acct_id,
        ]);
        return $paymentAcct;
    }
    
    /**
     * @SWG\Get(path="/payments/accounts",
     *   tags={"17 Payments"},
     *   summary="Get Account Detail",
     *   description="Get account detail from Stripe",
     *   operationId="viewAccount",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"multipart/form-data", "application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=404, description="account does not exist"),
     * )
     */
    public function viewAccount(Request $request) {
        $account = PaymentService::viewAccount($request->user()->seller);
        return response()->json($account);
        //return $account;
    }
    
    /**
     * @SWG\Put(path="/payments/accounts/bank",
     *   tags={"17 Payments"},
     *   summary="Update bank info in payment account.",
     *   description="Update bank info in payment account. <br> 
                      Test account_number: 000123456789, test routing_number: 110000000 <br>
                      A debit card token is also acceptable",
     *   operationId="updateBank",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="bank_account_token", in="formData", required=true, type="string", description="create bank account token from stripe"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=400, description="Payment account does not exist"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function updateBank(Request $request) {
        $seller = $request->user()->seller;
        
        // If the seller doesn't have payment account yet. Return error.
        $paymentAcct = $seller->sellerPaymentAccount;
        if(!$paymentAcct){ throw new BadRequestHttpException('Seller payment account does not exist'); }
        
        PaymentService::updateBank($paymentAcct->account_id, 
                $request->bank_account_token);
        
        // Query the most updated data
        return SellerPaymentAccount::where('seller_id', $seller->id)->first();
    }
    
    /**
     * @SWG\Put(path="/payments/accounts/identity",
     *   tags={"17 Payments"},
     *   summary="Update identity info in payment account stage1(name, birthday)",
     *   description="Update identity info in payment account stage1(name, birthday)",
     *   operationId="updateIdentity",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="first_name", in="query", required=true, type="string"),
     *   @SWG\Parameter(name="last_name", in="query", required=true, type="string"),
     *   @SWG\Parameter(name="date_of_birth", in="query", required=true, type="string", description="mm/dd/yyyy"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=400, description="Payment account does not exist"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function updateIdentity(Request $request) {
        $seller = $request->user()->seller;
        
        // If the seller doesn't have payment account yet. Return error.
        $paymentAcct = $seller->sellerPaymentAccount;
        if(!$paymentAcct){ throw new BadRequestHttpException('Seller payment account does not exist'); }        
        
        $acct = PaymentService::updateIdentity($seller, $paymentAcct->account_id, 
                $request->first_name, $request->last_name, $request->date_of_birth);
 
        // Query the most updated data
        return SellerPaymentAccount::where('seller_id', $seller->id)->first();
    } 
    
    /**
     * @SWG\Put(path="/payments/accounts/identity/advance",
     *   tags={"17 Payments"},
     *   summary="Update identity info in payment account",
     *   description="Update identity info in payment account for stage2(ssn_last_4), <br>
                      stage3(personal_id_number), <br> 
                      and stage4(identity_document, such as drivers license or passport",
     *   operationId="updateIdentityAdv",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="update_field", in="query", required=true, type="string", enum={"ssn_last_4", "personal_id_number", "identity_document"}),
     *   @SWG\Parameter(name="update_value", in="query", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=400, description="Payment account does not exist"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function updateIdentityAdvance(Request $request) {
        $seller = $request->user()->seller;
        
        // If the seller doesn't have payment account yet. Return error.
        $paymentAcct = $seller->sellerPaymentAccount;
        if(!$paymentAcct){ throw new BadRequestHttpException('Seller payment account does not exist'); }   
        
        $acct = PaymentService::updateIdentityAdvance($paymentAcct->account_id, $request->update_field, $request->update_value);
 
        // Query the most updated data
        return SellerPaymentAccount::where('seller_id', $seller->id)->first();
    } 
    
    /**
     * @SWG\Get(path="/payments/transfers",
     *   tags={"17 Payments"},
     *   summary="Get transfer list",
     *   description="Get transfer list from Stripe",
     *   operationId="getTransfers",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"multipart/form-data", "application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * 
     * )
     */
    public function getTransfers(Request $request) {
        return PaymentService::getTransfers($request->user()->seller);
    }
    
    // Create order record in the database
    private function storeOrders(User $user, $chargeid) {
        //1. add to order and orderdetail 
        $user_id = $user->id;
        $sellers = ShoppingCart::where('user_id', $user_id)->groupBy('seller_id')->select('seller_id')->get();

        foreach ($sellers as $seller) {
            $shoppingCartExtra = ShoppingCartExtra::where('user_id', $user_id)
                    ->where('seller_id', $seller->seller_id)
                    ->first();

            $order = Order::create([
                        "user_id" => $user_id,
                        "seller_id" => $seller->seller_id,
                        "type" => "REGULAR",
                        "total" => $shoppingCartExtra->total,
                        "transfer_amount" => $shoppingCartExtra->transfer_amount,
                        "app_fee" => $shoppingCartExtra->app_fee,
                        "deliver_fee" => $shoppingCartExtra->deliver_fee,
                        "pickup_time" => DateTime::createFromFormat('m/d/Y H:i', $shoppingCartExtra->pickup_time)->format('Y-m-d H:i:s'),
                        "pickup_type" => $shoppingCartExtra->pickup_type,
                        "pickup_location_desc" => $shoppingCartExtra->pickup_location_desc,
                        "address" => $shoppingCartExtra->address,
                        "google_place_id" => $shoppingCartExtra->google_place_id,
                        "tax" => $shoppingCartExtra->tax,
                        "description" => $shoppingCartExtra->description,
                        "total_price" => $shoppingCartExtra->total_price,
                        "charge_id" => $chargeid
            ]);
            
            // store order location
            LocationService::CreateLocationByGP_id($shoppingCartExtra->google_place_id, 'orders', $order->id);
            
            $shoppingCarts = ShoppingCart::where('user_id', $user_id)
                    ->where('seller_id', $seller->seller_id)
                    ->get();
            foreach ($shoppingCarts as $shoppingCart) {
                OrderDetail::create([
                    "order_id" => $order->id,
                    "dish_id" => $shoppingCart->dish_id,
                    "dish_name" => $shoppingCart->dish_name,
                    "quantity" => $shoppingCart->quantity,
                    "unit_price" => $shoppingCart->unit_price,
                    "total_price" => $shoppingCart->total_price,
                ]);
            }
        }
        //2. delete the staff in the shopping cart and shopping extra
        ShoppingCart::where("user_id", $user_id)->delete();
        ShoppingCartExtra::where("user_id", $user_id)->delete();
        
    }
    
}
