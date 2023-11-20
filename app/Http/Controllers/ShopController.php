<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ShopController extends Controller
{
    public function shop() {
        $items = Item::all();

        return view('pages.shop', [
            'items' => $items,
        ]);
    }

    public function shopFilter(Request $request, $filter) {
        $request->session()->put('category', $filter);
        $items = Item::join($filter, 'item.id', '=', $filter . '.id_item')->get();

        return view('pages.shop', [
             'items' => $items,
        ]);
    }
}
