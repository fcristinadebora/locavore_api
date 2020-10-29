<?php

namespace App\Models;

use App\Models\User;

class GrowerUser extends User
{   
    protected $table = 'users';

    public static function boot()
    {
        parent::boot();
    }

    public function identificationTags(){
        return $this->hasMany('App\Models\TagUserIdentified', 'user_id', 'id');
    }
}
