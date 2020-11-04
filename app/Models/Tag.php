<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description'
    ];

    public function products(){
        return $this->hasMany('App\Models\ProductTag', 'tag_id', 'id');
    }

    public function growers(){
        return $this->hasMany('App\Models\TagUserIdentified', 'tag_id', 'id');
    }
}