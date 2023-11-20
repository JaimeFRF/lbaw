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
        
        Log::info('itemId',['itemId' => $itemId]);
        Log::info('newQuantity',['newQuantity' => $newQuantity]);

        $cart =  Auth::user()->cart()->first();
        $items = $cart->products()->get();
        if (!$itemId) {
            return response()->json([
                'totalPrice' => $totalPrice,
                'message' => 'Item does not exist'
            ]);
        }

        if ($newQuantity == 0){
            Log::info('removido');
            $cart->products()->detach($itemId);
            Log::info('items',['items' => $items]);
            return response()->json([
                'totalPrice' => $totalPrice,
                'newQuantity' => $newQuantity,
                'message' => 'Item totally removed'
            ]); 
            //return redirect()->back()->with('success', 'Product removed from cart!');
        }

        $existsItem = $cart->products()->where('id', $itemId)->first();
        if ($existsItem) {
            
            $cart->products()->updateExistingPivot($itemId, ['quantity' => $newQuantity]);
        }
        else{
            $cart->products()->attach($item);
        }
        Log::info('cart',['cart' => $cart->products()->get()]);
        $totalPrice = 0;
        
        $products = $cart->products()->get();
        foreach ($products as $item) {
            $totalPrice += $item->price * $item->pivot->quantity;
        }
        
        Log::info('totalPrice',['totalPrice' => $totalPrice]);
        return response()->json([
            'totalPrice' => $totalPrice,
            'newQuantity' => $newQuantity,
            'message' => 'Price updated!'
        ]);
        // Log::info('New cart of Items: ', ['items' => $cart->products()->get()]);
        //return redirect()->back()->with('success', 'Product added to cart!');
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


        Log::info('New cart of Items: ', ['items' => $cart->products()->get()]);
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
    // Log::info('Cart: ', ['cart' => $cart]);
    $nrItems = $cart->count();
    return response()->json(['count' => $nrItems]);
}


}