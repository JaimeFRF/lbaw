<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;
use App\Models\Review;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Shirt;
use App\Models\Tshirt;
use App\Models\Jacket;
use App\Models\Jeans;
use App\Models\sneakers;
use App\Models\Image;


class CartController extends Controller
{
    public function show(string $id): View
    {        
        $cart = Cart::findOrFail($id);

        return view('pages.cart', [
            'cart' => $cart
        ]);
    }

    public function list()
    { 
        if (!Auth::check()) {
            $cart = Session::get('cart', []);

            foreach ($cart as &$cartItem) {
                $id = $cartItem['id'];
                $item = Item::find($id);
                if ($item) {        
                    if ($item->images()->count() > 0) {
                        $cartItem['picture'] = Image::where('id_item', $item->id)->first()->filepath;
                    } else {
                        $cartItem['picture'] = 'images/default-product-image.png';
                    }
                }
            }
            unset($cartItem);
            $items = $cart;
            Session::put('cart', $cart);
        }
        else {
            $cart =  Auth::user()->cart()->first();
            $this->authorize('show', $cart);
            
            $items = $cart->products()->get();
            foreach ($items as $item) {
                if($item->images()->count() > 0){
                    $item->picture = Image::where('id_item', $item->id)->first()->filepath;
                }else{
                    $item->picture = asset('images/default-product-image.png');
                }
            }
        }

        return view('pages.carts', [
            'breadcrumbs' => ['Home' => route('home')],
            'current' => 'Cart',
            'items' => $items
        ]);
    }

    public function create(Request $request)
    {
        $cart = new Cart();

        $this->authorize('create', $cart);

        $cart->name = $request->input('name');
        $cart->user_id = Auth::user()->id;

        $cart->save();
        return response()->json($cart);
    }

    public function delete(Request $request, $id)
    {
        $cart = Cart::find($id);

        $this->authorize('delete', $cart);

        $cart->delete();
        return response()->json($cart);
    }

}
