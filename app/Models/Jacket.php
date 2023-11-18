<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class Jacket extends Model{

        public $timestamps = false;

        public $primaryKey = 'id_item';

        protected $table = 'jacket';

        protected $fillable = ['id_item', 'jacket_type', 'size'];

        public function item(){
            return $this->belongsTo(Item::class, 'id_item');
        }


    }

?>