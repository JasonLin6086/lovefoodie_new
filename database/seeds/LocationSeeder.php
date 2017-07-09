<?php

use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $location = factory(App\Location::class, 50)->create();
//        $faker = Faker\Factory::create();
//        App\Location::truncate();
//        
//        $tables = array('users', 'sellers', 'orders', 'wishes', 'pickup_locations');
//        
//        foreach($tables as $table){
//            $data = \DB::table($table)->get();            
//            foreach($data as $d){
//                App\Location::create([
//                    'table_name'=> $table,
//                    'table_id'=> $d->id,
//                    'google_place_id'=> $d->google_place_id? $d->google_place_id: 'ChIJ47ZLWBDGj4ARy7_PEMHdDUs',
//                    'latitude'=> $faker->latitude($min = 37.2, $max = 37.4),
//                    'longitude'=> $faker->longitude($min = -121.99, $max = -122.15), 
//                    'address'=> $d->address? $d->address: '830 Stewart Dr, Sunnyvale, CA 94085',
//                    'city'=> $faker->city,
//                    'zipcode'=> $faker->postcode,
//                    'state'=>$faker->state,
//                    'country'=>$faker->country
//                ]);
//            }
//        } 
    }
}
