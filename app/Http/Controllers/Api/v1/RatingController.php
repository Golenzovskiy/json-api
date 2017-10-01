<?php

namespace App\Http\Controllers\Api\v1;

use App\AggregationRating;
use App\Post;
use App\Rating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    protected $errors = [];

    public function setRate(Request $request)
    {
        if (!$this->checkRequireFields($request)) {
            return response()->json(['status' => 'error', 'messages' => $this->errors], 422);
        }

        if (!$this->isPostExist($request)) {
            return response()->json(['status' => 'error', 'messages' => $this->errors], 400);
        }

        if (!$this->isScoreCorrect($request)) {
            return response()->json(['status' => 'error', 'messages' => $this->errors], 400);
        }

        try {
            $rating = new Rating();
            $rating->post_id = $request->post_id;
            $rating->score = $request->score;
            $rating->save();

            $scoreAvg = AggregationRating::calcScoreAvgPost($request->post_id);
        } catch (\Exception $exception) {
            return response()->json(['status' => 'error', 'messages' => [$exception->getMessage()]], 520);
        }

        return response()->json(['status' => 'success', 'post_score_avg' => $scoreAvg], 200);
    }

    protected function checkRequireFields(Request $request)
    {
        if (!isset($request->post_id)) {
            $this->errors[] = 'Не получено поле post_id';
        }
        if (!is_numeric($request->post_id) || $request->post_id < 1) {
            $this->errors[] = 'Некорректный post_id.';
        }
        if (!isset($request->score)) {
            $this->errors[] = 'Не получено поле score';
        }
        return (empty($this->errors)) ? true : false;
    }

    protected function isPostExist(Request $request)
    {
        $post = new Post();
        if ($post->getPostById($request->post_id) === null) {
            $this->errors[] = "Пост с $request->post_id не существует.";
            return false;
        }
        return true;
    }

    protected function isScoreCorrect(Request $request)
    {
        if (!is_numeric($request->score) || $request->score < 1 || $request->score > 5) {
            $this->errors[] = "Установлено недопустимое значение.";
            return false;
        }
        return true;
    }
}
