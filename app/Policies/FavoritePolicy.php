<?php

namespace App\Policies;

use App\User;
use App\Favorite;
use Illuminate\Auth\Access\HandlesAuthorization;

class FavoritePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the favorite.
     *
     * @param  \App\User  $user
     * @param  \App\Favorite  $favorite
     * @return mixed
     */
    public function view(User $user, Favorite $favorite)
    {
        //
    }

    /**
     * Determine whether the user can create favorites.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the favorite.
     *
     * @param  \App\User  $user
     * @param  \App\Favorite  $favorite
     * @return mixed
     */
    public function update(User $user, Favorite $favorite)
    {
        //
    }

    /**
     * Determine whether the user can delete the favorite.
     *
     * @param  \App\User  $user
     * @param  \App\Favorite  $favorite
     * @return mixed
     */
    public function delete(User $user, Favorite $favorite)
    {
        return $user->id === $favorite->user_id;
    }
    
    /**
     * View favorite sellers by userid
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewByUser(User $user, $user_id)
    {
        return $user->id == $user_id;
    }
}
