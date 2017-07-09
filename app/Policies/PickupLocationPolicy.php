<?php

namespace App\Policies;

use App\User;
use App\PickupLocation;
use Illuminate\Auth\Access\HandlesAuthorization;

class PickupLocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the pickupLocation.
     *
     * @param  \App\User  $user
     * @param  \App\PickupLocation  $pickupLocation
     * @return mixed
     */
    public function view(User $user, PickupLocation $pickupLocation)
    {
        //
    }

    /**
     * Determine whether the user can create pickupLocations.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the pickupLocation.
     *
     * @param  \App\User  $user
     * @param  \App\PickupLocation  $pickupLocation
     * @return mixed
     */
    public function update(User $user, PickupLocation $pickupLocation)
    {
        return $user->seller->id === $pickupLocation->seller_id;
    }

    /**
     * Determine whether the user can delete the pickupLocation.
     *
     * @param  \App\User  $user
     * @param  \App\PickupLocation  $pickupLocation
     * @return mixed
     */
    public function delete(User $user, PickupLocation $pickupLocation)
    {
        return $user->seller->id === $pickupLocation->seller_id;
    }

    /**
     * Determine whether the user can view the pickupLocation.
     *
     * @param  \App\User  $user
     * @param  int  $seller_id
     * @return mixed
     */    
    public function viewBySeller(User $user, $seller_id)
    {
        return $user->seller->id == $seller_id;
    }
}
