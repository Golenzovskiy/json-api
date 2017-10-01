<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AggregationRating extends Model
{
    public $timestamps = false;

    public static function calcScoreAvgPost(int $postId)
    {
        $rating = new Rating();
        $postRatings = $rating->where('post_id', $postId)->get();
        $count = count($postRatings);

        $sum = 0;
        foreach ($postRatings as $rating) {
            $sum += $rating->score;
        }
        $scoreAvg = $sum / $count;

        if ($aggregation = self::where('post_id', $postId)->first()) {
            $aggregation->score_avg = $scoreAvg;
            $aggregation->save();
        } else {
            $aggregation = new AggregationRating();
            $aggregation->post_id = $postId;
            $aggregation->score_avg = $scoreAvg;
            $aggregation->save();
        }
        return $aggregation->score_avg;
    }

    public static function calcScoreAvgAllPosts()
    {
        $collection = DB::table('ratings')->select('post_id')->groupBy('post_id')->get();
        foreach ($collection as $rating) {
            if (isset($rating->post_id)) self::calcScoreAvgPost($rating->post_id);
        }
    }

    public static function getPostsIdByScoreAvg($scoreAvg): array
    {
        $arrPostIds = DB::table('aggregation_ratings')
            ->where('score_avg', $scoreAvg)
            ->pluck('post_id')
            ->toArray();
        return $arrPostIds;
    }
}
