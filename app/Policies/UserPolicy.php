<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;



class UserPolicy
{
    use HandlesAuthorization;
   
    public function show(User $user){
        return Auth::id() == $user->id;
    }

}
?>