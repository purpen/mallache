<?php

namespace App\Http\Transformer;

use App\Models\TrendReports;
use League\Fractal\TransformerAbstract;

class TrendReportsTransformer extends TransformerAbstract
{
    public function transform(TrendReports $trendReports)
    {
        return [
            'id' => $trendReports->id,
            'title' => $trendReports->title,
            'cover_id' => $trendReports->cover_id,
            'cover' => $trendReports->cover,
            'image' => $trendReports->image,
            'created_at' => $trendReports->created_at,
            'verify_status' => $trendReports->verify_status,
            'pdf_id' => $trendReports->pdf_id,
            'status' => $trendReports->status,
            'summary' => $trendReports->summary,
            'hits' => $trendReports->hits,
            'tag' => $trendReports->tag ? explode(',' , $trendReports->tag) : [],
            'user_id' => $trendReports->user_id,
        ];
    }
}