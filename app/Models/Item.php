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

    protected $fillable = ['name', 'price', 'rating', 'fabric', 'brand', ' stock', 'description', 'era', 'color'];
    

    /**
     * Get the card where the item is included.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
