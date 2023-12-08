<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class NotificationController extends Controller
{
    public function index($userId)
    {
        $user = User::find($userId);
        $notifications = $user->notifications;

        return view('notifications.index', compact('notifications'));
    }
}