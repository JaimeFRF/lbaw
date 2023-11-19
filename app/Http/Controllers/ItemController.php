<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Item;
use App\Models\Jacket;
use App\Models\Jeans;
use App\Models\Shirt;
use App\Models\Sneaker;
use App\Models\Tshirt;




class ItemController extends Controller
{
    /**
     * Creates a new item.
     */
    public function create(Request $request, $card_id)
    {
        // Create a blank new item.
        $item = new Item();

        // Set item's card.
        $item->card_id = $card_id;

        // Check if the current user is authorized to create this item.
        $this->authorize('create', $item);

        // Set item details.
        $item->done = false;
        $item->description = $request->input('description');

        // Save the item and return it as JSON.
        $item->save();
        return response()->json($item);
    }

    public function nextItems($offset)
    {
        //Log::info('Offset', ['offset' => $offset]);
        $items = Item::skip($offset)->take(3)->get();
        //Log::info('User of a review', ['items' => $items]);
        return view('partials.item-list', ['items' => $items]);
    }
    /**
     * Updates the state of an individual item.
     */
    public function update(Request $request, $id)
    {
        // Find the item.
        $item = Item::find($id);

        // Check if the current user is authorized to update this item.
        $this->authorize('update', $item);

        // Update the done property of the item.
        $item->done = $request->input('done');

        // Save the item and return it as JSON.
        $item->save();
        return response()->json($item);
    }

    /**
     * Deletes a specific item.
     */
    public function delete(Request $request, $id)
    {
        // Find the item.
        $item = Item::find($id);

        // Check if the current user is authorized to delete this item.
        $this->authorize('delete', $item);

        // Delete the item and return it as JSON.
        $item->delete();
        return response()->json($item);
    }
    
    /**
     * Shows info about a specific item.
     */
    
    public function show($id)
    {
        $item = Item::find($id);
        return view('pages.items.item', ['item' => $item]);
    }

    public function search(Request $request)
    {
        $user_input = $request->input('search');
        Log::info('User input: '.$user_input);

        $keyword1 = strtolower($user_input); 
        $keyword2 = strtoupper($user_input); 
        $keyword3 = ucfirst($user_input);   
        $keyword4 = ucwords($user_input);

        $queryName = Item::where('name', 'like', '%'.$user_input.'%');
        $firstQuery = Item::where('name', 'like', '%'.$keyword1.'%');
        $secondQuery = Item::where('name', 'like', '%'.$keyword2.'%');
        $thirdQuery = Item::where('name', 'like', '%'.$keyword3.'%');
        $fourthQuery = Item::where('name', 'like', '%'.$keyword4.'%');

        $results = $queryName
        ->union($firstQuery)
        ->union($secondQuery)
        ->union($thirdQuery)
        ->union($fourthQuery)
        ->get();

        return view('pages.shop', ['items' => $results]);
    }
    public function filter(Request $request)
    {
        $color = $request->input('color');
        $category = $request->input('category');
        $orderBy = $request->input('orderBy');
        $inStock = $request->input('inStock');
        $price = $request->input('price');

        $rangeMin = 0;
        $rangeMax = 1000000;
        if($price == "0to15"){
            $rangeMax = 15;
        }else if($price == "15to30"){
            $rangeMin = 15;
            $rangeMax = 30;
        }else if($price == "30to50"){
            $rangeMin = 30;
            $rangeMax = 50;
        }else if($price == "50to75"){
            $rangeMin = 50;
            $rangeMax = 75;
        }else if($price == "75to100"){
            $rangeMin = 75;
            $rangeMax = 100;
        }else if($price == "100plus"){
            $rangeMin = 100;
        }


        $helper = "=";
        if($inStock == "1"){
            $helper = ">";
        }


        $table = "price";
        if ($orderBy == "none")
          $table = "id";
        else if ($orderBy == "rating-high-low" || $orderBy == "rating-low-high")
          $table = "rating";
    
        $string = "asc";
        if ($orderBy == "price-high-low" || $orderBy == "rating-high-low")
            $string = "desc";
    
        if($category == "all"){
            if($color == "None"){
                $items = Item::orderBy($table, $string)->where('stock', $helper, 0)->where('price', '>=', $rangeMin)->where('price', '<=', $rangeMax)->get();
            }
            else{
                $items = Item::where('color','=', $color)->where('stock', $helper, 0)->where('price', '>=', $rangeMin)->where('price', '<=', $rangeMax)->orderBy($table, $string)->get();
            }
        }else{
            if($color == "None"){
                $items = Item::join($category, 'item.id', '=', $category . '.id_item')->where('stock', $helper, 0)->where('price', '>=', $rangeMin)->where('price', '<=', $rangeMax)->orderBy($table, $string) 
                ->get();
            }
            else{
                $items = Item::where('color','=', $color)
                ->join($category, 'item.id', '=', $category . '.id_item')->where('stock', $helper, 0)->where('price', '>=', $rangeMin)->where('price', '<=', $rangeMax)->orderBy($table, $string) 
                ->get();
            }
        }
    
    
        return view('pages.shop', ['items' => $items]);
    }    
    
    
}

