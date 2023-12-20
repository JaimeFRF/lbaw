<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Item;
use App\Models\Review;
use App\Models\Jacket;
use App\Models\Jeans;
use App\Models\Shirt;
use App\Models\Sneakers;
use App\Models\Tshirt;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{

    public function create(Request $request, $card_id)
    {
        $item = new Item();

        $item->card_id = $card_id;

        $this->authorize('create', $item);

        $item->done = false;
        $item->description = $request->input('description');

        $item->save();
        return response()->json($item);
    }

    public function nextItems($offset)
    {
        $items = Item::skip($offset)->take(3)->get();
        return view('partials.item-list', ['items' => $items]);
    }

    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        $this->authorize('update', $item);

        $item->done = $request->input('done');

        $item->save();
        return response()->json($item);
    }

    public function removeStock(Request $request, $id)
    {
        $item = Item::find($id);

        $item->stock = 0;
        $item->save();

        return response()->json($item);
    }
    

    
    public function show($id)
    {
        $item = Item::find($id);
        $itemReviews = $item->reviews()->get();
        
        $shirt = Shirt::find($id);
        $tshirt = Tshirt::find($id);
        $jacket = Jacket::find($id);
        $jeans = Jeans::find($id);
        $sneakers = Sneakers::find($id);
        
        $size = $shirt->size ?? $tshirt->size ?? $jacket->size ?? $jeans->size ?? $sneakers->size ?? null;
        
        $category = null;
        if ($shirt) {
            $category = 'shirt';
        } elseif ($tshirt) {
            $category = 'tshirt';
        } elseif ($jacket) {
            $category = 'jacket';
        } elseif ($jeans) {
            $category = 'jeans';
        } elseif ($sneakers) {
            $category = 'sneakers';
        }


        if(!Auth::check()){
            $userReview = null;
            $reviews = Review::where('id_item', $id)->get();
        }else{
            $userReview = Review::where('id_item', $id)->where('id_user', Auth::id())->get()->first();
            $otherReviews = Review::where('id_item', $id)
            ->where(function ($query) {
                $query->where('id_user', '<>', Auth::id())
                      ->orWhereNull('id_user');
            })
            ->get();           
            $reviews = collect([$userReview])->concat($otherReviews);
        }

        // foreach ($reviews as $review) {
        //     if ($review != null ) {
        //         Log::info('reviw', ['reviw' => $review]);
        //     } 
        //     else {
        //         Log::info('no user');
        //     }
        // }


        $userHasNotPurchasedItem = false;
        if(Auth::check()){
            $purchases = Auth::user()->purchases;
            foreach($purchases as $purchase){
                $cart = $purchase->cart;
                foreach($cart->products as $cartItem){
                    if($cartItem->id == $id){
                        $userHasNotPurchasedItem = true;
                        break 2;
                    }
                }
            }
        }


        return view('pages.items.item', [
            'size' => $size, 
            'item' => $item, 
            'review' => $userReview, 
            'itemReviews' => $reviews, 
            'userHasNotPurchasedItem' => $userHasNotPurchasedItem,
            'breadcrumbs' => [
                'Home' => route('home'),
                'Shop' => route('shop'),
                ucfirst($category) => route('shopFilter', ['filter' => $category])
            ],
            'current' => $item->name
        ]);
    }

    public function getImages(Request $request, $id){
        $images = Image::where('id_item', $id)->get();
        Log::info($images);
        return response()->json($images);
    }

    public function search(Request $request)
    {
        $user_input = $request->input('search');    
        $results = Item::whereRaw("tsvectors @@ plainto_tsquery('english', ?)", [$user_input])
            ->orWhere('name', 'like', '%'.$user_input.'%')
            ->paginate(6);
        $results->appends(['search' => $user_input]);
    
        return view('pages.shop', ['items' => $results, 'breadcrumbs' => ['Home' => route('home')], 'current' => 'Search']);
    }

    public function filter(Request $request)
    {
        $color = $request->input('color');
        $category = $request->input('category');
        
        if($category == "sneakers"){
            $shoe_size_string = $request->input('shoeSizes');
            $shoe_sizes = explode(',', $shoe_size_string);
        }

        $subCategory = $request->input('subcategorySelect');
        $orderBy = $request->input('orderBy');
        $inStock = $request->input('inStock');
        $price = $request->input('price');


        $request->session()->put('color', $color);
        $request->session()->put('category', $category);
        $request->session()->put('orderBy', $orderBy);
        $request->session()->put('inStock', $inStock);
        $request->session()->put('price', $price);


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
                $items = Item::orderBy($table, $string)->where('stock', $helper, 0)->where('price', '>=', $rangeMin)->where('price', '<=', $rangeMax)->paginate(6);
            }
            else{
                $items = Item::where('color','=', $color)->where('stock', $helper, 0)->where('price', '>=', $rangeMin)->where('price', '<=', $rangeMax)->orderBy($table, $string)->paginate(6);
            }
        }
        else if($category == "sneakers"){
            if($color == "None"){
                $items = Item::join($category, function($join) use ($category, $shoe_sizes, $shoe_size_string) {
                    $join->on('item.id', '=', $category . '.id_item');
                    if ($shoe_size_string !== null) {
                        $join->whereIn($category . '.size', $shoe_sizes);
                    }
                })
                ->where('stock', $helper, 0)
                ->where('price', '>=', $rangeMin)
                ->where('price', '<=', $rangeMax)
                ->orderBy($table, $string)->paginate(6);
            }
            else{
                $items = Item::join($category, function($join) use ($category, $shoe_sizes, $color, $shoe_size_string) {
                    $join->on('item.id', '=', $category . '.id_item')
                         ->where('color', '=', $color);
                    if ($shoe_size_string !== null) {
                        $join->whereIn($category . '.size', $shoe_sizes);
                    }
                })
                ->where('stock', $helper, 0)
                ->where('price', '>=', $rangeMin)
                ->where('price', '<=', $rangeMax)
                ->orderBy($table, $string)->paginate(6);
            }
        }
        else{
            if($subCategory != "None"){
                if($color == "None"){
                    $items = Item::join($category, function($join) use ($category, $subCategory) {
                        $join->on('item.id', '=', $category . '.id_item')
                             ->where($category.'_type', '=', $subCategory);
                        })
                        ->where('stock', $helper, 0)
                        ->where('price', '>=', $rangeMin)
                        ->where('price', '<=', $rangeMax)
                        ->orderBy($table, $string)->paginate(6);
                }
                else{
                    $items = Item::join($category, function($join) use ($category, $subCategory) {
                        $join->on('item.id', '=', $category . '.id_item')
                             ->where($category.'_type', '=', $subCategory);
                        })
                        ->where('stock', $helper, 0)
                        ->where('price', '>=', $rangeMin)
                        ->where('price', '<=', $rangeMax)
                        ->where('color', '=', $color) 
                        ->orderBy($table, $string)->paginate(6);
                }   
            }
            else{
                if($color == "None"){
                    $items = Item::join($category, 'item.id', '=', $category . '.id_item')
                        ->where('stock', $helper, 0)
                        ->where('price', '>=', $rangeMin)
                        ->where('price', '<=', $rangeMax)
                        ->orderBy($table, $string)->paginate(6);
                }
                else{
                    $items = Item::join($category, 'item.id', '=', $category . '.id_item')
                        ->where('stock', $helper, 0)
                        ->where('price', '>=', $rangeMin)
                        ->where('price', '<=', $rangeMax)
                        ->where('color', '=', $color) 
                        ->orderBy($table, $string)->paginate(6);
                }
            }
        }

        $items->appends([
            'color' => $color,
            'category' => $category,
            'shoeSizes' => $request->input('shoeSizes'),
            'subcategorySelect' => $subCategory,
            'orderBy' => $orderBy,
            'inStock' => $inStock,
            'price' => $price,
        ]);

        return view('pages.shop', ['items' => $items, 'breadcrumbs' => ['Home' => route('home')], 'current' => ucfirst($category)]);
    }    

    public function clearFilters(Request $request)
    {
        $request->session()->put('color', "all");
        $request->session()->put('category', "None");
        $request->session()->put('orderBy', "None");
        $request->session()->put('price', "null");
        $request->session()->put('inStock', true); 

        $items = Item::paginate(6);
        return view('pages.shop', ['items' => $items, 'breadcrumbs' => ['Home' => route('home')], 'current' => 'All']);
    }

    public function shop() {
        $items = Item::paginate(6); 

        return view('pages.shop', [
            'breadcrumbs' => ['Home' => route('home')],
            'current' => 'Shop',
            'items' => $items,
        ]);
    }

    public function shopFilter(Request $request, $filter) {
        $request->session()->put('category', $filter);
        $items = Item::join($filter, 'item.id', '=', $filter . '.id_item')->paginate(6);

        return view('pages.shop', [
             'items' => $items,
             'breadcrumbs' => ['Home' => route('home')],'current' => ucfirst($filter)
        ]);
    }

    public function getSubcategories($category) {
        $category = ucfirst($category) . 'Type';
        $query = "SELECT unnest(enum_range(NULL::$category))";
        $result = DB::select($query);
        
        return response()->json(array_column($result, 'unnest'));
    }

    public function addItem(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'subCategory' => 'required|string',
            'size' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'photos' => 'sometimes|array',
            'photos.*' => 'sometimes|file|image',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $item = new Item();
        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->stock = $request->stock;
        $item->color = $request->color;
        $item->era = $request->era;
        $item->fabric = $request->fabric;
        $item->brand = $request->brand;
        $item->save();

        $categoryClassName = $this->getCategoryModelName($request->input('category'));
        if (!$categoryClassName) {
            return response()->json(['message' => 'Invalid category'], 422);
        }
    
        $category = new $categoryClassName();
        $category->id_item = $item->id;
        $category->size = $request->input('size');
        $category->{$request->category . '_type'} = $request->input('subCategory');
        $category->save();

        if ($request->has('photos')) {
            foreach ($request->file('photos') as $photo) {
                $extension = $photo->getClientOriginalExtension();
                
                $filename = uniqid() . '.' . $extension;
                
                if (Storage::disk('public')->exists('images/' . $filename)) {
                    Storage::disk('public')->delete($filename);
                }
        
                $path = $photo->storeAs('images', $filename, 'public');
                
                $newImage = new Image;
                $newImage->id_item = $item->id;
                $newImage->filepath = 'storage/images/' . $filename;
                $newImage->save();
            }
        }

        return response()->json(['message' => 'Item added successfully', 'item' => $item], 200);
    }

    public function deleteItemImage(Request $request){

        $image = Image::find($request->imageId);
        $image->delete();
        return response()->json(['message' => 'Image deleted successfully'], 200);
    }

    private function getCategoryModelName($category)
    {
        $allowedCategories = ['Tshirt', 'Shirt', 'Jacket', 'Jeans', 'Sneakers'];
        $category = ucfirst(strtolower($category));

        if (in_array($category, $allowedCategories)) {
            return "App\\Models\\" . $category;
        }

        return null;
    }

    
    
}

