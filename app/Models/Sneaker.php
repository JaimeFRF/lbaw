<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class Sneaker extends Model{

        public $timestamps = false;

        public $primaryKey = 'id_item';

        protected $table = 'sneaker';

        protected $fillable = ['id_item', 'shoe_size'];

        public function item(){
            return $this->belongsTo(Item::class, 'id_item');
        }


    }

?>