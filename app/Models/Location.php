<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class Location extends Model
    {
        public $timestamps = false;

        protected $table = 'location';

        protected $fillable = ['id', 'address', 'city', 'country', 'postal_code', 'description'];

        public function purchases(){
            return $this->hasMany(Purchase::class, 'id_location');
        }
                
        public function users(){
            return $this->hasMany(User::class, 'id_location');
        }
        

    }

?>