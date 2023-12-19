<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;



class HomeController extends Controller
{
    public function home(Request $request) {
        $items = Item::all();
        $purchases = Purchase::all();
        $notifications = Notification::all();

        $request->session()->put('color', "all");
        $request->session()->put('category', "None");
        $request->session()->put('orderBy', "None");
        $request->session()->put('price', "null");
        $request->session()->put('inStock', true); 


        return view('pages.home', [
            'items' => $items,
            'totalItems' => $items->count()
        ]);
    }
    
}
