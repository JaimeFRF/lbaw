<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Item;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Review;
use App\Models\Jacket;
use App\Models\Jeans;
use App\Models\Shirt;
use App\Models\sneakers;
use App\Models\Tshirt;
use App\Models\Purchase;
use App\Models\Location;
use Illuminate\Support\Facades\DB;



class AdminController extends Controller
{   
    public function viewHome(){
        return view('pages.admin.adminHome');
    }
    public function addItem(){
        return view('pages.admin.addItem');
    }
    public function viewUsers(){
      $users = User::orderBy('id')->get();
      return view('pages.admin.viewUsers',['users' => $users, 'breadcrumbs' => ['Admin Home' => route('admin-home')], 'current' => 'Users']);
    }
    public function viewAdmins(){
      $admins = Admin::orderBy('id')->get();
      return view('pages.admin.viewAdmins',['admins' => $admins, 'breadcrumbs' => ['Admin Home' => route('admin-home')], 'current' => 'Admins']);
    }

    public function viewOrders(Request $request)
    {
        $orders = Purchase::get();
        $ordersInfo = array();
        foreach($orders as $order){
            $idLocation = $order->id_location;
            $location = Location::where('id', $idLocation)->get()->first();
            if ($location) {
                $order->location = $location; 
            } else {
                $order->location = null; 
            }
        }
        return view('pages.admin.viewOrders',['orders' => $orders, 'breadcrumbs' => ['Admin Home' => route('admin-home')], 'current' => 'Orders']);
    }

