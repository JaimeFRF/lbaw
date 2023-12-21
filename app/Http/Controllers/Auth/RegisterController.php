<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Log;


class RegisterController extends Controller
{

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {

        $request->validate([
            'username' => 'required|string|max:250|unique:users',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();
        
        $userCart = Auth::user()->cart()->first();
        $cart = Session::get('cart', []);
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $itemId = $item['id'];
                $quantity = $item['quantity'];
                $itemInfo = Item::find($itemId);
                $userCart->products()->attach($itemId, ['quantity' => $quantity]); 
            }    
        }
        Session::forget('cart');

        return redirect()->route('home')
            ->withSuccess('You have successfully registered & logged in!');
    }
}
