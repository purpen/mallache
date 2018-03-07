<?php

namespace App\Http\AdminTransformer;

use App\Models\AwardCase;
use League\Fractal\TransformerAbstract;

class AwardCaseListTransformer extends TransformerAbstract
{
    public function transform(AwardCase $awardCase)
    {
        return [
            'id' => $awardCase->id,
            'type' => $awardCase->type,
            'category_id' => $awardCase->category_id,
            'category_value' => $awardCase->category_value,
            'url' => $awardCase->url,
            'tags' => $awardCase->tags,
            'title' => $awardCase->title,
            'user_id' => $awardCase->user_id,
            'cover_id' => $awardCase->cover_id,
            'cover' => $awardCase->cover,
            'summary' => $awardCase->summary,
            'time_at' => $awardCase->time_at,
            'recommended' => $awardCase->recommended,
            'recommended_on' => $awardCase->recommended_on,
            'grade' => $awardCase->grade,
            'status' => $awardCase->status,
            'created_at' => $awardCase->created_at,
            'random' => $awardCase->random,
        ];
    }
}
