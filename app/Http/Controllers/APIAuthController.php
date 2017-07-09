<?php

namespace App\Http\Controllers;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Auth\RegistersUsers;
//use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;

class APIAuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | APIAuthController Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users for apps as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    
    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
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
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
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
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=400, description="Bad request"),
     * )
     */
    public function register(Request $request)
    { 
        $this->validator($request->all())->validate();    

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);
        
        return $user->createToken($user->name);
    }
    
    
     /**
     * @SWG\Post(path="/loginWithPassword",
     *   tags={"01 Users"},
     *   summary="App login with username/password",
     *   description="App login with username/password",
     *   operationId="issueToken",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"multipart/form-data", "application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="grant_type", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="client_id", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="client_secret", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="username", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="password", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="scope", in="formData", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     * )
     */ 
}
