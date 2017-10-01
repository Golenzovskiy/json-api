<?php

namespace App\Http\Controllers\Api\v1;

use App\AggregationRating;
use App\Helpers\Api\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Author;
use App\Post;

class PostController extends Controller
{
    /**
     * Массив сообщений об ошибках
     * @var array
     */
    public $errors = [];

    /**
     * Добавление нового поста
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        if (!Helper::checkRequireFields($this, $request->all(), ['title', 'description', 'login'])) {
            return response()->json(['status' => 'error', 'messages' => $this->errors], 422);
        }
        
        try {
            $post = new Post();
            $author = new Author();

            if ($author = $author->getAuthorByLogin($request->login)) {
                $authorId = $author->id;
            } else {
                $author = new Author();
                $author->login = $request->login;
                $author->save();
                $authorId = $author->id;
            }

            $post->author_id = $authorId;
            $post->title = $request->title;
            $post->description = $request->description;
            $post->ip_address = $request->ip();
            $post->save();
        } catch (\Exception $exception) {
            return response()->json(['status' => 'error', 'messages' => [$exception->getMessage()]], 520);
        }

        return response()->json(['status' => 'success', 'params' => $request->all()], 200);
    }

    public function getPostsByScoreAvg(Request $request)
    {
        if (!Helper::isScoreCorrect($this, $request->score_avg)) {
            return response()->json(['status' => 'error', 'messages' => $this->errors], 400);
        }

        $scoreAvg = $request->score_avg;
        $arrPostIds = AggregationRating::getPostsIdByScoreAvg($scoreAvg);

        if (!Post::getArrPostsById($arrPostIds)->isEmpty()) {
            $key = 'posts';
            $result = Post::getArrPostsById($arrPostIds);
        } else {
            $key = 'message';
            $result = ["Постов с рейтингом $scoreAvg не найдено."];
        }

        return response()->json(['status' => 'success', $key => $result], 200);
    }
}
