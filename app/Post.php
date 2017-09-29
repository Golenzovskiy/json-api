<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function ratings()
    {
        return $this->hasMany('App\Rating');
    }
}
