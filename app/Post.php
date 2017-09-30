<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function ratings()
    {
        return $this->hasMany('App\Rating');
    }

    public function author()
    {
        return $this->belongsTo('App\Author');
    }

    public function getPostById($id)
    {
        return $this::find($id);
    }
}
