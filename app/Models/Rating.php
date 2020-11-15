<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'ratings';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'rating',
      'description',
      'rated_by',
    ];

    public function rater(){
      return $this->belongsTo('App\Models\User', 'rated_by', 'id');
    }

    public function productRating(){
      return $this->hasOne('App\Models\ProductRating', 'rating_id', 'id');
    }

    public function ratingUser(){
      return $this->hasOne('App\Models\RatingUser', 'rating_id', 'id');
    }
}