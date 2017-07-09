<?php

namespace App\Policies;

use App\User;
use App\Dish;
use App\Seller;
use Illuminate\Auth\Access\HandlesAuthorization;

class DishPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the dish.
     *
     * @param  App\User  $user
     * @param  App\Dish  $dish
     * @return mixed
     */
    public function view(User $user, Dish $dish)
    {
        //
    }

    /**
     * Determine whether the user can create dishes.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function create(User $user, Seller $seller)
    {
        
    }

    /**
     * Determine whether the user can update the dish.
     *
     * @param  App\User  $user
     * @param  App\Dish  $dish
     * @return mixed
     */
    public function update(User $user, Dish $dish)
    {
        $seller = $user->seller;
        return $seller->id === $dish->seller_id;
    }

    /**
     * Determine whether the user can delete the dish.
     *
     * @param  App\User  $user
     * @param  App\Dish  $dish
     * @return mixed
     */
    public function delete(User $user, Dish $dish)
    {
        $seller = $user->seller;
        return $seller->id === $dish->seller_id;
    }
    
    /**
     * Determine whether the user can delete the dish.
     *
     * @param  App\User  $user
     * @param  App\Dish  $dish
     * @return mixed
     */
    public function deleteImage(User $user, Dish $dish)
    {
        $seller = $user->seller;
        return $seller->id === $dish->seller_id;
        //return true;
    }
}
