<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticController extends Controller
{
    public function about(){
        return view('pages.static.about');
      }
    
      public function faq(){
        return view('pages.static.faq');
      }
    
      public function contacts(){
        return view('pages.static.contacts');
      }
}
