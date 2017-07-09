<?php

namespace App\Policies;

use App\User;
use App\Seller;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the seller.
     *
     * @param  App\User  $user
     * @param  App\Seller  $seller
     * @return mixed
     */
    public function view(User $user, Seller $seller)
    {
        return true;
    }

    /**
     * Determine whether the user can create sellers.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        echo("got it");
        return true;
    }

    /**
     * Determine whether the user can update the seller.
     *
     * @param  App\User  $user
     * @param  App\Seller  $seller
     * @return mixed
     */
    public function update(User $user, Seller $seller)
    {
        return $user->id === $seller->user_id;
    }

    /**
     * Determine whether the user can delete the seller.
     *
     * @param  App\User  $user
     * @param  App\Seller  $seller
     * @return mixed
     */
    public function delete(User $user, Seller $seller)
    {
        //return false??
    }
}
