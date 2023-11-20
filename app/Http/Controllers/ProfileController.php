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


class ProfileController extends Controller{

    /**
   * @method Displays the edit profile form
   * @param id Id of the User whose profile will be edited
   */
    public function show()
    {
      $user = User::find(Auth::id());

      $image = Image::where('id_user', $user->id)->first();

      // Check if the image record exists and filepath is not null
      if ($image && $image->filepath) {
          $profile_picture = $image->filepath;
          Log::info('profile_picture: ', ['profile_picture' => $profile_picture]);
        } else {
          // Handle the case where there is no image or filepath is null
          // For example, set a default image path
          $profile_picture = 'images/default-product-image.png';
      }  
      $wishlist = Wishlist::where('id_user', $user->id)->get();

      $items_wishlist = [];

      foreach($wishlist as $item){
        $items_wishlist[] = Item::find($item->id_item);
      }

      Log::info('Wishlist: ', ['wishlist' => $items_wishlist]);

      return view('pages.profile.profile', [
        'user' => $user,
        'items_wishlist' => $items_wishlist,
        'profile_picture' => $profile_picture
      ]);
    }
}
