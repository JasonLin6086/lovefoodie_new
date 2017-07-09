<?php

namespace App\Policies;

use App\User;
use App\PickupMethod;
use Illuminate\Auth\Access\HandlesAuthorization;

class PickupMethodPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the pickupMethod.
     *
     * @param  \App\User  $user
     * @param  \App\PickupMethod  $pickupMethod
     * @return mixed
     */
    public function view(User $user, PickupMethod $pickupMethod)
    {
        return $user->seller()->get()->seller_id === $pickupMethod->id;
    }

    /**
     * Determine whether the user can create pickupMethods.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the pickupMethod.
     *
     * @param  \App\User  $user
     * @param  \App\PickupMethod  $pickupMethod
     * @return mixed
     */
    public function update(User $user, PickupMethod $pickupMethod)
    {
        $seller = $user->seller;
        return $seller->id === $pickupMethod->seller_id;
    }

    /**
     * Determine whether the user can delete the pickupMethod.
     *
     * @param  \App\User  $user
     * @param  \App\PickupMethod  $pickupMethod
     * @return mixed
     */
    public function delete(User $user, PickupMethod $pickupMethod)
    {
        $seller = $user->seller;
        return $seller->id === $pickupMethod->seller_id;
    }
    
    public function viewBySeller(User $user, $sellerid)
    {
        $seller = $user->seller;
        return $seller->id == $sellerid;
    }
}
