<?php

namespace App\Http\Controllers\Api\v1;

use App\AggregationRating;
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
    protected $errors = [];

    /**
     * Добавление нового поста
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        if (!$this->checkRequireFields($request)) {
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
        if (!$this->isScoreCorrect($request)) {
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

    /**
     * Проверка реквеста на предмет обязательных полей
     * @param Request $request
     * @return bool false, если проверка не прошла
     */
    protected function checkRequireFields(Request $request)
    {
        if (!isset($request->title)) {
            $this->errors[] = 'Не получено поле title';
        }
        if (!isset($request->description)) {
            $this->errors[] = 'Не получено поле description';
        }
        if (!isset($request->login)) {
            $this->errors[] = 'Не получено поле login';
        }
        return (empty($this->errors)) ? true : false;
    }

    protected function isScoreCorrect(Request $request)
    {
        if (!is_numeric($request->score_avg) || $request->score_avg < 1 || $request->score_avg > 5) {
            $this->errors[] = "Установлено недопустимое значение.";
            return false;
        }
        return true;
    }
}
