<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Item;
use App\Models\Purchase;



class CartItemController extends Controller
{

    public function addToCart(Request $request)
    {
        $itemId = $request->input('itemId');
        $newQuantity = $request->input('quantity');
        $totalPrice = 0;
        $cart =  Auth::user()->cart()->first();
        $items = $cart->products()->get();
        $item = Item::find($itemId);
        if (!$itemId) {
            return response()->json([
                'totalPrice' => $totalPrice,
                'message' => 'Item does not exist'
            ]);
        }
        
        $updatedQuantity = $newQuantity;
        $existsItem = $cart->products()->where('id_item', $itemId)->first();
        if ($existsItem) {
            $currentQuantity = $cart->products()->where('id_item', $itemId)->first()->pivot->quantity;
            $updatedQuantity = $currentQuantity + $newQuantity;
            if ($updatedQuantity == 0){
                $cart->products()->detach($itemId);
                $products = $cart->products()->get();
                foreach ($products as $item) {
                    $totalPrice += $item->price * $item->pivot->quantity;
                }
                return response()->json([
                    'totalPrice' => $totalPrice,
                    'newQuantity' => $updatedQuantity,
                    'message' => 'Item totally removed'
                ]); 
            }
            else{
                $cart->products()->updateExistingPivot($itemId, ['quantity' => $updatedQuantity]);
            }
        }
        else{
            $cart->products()->attach($item);
        }

        $products = $cart->products()->get();
        foreach ($products as $item) {
            $totalPrice += $item->price * $item->pivot->quantity;
        }
        
        return response()->json([
            'totalPrice' => $totalPrice,
            'newQuantity' => $updatedQuantity,
            'message' => 'Price updated!'
        ]);
    }

    public function deleteFromCart(Request $request, $productId)
    {
        $cart =  Auth::user()->cart()->first();
        $items = $cart->products()->get();
        $item = Item::find($productId);
        

        if (!$item) {
            return redirect()->back()->with('error', 'Item not found.');
        }
        $cart->products()->detach($productId);
        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function removeFromCart(Request $request,$productId)
    {
    $cart = Auth::user()->cart()->first();

    if (!$cart) {
        return redirect()->back()->with('error', 'Cart not found.');
    }

    $item = $cart->products()->find($productId);

    if (!$item) {
        return redirect()->back()->with('error', 'Item not found in cart.');
    }

    // Decrement the quantity
    $currentQuantity = $item->pivot->quantity;
    if ($currentQuantity > 1) {
        // If more than one, just decrement
        $cart->products()->updateExistingPivot($productId, ['quantity' => $currentQuantity - 1]);
    } else {
        // If only one, remove the item completely
        $cart->products()->detach($productId);
    }

    return redirect()->back()->with('success', 'Item updated in cart.');
    }

public function countItemCart(Request $request){

    $cart = Auth::user()->cart()->first()->products()->get();
    $nrItems = $cart->count();
    return response()->json(['count' => $nrItems]);
}


}