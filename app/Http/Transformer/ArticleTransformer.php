<?php
namespace App\Http\Transformer;

use App\Models\Article;
use League\Fractal\TransformerAbstract;

class ArticleTransformer extends TransformerAbstract
{
    public function transform(Article $article)
    {
        //        id	int(10)	否
        //        title	varchar(50)	否		标题
        //        content	varchar(1000)	no		内容
        //        classification_id	int(11)	no		分类ID
        //        status	tinyint(4)	no	0	状态：0.未发布;1.发布
        //        recommend	timestamp	no	null	推荐时间
        //        read_amount	int(11)	no	0	阅读数量
//        return $article->toArray();
        return [
            'id' => $article->id,
            'title' => $article->title,
            'content' => $article->content,
            'classification_id' => $article->classification_id,
            'classification_value' => $article->classification->name,
            'status' => $article->status,
            'recommend' => $article->recommend,
            'read_amount' => $article->read_amount,
        ];
    }
}