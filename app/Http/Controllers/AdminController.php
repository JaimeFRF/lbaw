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
      return view('pages.admin.viewUsers',['users' => $users]);
    }
    public function viewAdmins(){
      $admins = Admin::orderBy('id')->get();
      return view('pages.admin.viewAdmins',['admins' => $admins]);
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

    public function upgradeAdmin($id, Request $request){
      
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
