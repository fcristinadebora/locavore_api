<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'user_id',
        'product_category_id'
    ];

    public function productCategory(){
        return $this->belongsTo('App\Models\ProductCategory', 'product_category_id', 'id');
    }

    public function grower(){
        return $this->belongsTo('App\Models\GrowerUser', 'user_id', 'id');
    }

    public function tags(){
        return $this->hasMany('App\Models\ProductTag', 'product_id', 'id');
    }

    public function images(){
        return $this->hasMany('App\Models\ImageProduct', 'product_id', 'id')->orderBy('id', 'desc');
    }
}