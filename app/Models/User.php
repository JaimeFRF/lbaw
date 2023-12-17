<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Added to define Eloquent relationships.
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'is_banned',
        'phone',
        'id_cart',
        //'id_location',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the carts for a user.
     */
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