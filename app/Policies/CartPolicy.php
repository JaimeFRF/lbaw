<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use Illuminate\Auth\Access\HandlesAuthorization;


class CartPolicy
{
    use HandlesAuthorization;

    public function show(User $user, Cart $cart){
        return $user->id == $cart->user()->get()->first()->id;
    }

    public function addItem(User $user, Cart $cart){
        return $user->id == $cart->user()->get()->first()->id;
    }

    public function removeItem(User $user, Cart $cart){
        return $user->id == $cart->user()->get()->first()->id;
    }
}
