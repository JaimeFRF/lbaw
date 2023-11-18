<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    public $timestamps = false;
    
    //Relation primary key
    public $primaryKey = 'id_cart';

    protected $table = 'cart_item';

    protected $fillable = ['id_cart', 'id_item', 'quantity'];


    public function carts(){
        return $this->belongsTo(Cart::class);
    }

    public function items(){
        return $this->belongsTo(Item::class);
    }
}


?>