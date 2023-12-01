<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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



}
