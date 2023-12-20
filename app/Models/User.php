<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps  = false;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'is_banned',
        'phone',
        'id_cart',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'id_cart');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_user');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'id_user');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'wishlist', 'id_user', 'id_item');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_user');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'id_location');
    }   

}
?>