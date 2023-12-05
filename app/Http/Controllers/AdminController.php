<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Item;
use Illuminate\Support\Facades\Log;


class AdminController extends Controller
{   
    public function viewHome(){
        return view('pages.admin.adminHome');
    }
    public function addItem(){
        return view('pages.admin.addItem');
    }
    public function viewUsers(){
      $users = User::all();
      return view('pages.admin.viewUsers',['users' => $users]);
    }
    public function viewStock() 
    {
      return view('pages.admin.viewItemsStock');
    }

    public function viewItems() 
    {
      $items = Item::all();      
      return view('pages.admin.viewItems',['items'=> $items]);
    }

    public function deleteUser($id, Request $request)
    {
      // if(!Auth::check()) return response()->json(['error' => 'Unauthenticated.'], 401);

      Log::info($id);
      $user = User::find($id);
      Log::info($user);
      if (!$user) {
          return response()->json(['message' => 'User not found'], 404);
      }
      $user->delete();
      Log::info('user removed');
        return response()->json(['message' => 'User deleted'], 200);
    }

    public function banUser($id, Request $request){

      $user = User::find($id);
      if (!$user) {
          return response()->json(['message' => 'User not found'], 404);
      }
      $user->is_banned = true;
      $user->save();
      return response()->json(['message' => 'User banned'], 200);
    }

    public function upgradeAdmin($id, Request $request){
      
    }




}
