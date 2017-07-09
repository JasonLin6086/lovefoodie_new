<?php

use Illuminate\Database\Seeder;

class PickupLocationMappingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$pickupMappings = factory(App\PickupLocationMapping::class, 500)->create();
        
        $pickupMethod = null;
        $pickupLocation = null;

        foreach (range(1, 200) as $index) {
            repeat:
            try{
                $pickupMethod = App\PickupMethod::findOrFail(random_int(\DB::table('pickup_methods')->min('id'), \DB::table('pickup_methods')->max('id')));
                $pickupLocations = App\PickupLocation::where('seller_id', $pickupMethod->seller_id)->get();
                if(sizeof($pickupLocations)==0){ goto repeat; }
                $pickupLocation = $pickupLocations[random_int(0, sizeof($pickupLocations)-1)];
                
                $count = DB::table('pickup_location_mappings')->where('pickup_method_id', $pickupMethod->id)->where('pickup_location_id', $pickupLocation->id)->get()->count();
                if($count>0){ goto repeat; }
                
                App\PickupLocationMapping::create([
                    'pickup_method_id' => $pickupMethod->id,
                    'pickup_location_id'=> $pickupLocation->id,
                    'description'=> $pickupLocation->description,
                    'address' => $pickupLocation->address,
                    'google_place_id' => $pickupLocation->google_place_id,
                ]);
            } catch (Exception $ex) {
                goto repeat;
            }
           
        }         
    }
}