    public function viewItems() 
    {
    $items = DB::table('item')
    ->leftJoin('shirt', 'item.id', '=', 'shirt.id_item')
    ->leftJoin('tshirt', 'item.id', '=', 'tshirt.id_item')
    ->leftJoin('jacket', 'item.id', '=', 'jacket.id_item')
    ->leftJoin('jeans', 'item.id', '=', 'jeans.id_item')
    ->leftJoin('sneakers', 'item.id', '=', 'sneakers.id_item')
    ->select(
        'item.id', 'item.name', 'item.price', 'item.stock', 'item.color', 
        'item.era', 'item.fabric', 'item.description', 'item.brand',
        DB::raw("
            CASE
                WHEN shirt.id_item IS NOT NULL THEN 'Shirt'
                WHEN tshirt.id_item IS NOT NULL THEN 'Tshirt'
                WHEN jacket.id_item IS NOT NULL THEN 'Jacket'
                WHEN jeans.id_item IS NOT NULL THEN 'Jeans'
                WHEN sneakers.id_item IS NOT NULL THEN 'Sneakers'
                ELSE 'Unknown'
            END as category"),
        DB::raw("COALESCE(CAST(shirt.shirt_type AS text), CAST(tshirt.tshirt_type AS text), CAST(jacket.jacket_type AS text), CAST(jeans.jeans_type AS text), CAST(sneakers.sneakers_type AS text)) as type"),
        DB::raw("COALESCE(CAST(shirt.size AS text), CAST(tshirt.size AS text), CAST(jacket.size AS text), CAST(jeans.size AS text), CAST(sneakers.size AS text)) as size")
    )
    ->get();

    return view('pages.admin.viewItems',['items'=> $items, 'breadcrumbs' => ['Admin Home' => route('admin-home')], 'current' => 'Items']);
    }

    public function deleteUser($id, Request $request)
    {
      $user = User::find($id);
      if (!$user) {
          return response()->json(['message' => 'User not found'], 404);
      }
      $user->delete();
      return response()->json(['message' => 'User deleted'], 200);
    }

    public function banUser($id, Request $request){

      $user = User::find($id);
      if (!$user) {
          return response()->json(['message' => 'User not found'], 404);
      }
      $user->is_banned = ($user->is_banned == true ? false : true);
      $user->save();
      return response()->json(['message' => 'User banned'], 200);
    }


    public function updateUser(Request $request, $id)
    {
    $user = User::findOrFail($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $request->validate([
        'email' => 'required|email|unique:users,email,' . $id,
        'username' => 'required|string|max:255|unique:users,username,' . $id,
        'name' => 'nullable|string|max:255',
    ]);

    // -------
    $user->fill($request->only(['name', 'email', 'username']));
    $user->phone = $request->phone;
    $user->save();


    return response()->json([
        'message' => 'User info updated',
        'updatedUserData' => [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone, 
        ]
    ], 200);
}

public function updateItem(Request $request, $id)
{
    $item = Item::findOrFail($id);


    if (!$item) {
        return response()->json(['message' => 'Item not found, wrong id'], 404);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'color' => 'nullable|string|max:255',
        'era' => 'nullable|string|max:255',
        'fabric' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:255',
        'brand' => 'nullable|string|max:255',
        'subcategory' => 'nullable|string|max:255',
    ]);
    
    // Update item attributes
    $item->fill($request->only([
        'name', 'price', 'stock', 'color', 'era', 'fabric', 'description', 'brand', 'subcategory'
    ]));
    

    $item->stock = $request->stock;
    
    // Specific attributes for different item types
    switch ($request->category) {
        case 'Shirt':
            $item->shirt()->update([
                'shirt_type' => $request->type,
                'size' => $request->size,
            ]);
            break;
        case 'Tshirt':
            $item->tshirt()->update([
                'tshirt_type' => $request->type,
                'size' => $request->size,
            ]);
            break;
        case 'Jacket':
            $item->jacket()->update([
                'jacket_type' => $request->type,
                'size' => $request->size,
            ]);
            break;
        case 'Jeans':
            $item->jeans()->update([
                'jeans_type' => $request->type,
                'size' => $request->size,
            ]);
            break;
        case 'Sneakers':
            $item->sneakers()->update([
                'sneakers_type' => $request->type,
                'size' => $request->size,
            ]);
            break;
        default:
            // Handle the 'Unknown' category or any other category
            break;
    }
    
    $item->save();
    
    return response()->json([
        'message' => 'Item info updated',
        'updatedItemData' => [
            'name' => $item->name,
            'price' => $item->price,
            'stock' => $item->stock,
            'color' => $item->color,
            'era' => $item->era,
            'fabric' => $item->fabric,
            'description' => $item->description,
            'brand' => $item->brand,
            'category' => $request->category,
            'type' => $request->type,
            'size' => $request->size,
            'subcategory' => $request->subcategory,
        ]
    ], 200);
    
}



    public function createUser(Request $request){
      $temporaryPassword = Str::random(10);

      $user = new User([
          'name' => $request->input('name'),
          'username' => $request->input('username'),
          'email' => $request->input('email'),
          'phone' => $request->input('phone'),
          'role' => $request->input('role'), 
          'password' => Hash::make($temporaryPassword),
      ]);

      $user->save();

      // You can send an email with the temporary password here using SMTP

      return response()->json(['message' => 'User created successfully', 'user' => $user], 200);
    }

    public function addAdmin(Request $request){
      $temporaryPassword = Str::random(10);

      $admin = new Admin([
          'name' => $request->input('name'),
          'username' => $request->input('username'),
          'email' => $request->input('email'),
          'phone' => $request->input('phone'),
          'role' => $request->input('role'), 
          'password' => Hash::make($temporaryPassword),
      ]);

      $admin->save();

      // You can send an email with the temporary password here using SMTP

      return response()->json(['message' => 'Admin created successfully', 'admin' => $admin], 200);
    }

    public function updateAdmin(Request $request, $id){
      
      $admin = Admin::findOrFail($id);

    if (!$admin) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $request->validate([
        'email' => 'required|email|unique:users,email,' . $id,
        'username' => 'required|string|max:255|unique:users,username,' . $id,
        'name' => 'nullable|string|max:255',
    ]);

    // -------
    $admin->fill($request->only(['name', 'email', 'username']));
    $admin->phone = $request->phone;
    $admin->save();


    return response()->json([
        'message' => 'User info updated',
        'updatedAdminData' => [
            'name' => $admin->name,
            'username' => $admin->username,
            'email' => $admin->email,
            'phone' => $admin->phone, 
        ]
    ], 200);
    }

    public function deleteAdmin(Request $request, $id){
      $admin = Admin::find($id);
      if (!$admin) {
          return response()->json(['message' => 'Admin not found'], 404);
      }
      $admin->delete();
      return response()->json(['message' => 'Admin deleted'], 200);
    }

}
