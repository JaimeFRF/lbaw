<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home() {
        return view('pages.home'); // Replace 'your.view' with the actual view name.
    }
    
}
