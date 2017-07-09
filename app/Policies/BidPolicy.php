<?php

namespace App\Policies;

use App\User;
use App\Bid;
use App\Seller;
use App\Wish;
use Illuminate\Auth\Access\HandlesAuthorization;

class BidPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the bid.
     *
     * @param  \App\User  $user
     * @param  \App\Bid  $bid
     * @return mixed
     */
    public function view(User $user, Bid $bid)
    {
        //
    }

    /**
     * Determine whether the user can create bids.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the bid.
     *
     * @param  \App\User  $user
     * @param  \App\Bid  $bid
     * @return mixed
     */
    public function update(User $user, Bid $bid)
    {
        $seller = Seller::where('user_id', $user->id)->first();
        return $seller->id === $bid->seller_id;
    }

    /**
     * Determine whether the user can delete the bid.
     *
     * @param  \App\User  $user
     * @param  \App\Bid  $bid
     * @return mixed
     */
    public function delete(User $user, Bid $bid)
    {
        $seller = Seller::where('user_id', $user->id)->first();
        return $seller->id === $bid->seller_id;
    }
    
    
    public function assignBidStatus(User $user, Bid $bid, Wish $wish)
    {
        return $user->id === $wish->user_id;
    }
}
