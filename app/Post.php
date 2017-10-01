<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function getArrPostsById(array $postIds)
    {
        return DB::table('posts')->select('title', 'description')->whereIn('id', $postIds)->get();
    }
}
