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
        $productId = $request->input('productId');
        $cart =  Auth::user()->cart()->first();
        $items = $cart->products()->get();
        $item = Item::find($productId);
        if (!$item) {
            return redirect()->back()->with('error', 'Item not found.');
        }
        $existsItem = $cart->products()->where('id', $productId)->first();

        if ($existsItem) {
            $cart->products()->updateExistingPivot($productId, ['quantity' => $existsItem->pivot->quantity + 1]);
        }
        else{
            $cart->products()->attach($item);
        }

        Log::info('New cart of Items: ', ['items' => $cart->products()->get()]);
        return redirect()->back()->with('success', 'Product added to cart!');
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