<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Image;
use App\Models\User;

class ProfileController extends Controller{

    /**
   * @method Displays the edit profile form
   * @param id Id of the User whose profile will be edited
   */
    public function show()
    {
      $user = User::find(Auth::id());
      //$this->authorize('show', $user);
      $profile_picture = Image::where('id_user', $user->id)->first()->filepath;
  
      return view('pages.profile.profile', [
        'user' => $user,
        'profile_picture' => $profile_picture
      ]);
    }
}
