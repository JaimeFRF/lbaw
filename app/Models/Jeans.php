<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class Jeans extends Model{

        public $timestamps = false;

        public $primaryKey = 'id_item';

        protected $table = 'jeans';

        protected $fillable = ['id_item', 'waist_size', 'inseam_size', 'rise_size'];

        public function item(){
            return $this->belongsTo(Item::class, 'id_item');
        }


    }

?>