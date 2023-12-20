<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Wishlist;
use App\Models\Cart;
use App\Models\Item;
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
  
    public function sendRestockNotification($itemId)
    {
       Log::info('teste');
       $product = Item::find($itemId);

       $wishlist = Wishlist::where('id_item', $itemId)->get();
       $users = [];
        foreach($wishlist as $item){
            $users[] = User::find($item->id_user);
        }
    
        foreach ($users as $user) {
    
            $newNotification = Notification::where('id_user', $user->id)->where('id_item', $itemId)->where('notification_type', 'RESTOCK')->first();    

            broadcast(new NewNotification($newNotification, $product));
        }
    
        return response()->json(['message' => 'Item notification sent successfully']);
    }
    
    public function sendWishlistSaleNotification($itemId)
    {
       $product = Item::find($itemId);

       $wishlist = Wishlist::where('id_item', $itemId)->get();
       $users = [];
        foreach($wishlist as $item){
            $users[] = User::find($item->id_user);
        }
    
        foreach ($users as $user) {
    
            $newNotification = Notification::where('id_user', $user->id)->where('id_item', $itemId)->where('notification_type', 'SALE')->first();
    
            broadcast(new NewNotification($newNotification, $product));
        }
    
        return response()->json(['message' => 'Item notification sent successfully']);
    }

    public function sendOrderNotification($userId, $purchaseId, $status) {
        $user = User::find($userId);

        $newNot = Notification::where('id_user', $userId)
        ->where('id_purchase', $purchaseId)
        ->orderBy('id', 'desc')
        ->first();

        Log::info('newNot: ', ['newNot' => $newNot]);

        broadcast(new NewNotification($newNot));
    
        return response()->json(['message' => 'Order update notification sent successfully']);
    }

    public function sendPriceChangeNotification($itemId){
        
        $carts = Cart::whereHas('products', function ($query) use ($itemId) {
            $query->where('id_item', $itemId);
        })->get();

        $item = Item::find($itemId);

        foreach ($carts as $cart) {
            $user = User::where('id_cart', $cart->id)->first();

            if($user != null){
                $newNotification = Notification::where('id_user', $user->id)->where('id_item', $itemId)->where('notification_type', 'PRICE_CHANGE')->first();
                broadcast(new NewNotification($newNotification, $item));
            }
        }
        return response()->json(['message' => 'Price change notification sent successfully']);

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

    public function countNotifications()
    {
        $user = Auth::user();
        $notificationsCount = Notification::where('id_user', $user->id)->count();

        return response()->json(['notificationsCount' => $notificationsCount]);
    }
    
}