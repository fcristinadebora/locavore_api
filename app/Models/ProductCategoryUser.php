<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryUser extends Model
{
    
    protected $table = 'product_category_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'product_category_id'
    ];

    public function productCategory(){
        return $this->belongsTo('App\Models\ProductCategory', 'product_category_id', 'id');
    }
}