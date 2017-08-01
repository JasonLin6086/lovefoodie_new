<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use App\FcmRegistrationToken;
use Psr\Http\Message\ServerRequestInterface;
use Auth;
use Hash;
use Illuminate\Auth\AuthenticationException;
use App\Service\UserAccountService;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

        /**
     * Override the function in AuthenticatesUsers
     * @param \App\Http\Controllers\Auth\Request $request
     * @return type
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password') + ['verified' => true];
    }
    
    /**
     * @SWG\Post(path="/loginWithPassword",
     *   tags={"01 Users"},
     *   summary="App login with username/password",
     *   description="App login with username/password",
     *   operationId="loginWithPassword",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"multipart/form-data", "application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="grant_type", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="client_id", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="client_secret", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="username", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="password", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="scope", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="utc_offset", in="formData", required=false, type="integer", description="time diff to UTC. In minutes"),
     *   @SWG\Parameter(name="fcm_registration_token", in="formData", required=false, type="string", description="registration token from FCM"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     * )
     */ 
    public function loginWithPassword(ServerRequestInterface $request)
    {
        $email = $request->getParsedBody()['username'];
        $password = $request->getParsedBody()['password']; 
        
        if (Auth::attempt(['email' => $email, 'password' => $password, 'verified' => true]))
        {
            $token = \App::call('\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
            
            $body = $request->getParsedBody();
            if(isset($body['utc_offset']) && isset($body['fcm_registration_token'])){
                $utc_offset = $request->getParsedBody()['utc_offset'];
                $fcm_registration_token = $request->getParsedBody()['fcm_registration_token'];
                UserAccountService::updateLoginInfo(Auth::user(), $utc_offset, $fcm_registration_token);
            }
            return $token;
        }else{
            // (1) Check if the email exists
            $user = User::where([['provider', 'password'],['email', $email]])->first();
            if(!$user){
                throw new AuthenticationException('Email does not exist');
            }
            
            // (2) Check if the password is matched
            if(!Hash::check($password, $user->password)){
                throw new AuthenticationException('Password does not match');
            }
            
            // (3) Check if email is verified
            if(!$user->verified){
                throw new AuthenticationException('Email does not verified. Please find the confirmation email from your email box and click on the confirm URL.');
            }
            
            // Some other unknown problem (??)
            throw new AuthenticationException('Client authentication failed');
        }
    }

    /**
     * @SWG\Post(path="/logout",
     *   tags={"01 Users"},
     *   summary="logout the user from App",
     *   description="logout the user from App",
     *   operationId="logoutApp",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="fcm_registration_token", in="formData", required=true, type="string", description="registration token from FCM"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     * )
     */ 
    public function logoutApp(Request $request)
    {
        // (1) Check if the fcm token is belonged to this user
        // (2) Delete fcm token from database
        $tokenObj = FcmRegistrationToken::where([['user_id', $request->user()->id],['token', $request->fcm_registration_token]])->first();
        if($tokenObj){
            FcmRegistrationToken::where([['user_id', $request->user()->id],['token', $request->fcm_registration_token]])->delete();
        }
    }
}
