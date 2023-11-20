<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;


class ShopController extends Controller
{
    public function shop() {
        $items = Item::all();

        return view('pages.shop', [
            'items' => $items,
        ]);
    }
}
