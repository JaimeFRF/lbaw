<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
use Illuminate\Support\Facades\Log;


class PurchaseController extends Controller
{
    public function createPurchase(Request $request)
    {
        $user = User::find(Auth::id());
    
        $items = json_decode($request->input('items'), true);
        Log::info('items: ',['items' => $items]);
        $purchase_price = 0;
        foreach($items as $item){
            Log::info('item: ', ['item' => $item]);
            Log::info('item_price: ', ['item_price' => $item['price']]);
            $purchase_price += $item['price'] * $item['pivot']['quantity'];
        }
    
        $entry = new Purchase;
        $entry->id_user = $user->id;
        $entry->price = $purchase_price;
        $entry->id_cart = $item['pivot']['id_cart'];
        $entry->purchase_date = date('Y-m-d'); // Current date
        $entry->delivery_date = date('Y-m-d', strtotime('+3 days')); // Delivery date 3 days from now
        $entry->purchase_status = 'Processing'; // Default status
        $entry->payment_method = 'Transfer'; // Assuming payment method is passed in request
        $entry->id_location = 1; // Assuming location_id is a field in User model
    
        $entry->save();

        $purchases = $user->purchases()->get();
    
        Log::info('purchases: ', ['purchases' => $purchases]);
    
        $items = Item::all();
        return view('pages.home', [
            'items' => $items,
            'totalItems' => $items->count()
        ]);
    }

}