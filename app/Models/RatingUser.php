<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingUser extends Model
{
    
    protected $table = 'rating_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'rating_id'
    ];

    public function rating(){
        return $this->belongsTo('App\Models\Rating', 'rating_id', 'id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}