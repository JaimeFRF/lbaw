<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Review;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class ReviewPolicy
{
    use HandlesAuthorization;

    public function create(User $user){
        return Auth::check();
    }

    public function delete(User $user, Review $review){
        return $user->id == $review->id_user;
    }

    public function edit(User $user, Review $review){
        return $user->id == $review->id_user;
    }

}
