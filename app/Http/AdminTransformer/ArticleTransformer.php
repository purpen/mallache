<?php
namespace App\Http\AdminTransformer;

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
        //    type	tinyint(4)	no	1	类型：1.文章；2.专题；
        //    topic_url	varchar(100)	yes		专题url
        //    user_id	int(11)	no		创建人ID
        //    label	varchar(100)	yes		标签
        //    short_content	vachar(200)	yes		文章提要
        //    source_from	varchar(50)	yes		文章来源
        return [
            'id' => $article->id,
            'title' => $article->title,
            'content' => $article->content,
            'classification_id' => $article->classification_id,
            'classification_value' => $article->classification->name,
            'status' => $article->status,
            'recommend' => $article->recommend,
            'read_amount' => $article->read_amount,
            'created_at' => $article->created_at,
            'cover_id' => $article->cover_id,
            'cover' => $article->cover,
            'type' => $article->type,
            'topic_url' => $article->topic_url,
            'user_id' => $article->user_id,
            'label' => $article->label,
            'short_content' => $article->short_content,
            'source_from' => $article->source_from,
        ];
    }
}