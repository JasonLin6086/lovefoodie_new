<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Service\UserAccountService;
use App\Service\LocationService;
use App\Service\PhoneVerifyService;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserLocationCreateRequest;
use App\User;
use App\Location;

class UserController extends Controller
{
    var $table_name = 'users';
    
    /**
     * @SWG\Get(path="/users/me",
     *   tags={"01 Users"},
     *   summary="Get user profile",
     *   description="Get user profile",
     *   operationId="show",
     *   produces={"application/xml", "application/json"},
     *   consumes={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     * )
     */
    public function showMe(Request $request)
    {
        $user = $request->user();
        return User::with('seller')->with('location')->find($user->id);
    }
    
    /**
     * @SWG\Post(path="/users/{userid}",
     *   tags={"01 Users"},
     *   summary="Update user profile",
     *   description="Update user profile",
     *   operationId="updates",
     *   produces={"application/xml", "application/json"},
     *   consumes={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="userid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="_method", in="formData", required=true, type="string", enum={"PUT"}),
     *   @SWG\Parameter(name="image", in="formData", required=false, type="file"),
     *   @SWG\Parameter(name="name", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="email", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="phone_number", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="password", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="address", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="google_place_id", in="formData", required=false, type="string"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        // If google_place_id has changed, update the entry in Locations
        $this->updateUserLocation($request, $user);
        
        // If the phone number is modified, need to verify 
        if($request->phone_number && $user->phone_number != $request->phone_number){
            $this->updatePhoneNumber($request, $id);
        }
        
        $user->update($request->all());

        return UserAccountService::updateImage($user, Input::file('image'));
    }

    /**
     * @SWG\Post(path="/users/{userid}/location",
     *   tags={"01 Users"},
     *   summary="Add a user location",
     *   description="Add a user location",
     *   operationId="addLocation",
     *   produces={"application/xml", "application/json"},
     *   consumes={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="userid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="address", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="google_place_id", in="formData", required=true, type="string"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */    
    public function addLocation(UserLocationCreateRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('addLocation', $user);
        
        $locations = $user->location()->get();
        LocationService::CreateLocationByGP_id($request->google_place_id, $this->table_name, $user->id);    
        foreach($locations as $loc){
            $loc->update(['isdefault' => 0]);
        }
        
        $user->update(['address' => $request->address, 'google_place_id' => $request->google_place_id]);
        return $user;
    }
    
    /**
     * @SWG\Delete(path="/users/{userid}/location/{locationid}",
     *   tags={"01 Users"},
     *   summary="Delete a user location",
     *   description="Delete a user location",
     *   operationId="deleteLocation",
     *   produces={"application/xml", "application/json"},
     *   consumes={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="userid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="locationid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */    
    public function deleteLocation(Request $request, $userid, $locationid)
    {
        $user = User::findOrFail($userid);
        $location = Location::findOrFail($locationid);
        $this->authorize('deleteLocation', [$user, $location, $user->location()->count()]);
        
        Location::destroy($location->id);
        
        $locations = $user->location()->orderby('updated_at', 'DESC')->get();
        foreach($locations as $idx => $loc){
            $loc->update(['isdefault' => ($idx==0? 1:0)]);
        }
        
        $user->update(['address' => $locations[0]->address, 'google_place_id' => $locations[0]->google_place_id]);
        return $user;
    }
    
    
    /**
     * @SWG\Put(path="/users/{userid}/location/{locationid}/default",
     *   tags={"01 Users"},
     *   summary="Assign user location as default",
     *   description="Assign a user location as default",
     *   operationId="addLocation",
     *   produces={"application/xml", "application/json"},
     *   consumes={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="userid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="locationid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */    
    public function assignDefaultLocation(Request $request, $userid, $locationid)
    {
        $user = User::findOrFail($userid);
        $location = Location::findOrFail($locationid);
        $this->authorize('assignDefaultLocation', [$user, $location]);
        
        $locations = $user->location()->get();
        foreach($locations as $loc){
            $loc->update(['isdefault' => ($loc->id==$locationid? 1:0)]);
        }
        
        $user->update(['address' => $location->address, 'google_place_id' => $location->google_place_id]);
        return $user;
    }
    
    /**
     * @SWG\Get(path="/users/{userid}/location/default",
     *   tags={"01 Users"},
     *   summary="Get the user's default location",
     *   description="Get the user's default location",
     *   operationId="viewDefaultLocation",
     *   produces={"application/xml", "application/json"},
     *   consumes={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="userid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="data not found"),
     * )
     */
    public function viewDefaultLocation(Request $request, $userid)
    {
        $this->authorize('viewDefaultLocation', [User::class, $userid]);
        $location = $request->user()->location()->first();
        if(!$location){ abort(404); }
        return $location;
    }
    
    /**
     * @SWG\Put(path="/users/{userid}/phone-number",
     *   tags={"01 Users"},
     *   summary="Update a user's phone number",
     *   description="Update a user's phone number",
     *   operationId="updatePhoneNumber",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="userid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="phone_number", in="query", required=true, type="string"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */    
    public function updatePhoneNumber(Request $request, $userid)
    {
        $user = User::findOrFail($userid);
        $this->authorize('updatePhoneNumber', [$user]);
        
        if($request->phone_number && $user->phone_number != $request->phone_number){
            $user->phone_number = $request->phone_number;
            $user->phone_verified = false;
            $user->save();
            return PhoneVerifyService::sendPhoneVerifyCode($user, $request->phone_number, $this->table_name);
        }else{
            return response()->json([ 'info' => 'Nothing to update' ]);
        }
    }
    
    /**
     * @SWG\Put(path="/users/{userid}/phone-number/confirm",
     *   tags={"01 Users"},
     *   summary="Confirm a user's phone number",
     *   description="Updae a user's phone number",
     *   operationId="confirmPhoneNumber",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="userid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="phone_verify_code", in="query", required=true, type="string"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=404, description="id does not exist"),
     *   @SWG\Response(response=422, description="validation error"),
     * )
     */
    public function confirmPhoneNumber(Request $request, $userid)
    {
        $user = User::findOrFail($userid);
        $this->authorize('confirmPhoneNumber', [$user]);
        
        $res = PhoneVerifyService::confirmPhoneNumber($user->phone_number, $request->phone_verify_code, $this->table_name, $user->id);
        $user->phone_verified = true;
        $user->save();
        return $res;
    }
    
    
    private function updateUserLocation(Request $request, $user)
    {
        if($request->google_place_id){
            // check if the same location already exists for this user
            $location = Location::where([['table_name', '=', $this->table_name], ['table_id', '=', $user->id], ['google_place_id', '=', $request->google_place_id]])->first();
            // If not, create a new location
            if(!$location){
                $location = LocationService::CreateLocationByGP_id($request->google_place_id, $this->table_name, $user->id);                
            }
            
            //Set this location as default
            $this->assignDefaultLocation($request, $user->id, $location->id);
        }
    }

}
