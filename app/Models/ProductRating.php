<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRating extends Model
{
    
    protected $table = 'product_rating';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'rating_id'
    ];

    public function rating(){
        return $this->belongsTo('App\Models\Rating', 'rating_id', 'id');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }
}