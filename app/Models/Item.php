<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Added to define Eloquent relationships.
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $table = 'item';

    protected $casts = [
        'price' => 'float',
    ];


    protected $fillable = ['id', 'name', 'price', 'rating', 'fabric', 'brand', ' stock', 'description', 'era', 'color'];
    

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_item', 'id_item', 'id_cart')->withPivot('quantity');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'id_item');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_item');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_item');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'wishlist', 'id_item', 'id_user');
    }

}

?>

