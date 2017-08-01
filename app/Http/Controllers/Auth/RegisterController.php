<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Service\UserAccountService;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmationEmail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
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
        $this->middleware('guest');
        parent::__construct();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'provider' => 'password'
        ]);
        
        UserAccountService::storeDefaultImage($user);
        return $user;
    }
  
    
    /**
     * @SWG\Post(path="/register",
     *   tags={"01 Users"},
     *   summary="Register a new user",
     *   description="Register a new user",
     *   operationId="register",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"multipart/form-data", "application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="name", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="email", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="password", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="password_confirmation", in="formData", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    // Register user for apps
    public function registerForApp(Request $request){
        return $this->handleRegister($request); 
    }
    
    // Register user for website
    public function register(Request $request)
    {
        $this->handleRegister($request);
        return back()->with('info', 'Please confirm your email address.');
    }
    
    private function handleRegister(Request $request){
        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));
        Mail::to($user->email)->send(new ConfirmationEmail($user));
        return $user;
    }
    
    /**
     * Confirm a user's email address.
     *
     * @param  string $token
     * @return mixed
     */    
    public function confirmEmail($verify_token)
    {
        try{        
            User::whereVerifyToken($verify_token)->firstOrFail()->confirmEmail();
        }catch(ModelNotFoundException $e){
            throw new NotAcceptableHttpException('Authentication Fail. The request path may be used or incorrect.');
        }
        
        return redirect('confirm-success');
        //return redirect('login')->with('success', 'You are now confirmed. Please login.');  //$this->agent->isMobile()? redirect('home') : 
    }
}
