<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    //Associate to database
    protected $table = 'cart';

    /**
     * Get the user that owns the cart.
     */
    public function user() 
    {
        return $this->hasOne(User::class);
    }

}