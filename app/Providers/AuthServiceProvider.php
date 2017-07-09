<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Carbon\Carbon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Bid' => 'App\Policies\BidPolicy',        
        'App\Dish' => 'App\Policies\DishPolicy',
        'App\Favorite' => 'App\Policies\FavoritePolicy',
        'App\Order' => 'App\Policies\OrderPolicy',
        'App\OrderSupport' => 'App\Policies\OrderSupportPolicy',
        'App\PickupLocation' => 'App\Policies\PickupLocationPolicy',
        'App\PickupMethod' => 'App\Policies\PickupMethodPolicy',        
        'App\Review' => 'App\Policies\ReviewPolicy',
        'App\Seller' => 'App\Policies\SellerPolicy',        
        'App\ShoppingCart' => 'App\Policies\ShoppingCartPolicy',
        'App\User' => 'App\Policies\UserPolicy',
        'App\Wish' => 'App\Policies\WishPolicy',
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        
        Passport::tokensExpireIn(Carbon::now()->addDays(15));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(14));
    }
}
