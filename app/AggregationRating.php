<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AggregationRating extends Model
{
    public $timestamps = false;

    public function reCalcScoreAvg(int $postId)
    {
        $rating = new Rating();
        $postRatings = $rating->where('post_id', $postId)->get();
        $count = count($postRatings);

        $sum = 0;
        foreach ($postRatings as $rating) {
            $sum += $rating->score;
        }
        $scoreAvg = $sum / $count;

        if ($aggregation = $this->where('post_id', $postId)->first()) {
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
}
