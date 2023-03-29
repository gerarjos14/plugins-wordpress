<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, User $userUpdate)
    {
        $auth = false;
        if($user->isAdmin()){ $auth = true; }
        if($user->id == $userUpdate->parent_id){ $auth = true;}
        return $auth; 
    }
    
    public function delete(User $user, User $userDelete)
    {
        $auth = false;
        if($user->isAdmin()){ $auth = true; }
        if($user->id == $userDelete->parent_id){ $auth = true;}
        return $auth; 
    }

}
