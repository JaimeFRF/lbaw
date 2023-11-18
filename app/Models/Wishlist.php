<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class Wishlist extends Model
    {
        public $timestamps = false;
        
        public $primaryKey = 'id_user';

        protected $table = 'wishlist';

        protected $fillable = ['id_user', 'id_item'];

        public function users()
        {
            return $this->belongsTo(User::class);
        }

        public function items()
        {
            return $this->belongsTo(Item::class);
        }

    }


?>