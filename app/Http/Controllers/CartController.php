<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Cart;

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

            $carts = Auth::user()->carts()->get();

            


            // Check if the current user can list the cards.
           // $this->authorize('list', Cart::class);

            // The current user is authorized to list cards.

            // Use the pages.cards template to display all cards.
            return view('pages.carts', [
                'carts' => $carts
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
}
