<?php
 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Item;
use App\Models\Cart;

class LoginController extends Controller
{

    /**
     * Display a login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/home');
        } else {
            return view('auth.login');
        }
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->is_banned) {
            return back()->withErrors([
                'email' => 'Your account has been banned.',
            ])->onlyInput('email');
        }
 
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $cart = Session::get('cart', []);
            $userCart = Auth::user()->cart()->first();
            if (!empty($cart)) {
                foreach ($cart as $item) {
                    $itemId = $item['id'];
                    $quantity = $item['quantity'];
                    $itemInfo = Item::find($itemId);
                    $existsItem = $userCart->products()->where('id_item', $itemId)->first();
                    if ($existsItem) {
                        $currentQuantity = $userCart->products()->where('id_item', $itemId)->first()->pivot->quantity;
                        $userCart->products()->updateExistingPivot($itemId, ['quantity' => $currentQuantity + $quantity]);
                    }
                    else {
                        $userCart->products()->attach($itemId, ['quantity' => $quantity]);
                    }
                }    
            }
            Session::forget('cart');
            return redirect()->intended('/home');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log out the user from application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    } 
}
