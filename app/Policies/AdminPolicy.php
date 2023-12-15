<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminPolicy
{
    public function view(User $user = null, Admin $currentAdmin)
    {
        return Auth::guard('admin')->user()->id === $currentAdmin->id;
    }

    public function update(User $user = null, Admin $currentAdmin)
    {
        return Auth::guard('admin')->user()->id === $currentAdmin->id;
    }

    public function delete(User $user = null, Admin $currentAdmin)
    {
        return Auth::guard('admin')->user()->id === $currentAdmin->id;
    }

    public function create(User $user = null, Admin $currentAdmin)
    {
        return Auth::guard('admin')->user()->id === $currentAdmin->id;
    }

    public function ban(User $user = null, Admin $currentAdmin)
    {
        return Auth::guard('admin')->user()->id === $currentAdmin->id;
    }
}
