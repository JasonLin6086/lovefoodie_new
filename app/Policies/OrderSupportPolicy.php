<?php

namespace App\Policies;

use App\User;
use App\OrderSupport;
use App\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderSupportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the orderSupport.
     *
     * @param  \App\User  $user
     * @param  \App\OrderSupport  $orderSupport
     * @return mixed
     */
    public function view(User $user, OrderSupport $orderSupport, Order $order)
    {
        return $user->id === $order->user_id || $user->id === $order->seller_id;
    }

    /**
     * Determine whether the user can create orderSupports.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, Order $order)
    {
        return $user->id === $order->user_id;
    }

    /**
     * Determine whether the user can update the orderSupport.
     *
     * @param  \App\User  $user
     * @param  \App\OrderSupport  $orderSupport
     * @return mixed
     */
    public function update(User $user, OrderSupport $orderSupport)
    {
        //
    }

    /**
     * Determine whether the user can delete the orderSupport.
     *
     * @param  \App\User  $user
     * @param  \App\OrderSupport  $orderSupport
     * @return mixed
     */
    public function delete(User $user, OrderSupport $orderSupport)
    {
        //
    }

    /**
     * Determine whether the user can add solution to the orderSupport.
     *
     * @param  \App\User  $user
     * @param  \App\OrderSupport  $orderSupport
     * @return mixed
     */
    public function addSolution(User $user, Order $order)
    {
        $seller = Seller::where('user_id', $user->id)->first();
        return $seller->id === $order->seller_id;
    }
}
