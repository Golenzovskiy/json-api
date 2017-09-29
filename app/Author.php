<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    public $timestamps = false;
    
    public function posts()
    {
        return $this->hasMany('App\Post');
    }
    
    public function getAuthorByLogin($login)
    {
        return $this::where('login', $login)->first();
    }
}
