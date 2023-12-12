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
use Illuminate\Support\Facades\Session;

class CartItemController extends Controller
{

    public function addToCart(Request $request)
{
    $itemId = $request->input('itemId');
    $newQuantity = $request->input('quantity');
    $totalPrice = 0;

    if (Auth::check()) {
        // cart for authenticated users
        $cart = Auth::user()->cart()->first();
        $items = $cart->products()->get();
        $item = Item::find($itemId);

        if (!$item) {
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

            if ($updatedQuantity == 0) {
                $cart->products()->detach($itemId);
            } else {
                $cart->products()->updateExistingPivot($itemId, ['quantity' => $updatedQuantity]);
            }
        } else {
            $cart->products()->attach($itemId, ['quantity' => $newQuantity]);
        }
    } else {
        $cart = Session::get('cart', []);
        $item = Item::find($itemId);

        if (!$item) {
            return response()->json([
                'totalPrice' => $totalPrice,
                'message' => 'Item does not exist'
            ]);
        }
        $quantity = 0;
        $itemFound = false;
        foreach ($cart as $key => &$cartItem) {
            if ($cartItem['id'] == $itemId) {
                $cartItem['quantity'] += $newQuantity;
                $quantity = $cartItem['quantity'];
                if ($cartItem['quantity'] <= 0) {
                    unset($cart[$key]);
                } 
                $itemFound = true;
                break;
            }
        }
        unset($cartItem);
        if (!$itemFound && $newQuantity > 0) {
            $quantity = 1;
            $cart[] = [
                'id' => $itemId, 
                'quantity' => $newQuantity, 
                'price' => $item->price,
                'name' => $item->name,
            ];
        }
        Session::put('cart', $cart);
    }

    if (Auth::check()) {
        $products = $cart->products()->get();
    } else {
        $products = $cart;
    }
    foreach ($products as $id => $cartItem) {
        if (Auth::check()) {
            $totalPrice += $cartItem->price * $cartItem->pivot->quantity;
        } else {
            $totalPrice += $cartItem['price'] * $cartItem['quantity'];
        }
    }

    return response()->json([
        'totalPrice' => number_format($totalPrice, 2, '.', ''),
        'newQuantity' => $updatedQuantity ?? ($quantity),
        'message' => 'Cart updated!'
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

    $nrItems = 0;

    if (Auth::check()) {
        $items = Auth::user()->cart()->first()->products()->get();

        foreach ($items as $item) {
            $nrItems += $item->pivot->quantity;
        }
    } else {
        $cart = Session::get('cart', []);
        foreach ($cart as $cartIndex => $cartItem) {
            $nrItems += $cartItem['quantity'];
        }
    }
    return response()->json(['count' => $nrItems]);
}

}