<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class WishlistController extends Controller
{

    private function getUser(){
        return User::where('id', '=', Auth::id())->first();
    }


    public function add($id_item) {
        if(!Auth::check())
            return redirect(route('login'));
  
        $user = $this->getUser();
    
        $entry = new Wishlist;
    
        $entry->id_user = $user->id;

        $entry->id_item = $id_item;
    
        $entry->save();
    
        return redirect()->back();

    }
}
