<?php
use \Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
static $password;

/*    return [
        'provider' => 'password',
        'provider_user_id' => $faker-> ean8,
        'image' => $faker->imageUrl(),
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => $password ?: $password = bcrypt('secret'),
        'isseller' => 0,
        'remember_token' => str_random(10),
    ];
*/
      return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
      ];
});

$factory->define(App\Category::class, function (Faker\Generator $faker) {
    $category_name = array('Chinese', 'Indian', 'Healthy', 'Italian', 'Salad', 'Pizza', 'Noodle');
    return [
        'name' => $category_name[random_int(0,6)],
        'order' => $faker->randomDigit
    ];
});

$factory->define(App\Seller::class, function (Faker\Generator $faker) {
    $user = factory(App\User::class)->create();
    $user->update(['isseller'=> 1]);
    
    return [
        'user_id' => $user->id,
        'icon' => $faker->imageUrl(),
        'kitchen_name' => $faker->unique()->company,
        'phone_number' => $faker-> phoneNumber,
        'email' => $faker-> email,
        'address' => $faker-> streetAddress,
        'google_place_id' => $faker->uuid,
        'description' => $faker->sentence,
        'rating' => $faker-> randomFloat($nbMaxDecimals = 2, $min = 0, $max = 5),
        'rating_count' => random_int(0, 200),
    ];
});


$factory->define(App\Dish::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->word,
        'price' => $faker-> randomFloat($nbMaxDecimals = 4, $min = 5, $max = 30),
        'description' => $faker->sentence,
        'rating' => $faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 5),
        'rating_count' => random_int(0, 200),
        'seller_id' => random_int(\DB::table('sellers')->min('id'),
                \DB::table('sellers')->max('id')),
        'category_id' => random_int(\DB::table('categories')->min('id'), 
                \DB::table('categories')->max('id')),
    ];
});

/*
$factory->define(App\Review::class, function (Faker\Generator $faker ) {
    
    return [
        'rating' => $faker-> randomFloat($nbMaxDecimals = 2, $min = 0, $max = 5),
        'description' => $faker->sentence,
        'dish_id' => factory(App\Dish::class)->create()->id,
        'seller_id' => random_int(\DB::table('sellers')->min('id'),
                \DB::table('sellers')->max('id')),
        'user_id' => random_int(\DB::table('users')->min('id'), 
                \DB::table('users')->max('id'))
    ];
});

$factory->define(App\Keyword::class, function (Faker\Generator $faker) {

    return [
        'word' => $faker->word,
        'order' => $faker-> randomDigit,
        'seller_id' => random_int(\DB::table('sellers')->min('id'), 
                \DB::table('sellers')->max('id')),        
        'dish_id' => random_int(\DB::table('dishes')->min('id'), 
                \DB::table('dishes')->max('id')),
    ];
});

$factory->define(App\DishImage::class, function (Faker\Generator $faker) {

    return [
        'path' => $faker->imageUrl(),
        'order' => $faker-> randomDigit,
        'dish_id' => random_int(\DB::table('dishes')->min('id'), 
                \DB::table('dishes')->max('id')),
    ];
});*/

$factory->define(App\ShoppingCart::class, function (Faker\Generator $faker) {
    
    $quantity = random_int(1,6);
    $dish = null;
    $user = null;
    
    repeat:
    try{
        $dish = App\Dish::findOrFail(random_int(\DB::table('dishes')->min('id'), \DB::table('dishes')->max('id')));
        $user = App\User::findOrFail(random_int(\DB::table('users')->min('id'), \DB::table('users')->max('id')));
    }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
        goto repeat;
    }
    
    $count = DB::table('shopping_carts')->where('dish_id', $dish->id)->where('user_id', $user->id)->get()->count();
    if($count>0){ goto repeat; }
    
    return [
            'user_id' => $user->id,
            'seller_id'=> $dish->seller_id,
            'dish_id'=> $dish->id,
            'dish_name' => $dish->name,
            'quantity' => $quantity,
            'unit_price' => $dish->price,
            'total_price' => $dish->price * $quantity,
    ];
});

