<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;
use GooglePlaces;
use App\Location;
use App\Service\LocationService;

class LocationService
{   
    public static function getLocationByRadius($target_lat, $target_lng, $distance=1, $table_name){

        return $result = DB::table('locations') 
                         ->where('table_name', $table_name)
                         ->selectRaw('*, ( 3959 * acos( cos( radians('.$target_lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$target_lng.')) + sin( radians('.$target_lat.') ) * sin( radians( latitude ) ) ) ) 
                                      as distance')
                        ->having('distance', '<', $distance)
                        //piginate not work here, have bug with 'having' statement
                        ->simplePaginate(20);
    }
    
    public static function CreateLocationByGP_id($google_place_id, $table_name, $table_id){
       //no exception handle
       $array = LocationService::LocationInfoArray($google_place_id);
       
       $location = Location::create([
                'table_id'=>$table_id,
                'table_name'=>$table_name,
                'google_place_id'=>$google_place_id,
                'latitude'=>$array[0],
                'longitude'=>$array[1],
                'address'=>$array[2],
                'city'=>$array[3],
                'zipcode'=>$array[4],
                'state'=>$array[5],
                'country'=>$array[6],
                ]);
       $location->save();
       return $location;
    }
    
    public static function UpdateLocationByGP_id($old_GP_id, $table_name, $table_id, $google_place_id ){
        
        if($old_GP_id == $google_place_id || $google_place_id == null){
            return;
        }else{
            $array = LocationService::LocationInfoArray($google_place_id);
            DB::table('locations')
            ->where([['table_name','=',$table_name],['table_id','=',$table_id]])
            ->update(['google_place_id'=>$google_place_id,
                      'latitude'=>$array[0],
                      'longitude'=>$array[1],
                      'address'=>$array[2],
                      'city'=>$array[3],
                      'zipcode'=>$array[4],
                      'state'=>$array[5],
                      'country'=>$array[6],
                      ]);
            return;
        }
     }
     
    public static function DeleteLocationByGP_id($table_name, $table_id){
        DB::table('locations')
                     ->where([['table_name','=',$table_name],['table_id','=',$table_id]])
                     ->delete();
        return 'Delete complete.';
    }
    
    private static function LocationInfoArray($google_place_id){
        $response = GooglePlaces::placeDetails($google_place_id)->toJson();
        $data =json_decode($response);

        $formatted_address = $data->{'result'}->{'formatted_address'};
        $address_components = $data->{'result'}->{'address_components'};
        $longitude= $data->{'result'}->{'geometry'}->{'location'}->{'lng'};
        $latitude = $data->{'result'}->{'geometry'}->{'location'}->{'lat'};
        $location_url = $data->{'result'}->{'url'};

        for ($i = 0; $i < sizeof($address_components); $i++) {
             for($j = 0; $j < sizeof($address_components[$i]->{'types'}); $j++){
                 $data_type = $address_components[$i]->{'types'};
                 if($data_type[$j]=='country'){
                     $country = $address_components[$i]->{'short_name'};
                     break;
                 }elseif($data_type[$j]=='administrative_area_level_1'){
                     $state = $address_components[$i]->{'short_name'};
                     break;
                 }elseif($data_type[$j]=='locality'){
                     $city = $address_components[$i]->{'long_name'};
                     break;
                 }elseif($data_type[$j]=='postal_code'){
                     $zipcode = $address_components[$i]->{'short_name'};
                     break;
                 }
             }
         }
        $locationInfoArray = array($latitude,$longitude, $formatted_address,$city,$zipcode, $state, $country, $location_url);
        return $locationInfoArray;
    }
}

