<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public $timestamps = false;

    protected $table = 'image';

    protected $fillable = ['id', 'filepath', 'id_item'];

    public function items(){
        return $this->belongsTo(Item::class, 'id_item');
    }
  
}

?>