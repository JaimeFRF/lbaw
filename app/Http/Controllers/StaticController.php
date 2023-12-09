<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticController extends Controller
{
    public function about(){
        return view('pages.static.about', [
          'breadcrumbs' => ['Home' => route('home')],
          'current' => 'About Us'
        ]);
      }
    
      public function faq(){
        return view('pages.static.faq', [
          'breadcrumbs' => ['Home' => route('home')],
          'current' => 'Frequently Asked Questions'
        ]);
      }
    
      public function contacts(){
        return view('pages.static.contacts', [
          'breadcrumbs' => ['Home' => route('home')],
          'current' => 'Contacts'
        ]);
      }
}
