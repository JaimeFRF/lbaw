<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Image;
use App\Models\Wishlist;
use App\Models\User;
use App\Models\Item;
use App\Models\Cart;
use App\Models\Purchase;



class ProfileController extends Controller{

    /**
   * @method Displays the edit profile form
   * @param id Id of the User whose profile will be edited
   */
    public function show()
    {
      $user = User::find(Auth::id());

      $image = Image::where('id_user', $user->id)->first();

      if ($image && $image->filepath) {
          $profile_picture = $image->filepath;
        } else {
          $profile_picture = 'images/default-product-image.png';
      }  

      $purchases = Purchase::where('id_user', $user->id)->get();

      $wishlist = Wishlist::where('id_user', $user->id)->get();

      $items_wishlist = [];

      foreach($wishlist as $item){
        $items_wishlist[] = Item::find($item->id_item);
      }

      $carts_purchase = [];
      $purchases = Purchase::where('id_user', $user->id)->get();
      foreach($purchases as $purchase){
        $carts_purchase[] = Cart::find($purchase->id_cart);
      }






      return view('pages.profile.profile', [
        'user' => $user,
        'items_wishlist' => $items_wishlist,
        'profile_picture' => $profile_picture,
        'purchases' => $purchases,
        'carts_purchase' => $carts_purchase
      ]);
    }
}
