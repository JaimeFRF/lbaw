<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{

    public $timestamps  = false;

    protected $table = 'cart';

    protected $fillable = ['id'];

    public function user() 
    {
        return $this->hasOne(User::class, 'id_cart');
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class, 'id_cart');
    }

    public function products()
    {
        return $this->belongsToMany(Item::class, 'cart_item', 'id_cart', 'id_item')->withPivot('quantity');
    }


}

?>