<?php 
namespace App\Service;
use Stripe;
use Stripe_Charge;
use Stripe_InvalidRequestError;
use Stripe_CardError;
use Cartalyst\Stripe\Api\Customers;

class StripeService {

    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_TEST_USER_SECRET_KEY'));
    }
    
    public function CreateCustomer($token){
        $stripe = Stripe::make(env('STRIPE_TEST_USER_SECRET_KEY'));
        // Create a new Stripe customer
        try {
            $customer = Customers::create([
                "source" => $token,
            ]);
        } catch (Cartalyst\Stripe\Exception\NotFoundException $e) {
            
        }
        return $customer->id;
    }
    
    public static function makePurchase($user, $request, $item){

        try {
                Stripe_Charge::create([
                  'amount' => $item->amount,
                  'currency' => 'usd',
                  'customer' => $user->stripe_id,
                  'source' => $item['stripe-token'],
                  'description' => $item->title,
                  'application_fee' => 100, // amount in cents to go to App owner. You'd probably want to set this up as it's own function to calculate
                  'destination' => $item->user->stripe_id // the remainder going to the author's account
                ]);
        }catch (Stripe_InvalidRequestError $e){

                // invalid request
        }catch (Stripe_CardError $e) {

                // card was declined
        }
    }
}

