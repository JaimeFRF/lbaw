<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CartController;
use App\Http\Controllers\ItemController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;

use App\Http\Controllers\StaticController;

use App\Http\Controllers\AdminController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EditProfileController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\PurchaseController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::get('/', function () {
    return redirect('/home');
});

//Purchase
Route::post('/purchase', [PurchaseController::class, 'createPurchase'])->name('add_purchase');

Route::get('/home', [HomeController::class, 'home'])->name('home');

//Admin
Route::get('/admin-add-item', [AdminController::class, 'addItem'])->name('addItem');

//Statics
Route::get('/faq', [StaticController::class, 'faq'])->name('faq');
Route::get('/contacts', [StaticController::class, 'contacts'])->name('contacts');
Route::get('/about', [StaticController::class, 'about'])->name('about');

//Shop
Route::get('/shop', [ShopController::class, 'shop'])->name('shop');
Route::post('/shop/{filter}', [ShopController::class, 'shopFilter'])->name('shopFilter');


// Items on home-page
Route::get('/next-items/{offset}', [ItemController::class, 'nextItems']);


//Item
Route::post('/search', [ItemController::class, 'search'])->name('search');
Route::post('/search/filter', [ItemController::class, 'filter'])->name('filter');
Route::post('/search/clearFilters', [ItemController::class, 'clearFilters'])->name('clearFilters');

// Cards
Route::controller(CartController::class)->group(function () {
    Route::get('/cards', 'list')->name('cards');
    Route::get('/cards/{id}', 'show');
});

//wishlist
Route::put('users/wishlist/product/{id_item}', [WishlistController::class, 'add']);
Route::delete('users/wishlist/product/{product_id}', [WishlistController::class, 'delete']);

// API
Route::controller(CartController::class)->group(function () {
    Route::put('/api/cards', 'create');
    Route::delete('/api/cards/{card_id}', 'delete');
});

Route::controller(ItemController::class)->group(function () {
    Route::put('/api/cards/{card_id}', 'create');
    Route::post('/api/item/{id}', 'update');
    Route::delete('/api/item/{id}', 'delete');
    Route::get('/api/item/{id}', 'show');
});


// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

Route::controller(ProfileController::class)->group(function () {
    Route::get('/profile', 'show')->name('profile');  
});

Route::controller(CartController::class)->group(function () {
    Route::get('/cart', 'list')->name('cart');
});

Route::get('/edit-profile', [EditProfileController::class, 'show'])->name('edit_profile');
Route::post('/edit-profile/username', [EditProfileController::class, 'changeUsername'])->name('change_username');
Route::post('/edit-profile/name', [EditProfileController::class, 'changeName'])->name('change_name');
Route::post('/edit-profile/password', [EditProfileController::class, 'changePassword'])->name('change_password');
Route::post('/edit-profile/remove', [EditProfileController::class, 'removeUser'])->name('remove_user');
Route::post('/edit-profile/picture', [EditProfileController::class, 'changePicture'])->name('update_profile_pic');


Route::controller(CartItemController::class)->group(function () {
    Route::post('/cart/add/{productId}', [CartItemController::class , 'addToCart'])->name('cart.add');
    Route::post('/cart/delete/{productId}', 'deleteFromCart')->name('cart.delete');
    Route::post('/cart/remove/{productId}', 'removeFromCart')->name('cart.remove');
    Route::get('/api/cart/count', 'countItemCart')->name('cart.count');
});

