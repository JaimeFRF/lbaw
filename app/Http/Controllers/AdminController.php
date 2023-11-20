<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function addItem(){
        return view('pages.admin.addItem');
    }
    public function viewUsers(){
        return view('pages.admin.viewUsers');
    }
    public function viewStock() 
    {
      return view('pages.admin.viewItemsStock');
    }

    public function viewItems() 
    {
      return view('pages.admin.viewItems');
    }

}
