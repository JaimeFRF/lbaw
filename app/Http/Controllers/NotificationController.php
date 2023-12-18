<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Wishlist;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

use App\Events\NewNotification;

class NotificationController extends Controller
{
    public function  getNotifications() : View
    {
        if (!Auth::check()) {
            return redirect('/login');
        } else {
            $user = Auth::user();

            $notifications = Notification::where('id_user', $user->id)
            ->with(['user', 'purchase', 'item'])
            ->get();

            return view('layouts.navbar', compact('notifications'));
        }
    }
  
    public function sendItemNotification(Request $request)
    {
        $itemId = $request->input('item_id');
        $notificationType = $request->input('notification_type');
    
        $users = User::whereHas('wishlist', function ($query) use ($itemId) {
            $query->where('item_id', $itemId);
        })->get();
    
        foreach ($users as $user) {
            $notificationData = [
                'id_user' => $user->id,
                'notification_type' => $notificationType,
                'id_item' => $itemId,
            ];
    
            // Create a new notification
            $newNotification = Notification::create($notificationData);
    
            // Broadcast the new notification
            broadcast(new NewNotification($newNotification));
        }
    
        return response()->json(['message' => 'Item notification sent successfully']);
    }

    public function sendOrderNotification($userId, $purchaseId, $status)
    {

        $user = User::find($userId);

        $newNot = Notification::where('id_user', $userId)
        ->where('id_purchase', $purchaseId)
        ->orderBy('id', 'desc')
        ->first();

        Log::info('newNot: ', ['newNot' => $newNot]);

        broadcast(new NewNotification($newNot));
    
        return response()->json(['message' => 'Order update notification sent successfully']);
    }

    public function deleteNotification($id)
    {
        Log::info('id: ' . $id);    
        $notification = Notification::find($id);
        $notification->delete();

        $allNots = Notification::all();
        Log::info('allNots: ' . $allNots);
        return response()->json(['message' => 'Notification deleted successfully']);
    }
    
}