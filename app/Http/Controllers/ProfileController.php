<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
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


      $wishlist = Wishlist::where('id_user', $user->id)->get();

      $items_wishlist = [];

      foreach($wishlist as $item){
        $items_wishlist[] = Item::find($item->id_item);
      }

      $carts_orders = [];
      $orders = Purchase::where('id_user', $user->id)->where('purchase_status', '!=', 'Delivered')->get();
      foreach($orders as $order){
        $carts_orders[] = Cart::find($order->id_cart);
      }

      $carts_purchases = [];
      $purchases = Purchase::where('id_user', $user->id)->where('purchase_status', '=', 'Delivered')->get();
      foreach($purchases as $purchase){
        $carts_purchases[] = Cart::find($purchase->id_cart);
      }

      return view('pages.profile.profile', [
        'user' => $user,
        'items_wishlist' => $items_wishlist,
        'profile_picture' => $profile_picture,
        'orders' => $orders,
        'carts_orders' => $carts_orders,
        'purchases' => $purchases,
        'carts_purchases' => $carts_purchases
      ]);
    }


    public function showEditProfile() {
      $user = User::find(Auth::id());
      
      Log::info('User: ', ['user' => $user]);
  
      return view('pages.profile.edit_profile', [
        'user' => $user
      ]);
    }
  
  
    public function changeUsername(Request $request) {
      if (Auth::check()) {
          $user = User::find(Auth::id());
          $new_username = $request->input('new_username');
  
          if($new_username === null){
              return view('pages.profile.edit_profile', ['user' => $user, 'errorUsername' => 'Username cannot be empty']);
          }
          else{
              $existingUser = User::where('username', $new_username)->first();
              if($existingUser){
                  return view('pages.profile.edit_profile', ['user' => $user, 'errorUsername' => 'Username already exists']);
              }
              else{
                  $user->username = $new_username;
                  $user->save();
                  return view('pages.profile.edit_profile', ['user' => $user, 'successUsername' => 'Username changed successfully']);
              }
          }
      } else {
          return response()->json(['message' => 'User not authenticated']);
      }
  }
  
    public function changeName(Request $request){
      if(Auth::check()) {
        $user = User::find(Auth::id());
        $new_name = $request->input('new_name');
        $user->name = $new_name;
        $user->save();
  
        Log::info('new_name: ', ['new_name' => $new_name]);
  
        return view('pages.profile.edit_profile', ['user' => $user, 'successName' => 'Name changed successfully']);
      }else{
          return response()->json(['message' => 'User not authenticated']);
      }
    }
  
    public function changePassword(){
      if(Auth::check()){
        $user = User::find(Auth::id());
        $new_password = $request->input('new_password');
        $new_password_confirmation = $request->input('new_password_confirmation');
        if(strlen($new_password) < 10){
          return view('pages.profile.edit_profile', ['user' => $user, 'errorPassword' => 'Password must be longer than 10 characters']);
        }
        else if($new_password !== $new_password_confirmation){
          return view('pages.profile.edit_profile', ['user' => $user, 'errorPassword' => 'Passwords do not match']);
        }
        else{
          $user->password = Hash::make($new_password);
          $user->save();
          return view('pages.profile.edit_profile', ['user' => $user, 'successPassword' => 'Password changed successfully']);
        }
      }
    }
  
    public function removeUser(Request $request){
      if(Auth::check()){
        $user = User::find(Auth::id());
        $password = request()->input('password');
        if(!Hash::check($password, $user->password)){
          return view('pages.profile.edit_profile', ['user' => $user, 'errorRemove' => 'Incorrect password']);
        }
        else{
          $user->delete();
          return redirect()->route('home');
        }
      }
    }
  
    public function changePicture(Request $request){
      if(Auth::check()){
        $user_id = Auth::user()->id;
        $request->validate([
          'imageInput' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
  
        if ($request->hasFile('imageInput')) {
          $file = $request->file('imageInput');
          $extension = $file->getClientOriginalExtension();
          
          $filename = 'profile_user_' . $user_id . '.' . $extension;
  
          if (Storage::disk('public')->exists('images/' . $filename)) {
            Storage::disk('public')->delete($filename);
          }
  
          $path = $file->storeAs('images', $filename, 'public');
          Log::info('path: ', ['path' => $path]);
  
          $existingImage = Image::where('id_user', $user_id)->first();
  
          if ($existingImage) {
            $existingImage->filepath = 'storage/images/' . $filename;
            $existingImage->save();
          } else {
            $newImage = new Image;
            $newImage->id_user = $user_id;
            $newImage->filepath = 'storage/images/' . $filename;
            $newImage->save();
          }
        
        }
        return redirect()->route('profile')->with('success', 'Profile picture updated successfully.');
      }
    }
}
