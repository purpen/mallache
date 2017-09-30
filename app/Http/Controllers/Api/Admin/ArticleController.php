<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\ArticleListTransformer;
use App\Http\AdminTransformer\ArticleTransformer;
use App\Models\Article;
use App\Models\AssetModel;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    /**
     * @api {get} /admin/article/list 文章列表
     * @apiVersion 1.0.0
     * @apiName classification index
     * @apiGroup AdminArticle
     *
     * @apiParam {integer} status 状态 -1.未发布；0.全部；1.发布；
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
     * "type": 1,                       // 类型：1.文章；2.专题；
     * "topic_url": "",                 // 主题url
     * "user_id": 0,                    // 创建者ID
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
        $status = $request->status;

        switch ($status) {
            case -1:
                $status = 0;
                break;
            case 0:
                $status = null;
                break;
            case 1:
                $status = 1;
                break;
            default:
                $status = 1;
        }


        $query = Article::query();
        if ($classification_id != 0) {
            $query->where('classification_id', (int)$classification_id);
        }
        if ($status != null) {
            $query->where('status', (int)$status);
        }

        $lists = $query
            ->orderBy('recommend', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return $this->response->paginator($lists, new ArticleListTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /admin/article 添加文章
     * @apiVersion 1.0.0
     * @apiName classification store
     * @apiGroup AdminArticle
     *
     * @apiParam {string} title *标题；
     * @apiParam {integer} classification_id 分类ID
     * @apiParam {string} content 内容
     * @apiParam {string} random
     * @apiParam {integer} cover_id  封面图ID
     * @apiParam {integer} type  类型：1.文章；2.专题
     * @apiParam {string}  topic_url 专题url 100
     * @apiParam {array} label 标签
     * @apiParam {string} short_content 文章提要 200
     * @apiParam {string} source_from 文章来源 50
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function store(Request $request)
    {
        //    type	tinyint(4)	no	1	类型：1.文章；2.专题；
        //    topic_url	varchar(100)	yes		专题url
        //    user_id	int(11)	no		创建人ID
        //    label	varchar(100)	yes		标签
        //    short_content	vachar(200)	yes		文章提要
        //    source_from	varchar(50)	yes		文章来源
//        id	int(10)	否
//        title	varchar(50)	否		标题
//        content	varchar(1000)	no		内容
//        classification_id	int(11)	no		分类ID
//        status	tinyint(4)	no	0	状态：0.未发布;1.发布
//        recommend	timestamp	no	null	推荐时间
//        read_amount	int(11)	no	0	阅读数量
        $this->validate($request, [
            'title' => 'required|max:50',
            'content' => 'required|max:1000',
            'classification_id' => 'required|integer',
            'cover_id' => 'integer',
            'type' => 'integer|required',
            'topic_url' => 'max:100',
            'label' => 'array',
            'short_content' => 'max:200',
            'source_from' => 'max:50',
        ]);

        $article = new Article();
        $article->classification_id = $request->input('classification_id');
        $article->title = $request->input('title');
        $article->content = $request->input('content');
        $article->cover_id = $request->input('cover_id') ?? 0;
        $article->type = $request->input('type');
        $article->topic_url = $request->input('topic_url') ?? '';
        $article->user_id = $this->auth_user_id;
        $article->label = $request->input('label') ? implode( ',',$request->input('label')) : '';
        $article->short_content = $request->input('short_content') ?? '';
        $article->source_from = $request->input('source_from') ?? '';

        if ($article->save()) {
            if ($random = $request->input('random')) {
                AssetModel::setRandom($article->id, $random);
            }
            return $this->response->array($this->apiSuccess());
        } else {
            return $this->response->array($this->apiError());
        }

    }

    /**
     * @api {get} /admin/article 文章详情
     * @apiVersion 1.0.0
     * @apiName classification edit
     * @apiGroup AdminArticle
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
     * "status": 0,                     // 状态 0.未发布，1.已发布
     * "recommend": null,               // 推荐时间
     * "read_amount": 1,                // 阅读数量
     * "cover_id": 1,                   // 封面图ID
     * "cover": ''                      // 封面图url
     * "created_at": 1505727190,
     * "type": 1,                       // 类型：1.文章；2.专题；
     * "topic_url": "",                 // 主题url
     * "user_id": 0,                    // 创建者ID
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

    /**
     * @api {put} /admin/article 修改文章
     * @apiVersion 1.0.0
     * @apiName classification update
     * @apiGroup AdminArticle
     *
     * @apiParam {integer} id
     * @apiParam {string} title *标题
     * @apiParam {string} content 内容
     * @apiParam {integer} classification_id 分类ID
     * @apiParam {integer} cover_id  封面图ID
     * @apiParam {integer} type  类型：1.文章；2.专题
     * @apiParam {string}  topic_url 专题url 100
     * @apiParam {array} label 标签
     * @apiParam {string} short_content 文章提要 200
     * @apiParam {string} source_from 文章来源 50
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'title' => 'max:50',
            'content' => 'max:1000',
            'classification_id' => 'integer',
            'type' => 'integer',
            'topic_url' => 'max:100',
            'label' => 'array',
            'short_content' => 'max:200',
            'source_from' => 'max:50',
        ]);

        $article = Article::find($request->input('id'));
        if (!$article) {
            return $this->response->array($this->apiSuccess('not found', 404));
        }
        $data = $request->only(['title', 'content', 'classification_id', 'cover_id', 'type', 'topic_url', 'short_content', 'source_from']);
        $data = array_filter($data,function($v){
            return $v === null ? false : true;
        });
        if ($request->input('label')) {
            $data['label'] = implode($request->input('label'), ',');
        }

        $article->update($data);

        return $this->response->array($this->apiSuccess());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }

    /**
     * @api {put} /admin/article/verifyStatus 文章审核
     * @apiVersion 1.0.0
     * @apiName classification verifyStatus
     * @apiGroup AdminArticle
     *
     * @apiParam {integer} id
     * @apiParam {integer} status 状态 0.未审核 1.已审核
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function verifyStatus(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'status' => 'required|integer',
        ]);

        $article = Article::find($request->input('id'));
        if (!$article) {
            return $this->response->array($this->apiSuccess('not found', 404));
        }

        if ($request->input('status')) {
            $article->status = 1;
        } else {
            $article->status = 0;
        }
        $article->save();

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/article/recommend 文章推荐
     * @apiVersion 1.0.0
     * @apiName classification recommend
     * @apiGroup AdminArticle
     *
     * @apiParam {integer} id
     * @apiParam {integer} status 状态 0.未推荐 1.已推荐
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function recommend(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'status' => 'required|integer',
        ]);

        $article = Article::find($request->input('id'));
        if (!$article) {
            return $this->response->array($this->apiSuccess('not found', 404));
        }

        if ($request->input('status')) {
            $article->recommend = time();
        } else {
            $article->recommend = null;
        }
        $article->save();

        return $this->response->array($this->apiSuccess());
    }

}
