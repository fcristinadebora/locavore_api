<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageProduct extends Model
{

  protected $table = 'image_product';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'product_id',
    'image_id'
  ];

  public function image()
  {
    return $this->belongsTo('App\Models\Image', 'image_id', 'id');
  }
}
