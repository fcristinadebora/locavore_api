<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    
    protected $table = 'product_tag';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'tag_id'
    ];

    public function tag(){
        return $this->belongsTo('App\Models\Tag', 'tag_id', 'id');
    }
}