$factory->define(App\Order::class, function (Faker\Generator $faker) {
    $pickup_method = array('PICKUP', 'DELIVER');
    $payment_method = array('STRIPE');
    $type = array('BID', 'REGULAR');
    
    $PM = $pickup_method[random_int(0,1)];
    return [
        'type' => $type[random_int(0,1)],
        'deliver_fee' => $PM=='PICKUP'? 0: $faker->randomFloat($nbMaxDecimals = 4, $min = 0, $max = 5),
        'total' => $faker-> randomFloat($nbMaxDecimals = 4, $min = 80, $max = 100),
        'order_time' => $faker-> dateTimeThisMonth,
        'pickup_time' => $faker-> dateTimeThisMonth,
        'pickup_description' => $PM,
        'address' => $faker-> streetAddress,
        'google_place_id' => $faker->uuid,
        'pay_time' => $faker-> dateTimeThisMonth,
        'payment_method' => $payment_method[random_int(0,0)],
        'complete_time' => $faker-> dateTimeThisMonth,
        'user_id' => random_int(\DB::table('users')->min('id'), 
                \DB::table('users')->max('id')),
        'seller_id' => random_int(\DB::table('sellers')->min('id'), 
                \DB::table('sellers')->max('id')),
    ];
});

/*
$factory->define(App\OrderDetail::class, function (Faker\Generator $faker) {
    $quantity = random_int(1,10);
    $unit_price = $faker-> randomFloat($nbMaxDecimals = 4, $min = 5, $max = 30);
    $total_price = $quantity*$unit_price;
    
    return [
        'dish_name' => $faker->word,
        'quantity' => $quantity,
        'unit_price' => $unit_price,
        'total_price' => $total_price,
        'order_id' => random_int(\DB::table('orders')->min('id'), 
                \DB::table('orders')->max('id')),
    ];
});*/

$factory->define(App\OrderSupport::class, function (Faker\Generator $faker) {
    $array = array('new', 'seller_replied');
    $problem_code_id = array('5','6', '10101', '10102', '10103', '102','103','201','202','203', '204', '301','302', '401', '402');
    return [
        'status' =>  $array[random_int(0,1)],
        'seller_description' => $faker->sentence,
        'user_description' => $faker->sentence,
        'order_id' => random_int(\DB::table('orders')->min('id'), 
                \DB::table('orders')->max('id')),
        
        'problem_code_id' => $problem_code_id[random_int(0,14)],
        
        'solution_id' => random_int(\DB::table('solutions')->min('id'), 
                \DB::table('solutions')->max('id')),
    ];
});

$factory->define(App\Wish::class, function (Faker\Generator $faker) {
    $array = array('OPEN', 'CLOSE');
    $pick_type = array('PICKUP', 'DELIVER');
    $categoriesIds = App\Category::all()->pluck('id');
    $userIds = App\User::all()->pluck('id');
    
    return [
        'topic' => $faker->word,
        'description' => $faker->sentence,
        'pickup_time' => $faker-> dateTimeThisMonth,
        'pickup_method' => $pick_type[random_int(0,1)],
        'address' => $faker-> streetAddress,
        'google_place_id' => $faker->uuid,
        'quantity' => $faker-> randomDigit,
        'end_date' => $faker-> dateTimeThisMonth,
        'status' => $array[random_int(0,1)],
        'price_from' => $faker-> randomFloat($nbMaxDecimals = 4, $min = 5, $max = 30),
        'price_to' => $faker-> randomFloat($nbMaxDecimals = 4, $min = 80, $max = 100),
        'category_id' => $categoriesIds[random_int(0, sizeof($categoriesIds)-1)],
        'user_id' => $userIds[random_int(0, sizeof($userIds)-1)],
    ];
});

