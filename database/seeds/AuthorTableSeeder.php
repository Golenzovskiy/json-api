<?php

use Illuminate\Database\Seeder;
use App\Author;
use App\Post;

class AuthorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Author::class, 100)->create()->each(function ($author) {
            for ($i = 0, $s = rand(10, 1000); $i <= $s; $i++) {
                $author->posts()->save(factory(Post::class)->make());
            }
        });
    }
}
