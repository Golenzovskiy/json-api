<?php

namespace App\Http\Controllers\Api\v1;

use App\AggregationRating;
use App\Helpers\Api\Helper;
use App\Post;
use App\Rating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    public $errors = [];

    public function setRate(Request $request)
    {
        if (!Helper::checkRequireFields($this, $request->all(), ['post_id', 'score'])) {
            return response()->json(['status' => 'error', 'messages' => $this->errors], 422);
        }

        if (Post::find($request->post_id) === null) {
            return response()->json([
                'status' => 'error', 'messages' => ["Пост с $request->post_id не существует."]
            ], 400);
        }

        if (!Helper::isScoreCorrect($this, $request->score)) {
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
}