$factory->define(App\PickupMethod::class, function (Faker\Generator $faker) {
    $typeArray = array('DATE', 'WEEKDAY');
    $weekdayArray = array('1,2,3', '1,3,5', '2,4,6', '2', '6,7', '3');
    $weekdayMsgArray = array('Mon-Wed', 'Mon,Wed,Fri', 'Tue,Thr,Sat', 'Tue', 'Sat-Sun', 'Wed');
    $weekdayMsgArray2 = array('Mon', 'Tue', 'Wed', 'Thr', 'Fri', 'Sat', 'Sun');
    $timeArray = array('09:30', '12:00', '13:30', '20:00');
    $weekIdx = random_int(0,5);
    $no_time = $faker->boolean();
    $start_time = Carbon::createFromFormat('H:i', $timeArray[random_int(0, 3)]);
    $type = $typeArray[random_int(0,1)];
    $seller_id = 0;
    $date = Carbon::now()->addDays(random_int(0, 30));
    $dayOfWeek = $date->dayOfWeek==0? 7: $date->dayOfWeek;
    
    repeat:
    try{
        $seller_id = random_int(\DB::table('sellers')->min('id'), 
                \DB::table('sellers')->max('id'));
        \App\Seller::findOrFail($seller_id);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        goto repeat;
    }

    return [
            'seller_id' => $seller_id,
            'type' => $type,
            'date' => $type!='DATE'? null: $date->addDays(random_int(0, 30))->format('m/d/Y'), 
            'weekday' => $type=='DATE'? $dayOfWeek: $weekdayArray[$weekIdx],
            'weekday_msg' => $type=='DATE'? $weekdayMsgArray2[$dayOfWeek-1]: $weekdayMsgArray[$weekIdx],
            'no_time' => $no_time,
            'start_time'=> $no_time? null : $start_time->format('H:i'),
            'end_time'=> $no_time? null : $start_time->addMinutes(30)->format('H:i')
    ];
});

/*
$factory->define(App\Location::class, function (Faker\Generator $faker) {
    $array = array('sellers', 'orders', 'wishes', 'pickup_locations');
    $table_name = $array[random_int(0,3)];
    if($table_name == 'sellers'){
        $table_id = factory(App\Seller::class)->create()->id;
    }elseif($table_name == 'orders'){
        $table_id = factory(App\Order::class)->create()->id;
    }elseif($table_name == 'wishes'){
        $table_id = factory(App\Wish::class)->create()->id;
    }else{
        $table_id = factory(App\PickupLocation::class)->create()->id;
    }
    return [
        'table_name'=> $table_name,
        'table_id'=> $table_id,
        'google_place_id'=> $faker->uuid,
        'latitude'=> $faker->latitude,
        'longitude'=> $faker->longitude,
        'address'=> $faker->streetAddress,
        'city'=> $faker->city,
        'zipcode'=> $faker->postcode,
        'state'=>$faker->state,
        'country'=>$faker->country
    ];
});
*/

$factory->define(App\Ingredient::class, function (Faker\Generator $faker) {

    return [
        'word' => $faker->word,
        'order' => $faker-> randomDigit,     
        'dish_id' => random_int(\DB::table('dishes')->min('id'), 
                \DB::table('dishes')->max('id')),
    ];
});

$factory->define(App\PickupLocation::class, function (Faker\Generator $faker) {
    
    $seller_id = 0;
    repeat:
    try{
        $seller_id = random_int(\DB::table('sellers')->min('id'), 
                \DB::table('sellers')->max('id'));
        \App\Seller::findOrFail($seller_id);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        goto repeat;
    }    
    
    return [
        'seller_id' => $seller_id,
        'description' => $faker->sentence,
        'address' => $faker-> streetAddress,
        'google_place_id' => $faker->uuid,
        'order'=> random_int(0,20),
    ];
});

