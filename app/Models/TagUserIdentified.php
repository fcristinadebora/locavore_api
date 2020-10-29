<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagUserIdentified extends Model
{
    
    protected $table = 'tag_user_identified';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'tag_id'
    ];

    public function tag(){
        return $this->belongsTo('App\Models\Tag', 'tag_id', 'id');
    }
}