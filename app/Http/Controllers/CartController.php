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

            //$carts = Auth::user()->carts()->get();
            $user = Auth::user();
            $carts =  Auth::user()->cart()->get();

            //FindPurchase using cart
            /*
            $cartTest = Cart::find(1);
            Log::info('Cart information', ['cartTest' => $cartTest]);
            $purchase = $cartTest->purchase()->get();
            Log::info('Purchase information', ['purchase' => $purchase]);
            */

            //Purchase Information Test
            /*
            $purchase = Purchase::find(1);
            Log::info('Purchase information', ['purchase' => $purchase]);
            $user_purchase = $purchase->user()->get();
            $user_location = $purchase->location()->get();
            $user_cart = $purchase->cart()->get();
            Log::info('User purchase information', ['user_purchase' => $user_purchase]);
            Log::info('Location purchase information', ['user_location' => $user_location]);
            Log::info('Cart purchase information', ['user_cart' => $user_cart]);
            */

            /*
            $review = Review::find(1);
            $user_review = $review->user()->get();
            $item_review = $review->item()->get();
            Log::info('User of a review', ['user_review' => $user_review]);
            Log::info('Item of a review', ['item_review' => $item_review]);
            */

            //Ver as reviews de um item
            /*
            $item = Item::find(1);
            $reviews = $item->reviews()->get();
            Log::info('Reviews of an item', ['reviews' => $reviews]);
            */


            //Ver as imagens de um item
            /*
            $item = Item::find(1);
            $images = $item->images()->get();
            Log::info('Images of an item', ['images' => $images]);
            */

            //Ver os carts de um user
            /*
            Log::info('Carts of an user', ['cart' => $carts]);
            */

            //Testar as reviews de um user, e ver so a primeira
            /*
            $reviews = Auth::user()->reviews()->get();
            $first_review = $reviews->first();
            Log::info('Reviews of an user', ['review' => $reviews]);
            Log::info('First review of an user', ['first_review' => $first_review]);
            foreach($reviews as $review){
                Log::info('Each review: ', ['review' => $review]);
            }
            */

            //Testar os produtos que estao num cart
            /*
            $cart1 = Cart::find(1);
            Log::info('Cart1', ['cart1' => $cart1]);
            Tem de usar products sem o ()
            $products = $cart1->products;
            Log::info('Products of cart1', ['products' => $products]);
            */


            //Log::info('User login attempt', ['cart' => $cartid]);
            //$reviews = Auth::user()->reviews()->get();
            


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
