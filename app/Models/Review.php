<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class Review extends Model{
        
        public $timestamps = false;

        protected $table = 'review';

        protected $casts = [
            'rating' => 'float',
        ];

        protected $fillable = ['id', 'description', 'rating', 'up_votes', 'down_votes', 'id_user', 'id_item'];

        public function user(){
            return $this->belongsTo(User::class, 'id_user');
        }

        public function item(){
            return $this->belongsTo(Item::class, 'id_item');
        }
    }
?>