<?php

namespace App\Http\Controllers\Webhooks;

use Stripe;
use App\SellerPaymentAccount;
use App\Service\PaymentService;
use App\Notifications\SellerPaymentAccountUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller{

    private static $stripe;
    private static $endpoint_secret_update_acct;
    private static $endpoint_secret_update_extacct;

    private static function initialize() {
        if (self::$stripe != null && self::$endpoint_secret_update_acct != null && self::$endpoint_secret_update_extacct != null)
            return;
        self::$stripe = Stripe::make(env('STRIPE_SECRET_KEY'));
        self::$endpoint_secret_update_acct = env('STRIPE_ENDPOINT_SECRET_UPDATE_ACCOUNT');
        self::$endpoint_secret_update_extacct = env('STRIPE_ENDPOINT_SECRET_UPDATE_EXTACCOUNT');
    }

    /**
     * Handles a account.updated event
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleAccountUpdated(Request $request) {
        self::initialize();
        $payload = file_get_contents("php://input");
        $sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];
        $event = null;
            
        try {
            $event = \Stripe\Webhook::constructEvent(
                            $payload, $sig_header, self::$endpoint_secret_update_acct
            );
            
            $accountId = $event->data->object->id;
            return $accountId;
            
            $event_json = json_decode($payload);
            $accountId = $event_json->{'data'}->{'object'}->{'id'};
            
            $oldPaymentAcct = SellerPaymentAccount::where('account_id', $accountId)->first();
            if(!$oldPaymentAcct){ return; }

            $seller = $oldPaymentAcct->seller;
            $newPaymentAcct = PaymentService::viewAccount($seller);
            
            // If the identity status has any problem, notify the seller
            if ($oldPaymentAcct->identity_status != $newPaymentAcct->identity_status && $newPaymentAcct->identity_status != 'verified') {
                $seller->user->notify(new SellerPaymentAccountUpdated($newPaymentAcct));
            }

            http_response_code(200);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400); // PHP 5.4 or greater
            exit();
        } catch (\Stripe\Error\SignatureVerification $e) {
            // Invalid signature
            http_response_code(400); // PHP 5.4 or greater
            exit();
        }
    }

    /**
     * Handles a account.external_account.updated event
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleAccountExternalAccountUpdated(Request $request) {
        self::initialize();
        $payload = file_get_contents("php://input");
        $sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];
        $event = null;
            
        try {
            $event = \Stripe\Webhook::constructEvent(
                            $payload, $sig_header, self::$endpoint_secret_update_extacct
            );
            
            $accountId = $event->data->object->account;            
            $oldPaymentAcct = SellerPaymentAccount::where('account_id', $accountId)->first();
            if(!$oldPaymentAcct){ return; }
            
            $seller = $oldPaymentAcct->seller;
            $newPaymentAcct = PaymentService::viewAccount($seller);
            
            // If the identity status has any problem, notify the seller
            if ($oldPaymentAcct->identity_status != $newPaymentAcct->identity_status && $newPaymentAcct->identity_status != 'verified') {
                $seller->user->notify(new SellerPaymentAccountUpdated($newPaymentAcct));
            }
            
            http_response_code(200);
            
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400); // PHP 5.4 or greater
            exit();
        } catch (\Stripe\Error\SignatureVerification $e) {
            // Invalid signature
            http_response_code(400); // PHP 5.4 or greater
            exit();
        }
    }

}
