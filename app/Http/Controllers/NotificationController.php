<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Wishlist;
use App\Models\Notification;

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

    public function sendOrderNotification(Request $request)
    {
        $userId = $request->input('user_id');
        $purchaseId = $request->input('purchase_id');

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        $notificationData = [
            'id_user' => $userId,
            'notification_type' => 'ORDER_UPDATE',
            'id_purchase' => $purchaseId,
        ];
    
        $newNotification = Notification::create($notificationData);
    
        broadcast(new NewNotification($newNotification));
    
        return response()->json(['message' => 'Order update notification sent successfully']);
    }

    public function deleteNotification($id)
    {
        $notification = Notification::find($id);
        $notification->delete();
        return response()->json(['message' => 'Notification deleted successfully']);
    }
    
}