<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'user_id',
      'name',
      'email',
      'description'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}