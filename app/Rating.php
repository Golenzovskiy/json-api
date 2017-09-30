<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public $timestamps = false;

    public function post()
    {
        return $this->belongsTo('App\Post');
    }
}
