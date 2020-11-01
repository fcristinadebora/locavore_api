<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model
{
    
    protected $table = 'user_favorites';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'favorite_user_id'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function favoriteUser(){
        return $this->belongsTo('App\Models\GrowerUser', 'user_id', 'id');
    }
}