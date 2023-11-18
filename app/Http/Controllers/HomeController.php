<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Log;



class HomeController extends Controller
{
    public function home() {
        $items = Item::all();
       // Log::info('User of a review', ['items' => $items]);

        return view('pages.home', [
            'items' => $items,
            'totalItems' => $items->count()
        ]);
    }
    
}
