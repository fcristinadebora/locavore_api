<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'street',
        'number',
        'district',
        'city',
        'state',
        'country',
        'complement',
        'lat',
        'long',
        'name',
        'postal_code',
        'user_id',
        'is_main'
    ];

    public function contacts(){
        return $this->hasMany('App\Models\Contact', 'address_id', 'id');
    }
}