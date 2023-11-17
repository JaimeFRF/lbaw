<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class Tshirt extends Model{

        public $timestamps = false;

        public $primaryKey = 'id_item';

        protected $table = 'tshirt';

        protected $fillable = ['id_item', 'tshirt_type', 'size'];

        public function item(){
            return $this->belongsTo(Item::class, 'id_item');
        }


    }

?>