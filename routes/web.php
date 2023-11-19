<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CartController;
use App\Http\Controllers\ItemController;

use App\Http\Controllers\HomeController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartItemController;

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

Route::get('/home', [HomeController::class, 'home'])->name('home');

// web.php
Route::get('/next-items/{offset}', [ItemController::class, 'nextItems']);



// // Cards
// Route::controller(CartController::class)->group(function () {
//     Route::get('/cards', 'list')->name('cards');
//     Route::get('/cards/{id}', 'show');
// });

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

Route::controller(CartItemController::class)->group(function () {
    Route::post('/cart/add/{productId}', 'addToCart')->name('cart.add');
});

