<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Cart;
use App\Models\Review;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Shirt;
use App\Models\Tshirt;
use App\Models\Jacket;
use App\Models\Jeans;
use App\Models\Sneaker;
use App\Models\Image;


class CartController extends Controller
{
    /**
     * Show the card for a given id.
     */
    public function show(string $id): View
    {
        // Get the card.
        $cart = Cart::findOrFail($id);

        // Check if the current user can see (show) the card.
        $this->authorize('show', $cart);  

        // Use the pages.card template to display the card.
        return view('pages.cart', [
            'cart' => $cart
        ]);
    }

    /**
     * Shows all cards.
     */
    public function list()
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');

        } else {

            $user = Auth::user();
            $cart =  Auth::user()->cart()->get();
            Log::info('User: ', ['user' => $user]);
            Log::info('Cart: ', ['cart' => $cart]);
            $items = CartController::getItems($cart[0]->id);

            foreach ($items as $item) {
                $item->picture = Image::where('id_item', $item->id)->first()->filepath;
            }
            Log::info('Items: ', ['items' => $items]);
            return view('pages.carts', [
                'items' => $items
            ]);
        }
        
    }

    /**
     * 
     * Creates a new card.
     */
    public function create(Request $request)
    {
        // Create a blank new Card.
        $cart = new Cart();

        // Check if the current user is authorized to create this cart.
        $this->authorize('create', $cart);

        // Set cart details.
        $cart->name = $request->input('name');
        $cart->user_id = Auth::user()->id;

        // Save the cart and return it as JSON.
        $cart->save();
        return response()->json($cart);
    }

    /**
     * Delete a cart.
     */
    public function delete(Request $request, $id)
    {
        // Find the cart.
        $cart = Cart::find($id);

        // Check if the current user is authorized to delete this cart.
        $this->authorize('delete', $cart);

        // Delete the cart and return it as JSON.
        $cart->delete();
        return response()->json($cart);
    }

    public function getItems($cartID)
    {
        $cart = Cart::find($cartID);
        $items = $cart->products()->get();
        return $items;
    }
}
