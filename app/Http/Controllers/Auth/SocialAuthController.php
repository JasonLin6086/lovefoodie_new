<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Service\UserAccountService;
use Socialite;
use App\User;
use Laravel\Socialite\Two\User as ProviderUser;
use Exception;

class SocialAuthController extends Controller
{
    public function login($provider = 'google')
    {
        return Socialite::driver($provider)->scopes(['email'])->redirect();
    }
    
    public function callback($provider = 'google')
    {
        $providerUser = Socialite::driver($provider)->user();
        $user = UserAccountService::createOrGetUser($providerUser, $provider);
        auth()->login($user);
        
        return redirect('home');
    }

    /**
     * @SWG\Post(path="/loginWithToken",
     *   tags={"01 Users"},
     *   summary="Login a user with third party token",
     *   description="Login a user with third party (facebook, google+...) token, for app use only",
     *   operationId="loginWithToken",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"multipart/form-data", "application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="provider", in="formData", required=true, type="string", enum={"facebook","google","weixin"}),
     *   @SWG\Parameter(name="token", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="openid", in="formData", required=false, type="string", description="required when provider=weixin"),
     *   @SWG\Parameter(name="utc_offset", in="formData", required=false, type="integer", description="time diff to UTC. In minutes"),
     *   @SWG\Parameter(name="fcm_registration_token", in="formData", required=false, type="string", description="registration token from FCM"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="token is invalid"),
     * )
     */    
    public function loginWithToken(Request $request)
    {        
        $providerUser = null;
        switch($request->provider){
            case 'google': $providerUser = $this->getGoogleUser($request->token); break;
            case 'facebook': $providerUser = $this->getSocialUser($request->provider, $request->token); break;
            case 'weixin': $providerUser = $this->getWeixinUser($request->provider, $request->token, $request->openid); break;
        }
        //return ver_dump($providerUser);
        if(!$providerUser){ throw new \Illuminate\Auth\AuthenticationException(); }
        
        $user = UserAccountService::createOrGetUser($providerUser, $request->provider);
        
        if(isset($request['utc_offset']) && isset($request['fcm_registration_token'])){
            UserAccountService::updateLoginInfo($user, $request->utc_offset, $request->fcm_registration_token);
        }
        return $user->createToken($user->name);
    }
    
    private function getSocialUser($provider, $token){
        return Socialite::driver($provider)->userFromToken($token);
    }
    
    private function getWeixinUser($providerTag, $token, $openid){
        if(!$openid){ return null; }
        $provider = Socialite::driver($providerTag);
        $provider->setOpenId($openid);
        return $provider->userFromToken($token);
    }
    
    private function getGoogleUser($id_token){
        $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            
            // avoid the condition that the google token doesn't contains "name" and "picture" variables
            $name = "";
            try{
                $name = $payload['name'];
            }catch(Exception $e){
                $name = $payload['email'];
            }

            $picture = "";            
            try{
                $picture = $payload['picture'];
            }catch(Exception $e){
                $picture = asset("image/avatar1.jpg");
            }
            
            return (new ProviderUser)->setRaw($payload)->map([
                'id' => $payload['sub'], 
                'nickname' => $name, 
                'name' => $name, 
                'email' => $payload['email'], 
                'avatar' => $picture,
                'avatar_original' => preg_replace('/\?sz=([0-9]+)/', '', $picture),
            ]);
        } else {
            return null;
        }
    }
}