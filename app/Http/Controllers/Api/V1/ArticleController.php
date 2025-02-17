<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ArticleListTransformer;
use App\Http\Transformer\ArticleTransformer;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends BaseController
{
    /**
     * @api {get} /article/list 文章列表
     * @apiVersion 1.0.0
     * @apiName classification index
     * @apiGroup article
     *
     * @apiParam {integer} classification_id 分类ID 0.全部
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     * "data": [
     * {
     * "id": 1,
     * "title": "修改修改",             // 文章标题
     * "content": "测试修改",           // 文章内容
     * "classification_id": 1,          // 类型id
     * "classification_value": "单元测试1", // 类型名称
     * "status": 1,
     * "recommend": null,               // 推荐时间
     * "read_amount": 3                 // 阅读数量
     * "cover_id": 1,                   // 封面图ID
     * "cover": ''                      // 封面图url
     *  "type": 1,                       // 类型：1.文章；2.专题；
     * "topic_url": "",                 // 主题url
     * "label": [],                     // 文章标签
     * "short_content": "",             // 文章简述
     * "source_from": ""                // 来源
     * },
     * ],
     * "meta": {
     * "message": "Success",
     * "status_code": 200,
     * "pagination": {
     * "total": 2,
     * "count": 2,
     * "per_page": 10,
     * "current_page": 1,
     * "total_pages": 1,
     * "links": []
     * }
     * }
     * }
     */
    public function index(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $classification_id = $request->classification_id;

        $query = Article::query()->where('status', 1);
        if ($classification_id != 0) {
            $query->where('classification_id', (int)$classification_id);
        }

        $lists = $query
            ->orderBy('recommend', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return $this->response->paginator($lists, new ArticleListTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /article 文章详情
     * @apiVersion 1.0.0
     * @apiName classification edit
     * @apiGroup article
     *
     * @apiParam {string} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     * "data": {
     * "id": 1,
     * "title": "这是文章的标题",      // 标题
     * "content": "内容奥这里 ",         // 内容
     * "classification_id": 1,          // 分类ID
     * "recommend": null,               // 推荐时间
     * "read_amount": 1,                // 阅读数量
     * "cover_id": 1,                   // 封面图ID
     * "cover": ''                      // 封面图url
     * "created_at": 1505727190,
     *  "type": 1,                       // 类型：1.文章；2.专题；
     * "topic_url": "",                 // 主题url
     * "label": [],                     // 文章标签
     * "short_content": "",             // 文章简述
     * "source_from": ""                // 来源
     * },
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function edit(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $article = Article::find($request->input('id'));
        if (!$article) {
            return $this->response->array($this->apiSuccess('not found', 404));
        } else {
            $article->read_amount += 1;
            $article->save();
        }

        return $this->response->item($article, new ArticleTransformer())->setMeta($this->apiMeta());
    }
}