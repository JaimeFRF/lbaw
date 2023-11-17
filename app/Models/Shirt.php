<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class Shirt extends Model{

        public $timestamps = false;

        public $primaryKey = 'id_item';

        protected $table = 'shirt';        

        protected $fillable = ['id_item', 'shirt_type', 'size'];

        public function item(){
            return $this->belongsTo(Item::class, 'id_item');
        }


    }

?>