<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class Purchase extends Model
    {
        public $timestamps = false;

        protected $table = 'purchase';

        protected $fillable = ['id', 'price', 'purchase_date', 'delivery_date', 'purchase_status', 'payment_method', 'id_user', 'id_location', 'id_cart'];

        public function user(){
            return $this->belongsTo(User::class, 'id_user');
        }

        public function location(){
            return $this->belongsTo(Location::class, 'id_location');
        }

        public function cart(){
            return $this->belongsTo(Cart::class, 'id_cart');
        }

        public function notifications(){
            return $this->hasMany(Notification::class, 'id_purchase');
        }


    }

?>