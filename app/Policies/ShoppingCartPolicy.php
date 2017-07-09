<?php

namespace App\Policies;

use App\User;
use App\ShoppingCart;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShoppingCartPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the shoppingCart.
     *
     * @param  \App\User  $user
     * @param  \App\ShoppingCart  $shoppingCart
     * @return mixed
     */
    public function view(User $user, ShoppingCart $shoppingCart)
    {
        //
    }

    /**
     * Determine whether the user can create shoppingCarts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the shoppingCart.
     *
     * @param  \App\User  $user
     * @param  \App\ShoppingCart  $shoppingCart
     * @return mixed
     */
    public function update(User $user, ShoppingCart $shoppingCart)
    {
        return $user->id === $shoppingCart->user_id;
    }

    /**
     * Determine whether the user can delete the shoppingCart.
     *
     * @param  \App\User  $user
     * @param  \App\ShoppingCart  $shoppingCart
     * @return mixed
     */
    public function delete(User $user, ShoppingCart $shoppingCart)
    {
        return $user->id === $shoppingCart->user_id;
    }

    /**
     * Determine whether the user can see the shoppingCart items.
     *
     * @param  \App\User  $user
     * @param  \App\ShoppingCart  $shoppingCart
     * @return mixed
     */    
    public function viewByBuyer(User $user, $user_id)
    {
        return $user->id == $user_id;
    }
}
