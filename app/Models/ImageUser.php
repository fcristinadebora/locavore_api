<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageUser extends Model
{

  protected $table = 'image_user';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'image_id'
  ];

  public function image()
  {
    return $this->belongsTo('App\Models\Image', 'image_id', 'id');
  }
}
