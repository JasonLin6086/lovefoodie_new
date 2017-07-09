<?php

namespace App\Policies;

use App\User;
use App\Order;
use App\Seller;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function view(User $user, Order $order)
    {
        return $order->user_id===$user->id || ($user->isseller && $user->seller->id===$order->seller_id);
    }

    /**
     * Determine whether the user can create orders.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function update(User $user, Order $order)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function delete(User $user, Order $order)
    {
        return false;
    }
    
    /**
     * Determine whether the user can view the order as a buyer
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @param  int $userId
     * @return mixed
     */
    public function viewByBuyer(User $user, $userId)
    {
        return $user->id == $userId;
    }
    
     /**
     * Determine whether the user can view the order as a seller.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @param  int $sellerId
     * @return mixed
     */
    public function viewBySeller(User $user, $sellerId)
    {
        return $user->seller->id == $sellerId;
    }
    
     /**
     * Determine whether the user can view the order as a seller.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @param  int $sellerId
     * @return mixed
     */
    public function viewBySellerFiltered(User $user, $sellerId)
    {
        return $user->seller->id == $sellerId;
    }
    
    /**
     * Determine whether the user can accept the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function accept(User $user, Order $order)
    {
        return $order->status =='NEW' && $user->seller->id === $order->seller_id;
    }
    
    /**
     * Determine whether the user can reject the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function reject(User $user, Order $order)
    {
        return $order->status =='NEW' && $user->seller->id === $order->seller_id;
    }

    /**
     * Determine whether the user can deliver the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function deliver(User $user, Order $order)
    {
        return $order->status =='ACCEPTED' && $user->seller->id === $order->seller_id;
    }    

    /**
     * Determine whether the user can complete the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function complete(User $user, Order $order)
    {
        return $order->status =='DELIVERED' && $user->seller->id === $order->seller_id;
    }    
}
