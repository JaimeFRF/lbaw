<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class Notification extends Model{
        public $timestamps = false;
        
        protected $table = 'notification';

        protected $fillable = ['id', 'description', 'date', 'notification_type', 'id_user', 'id_purchase', 'id_item'];

        public function user(){
            return $this->belongsTo(User::class, 'id_user');
        }

        public function purchase(){
            return $this->belongsTo(Purchase::class, 'id_purchase');
        }

        public function item(){
            return $this->belongsTo(Item::class, 'id_item');
        }

    }

?>