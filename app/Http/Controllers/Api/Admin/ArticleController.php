<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\ArticleTransformer;
use App\Models\Article;
use App\Models\AssetModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new ArticleTransformer())->setMeta($this->apiMeta());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        ]);

        $article = new Article();
        $article->classification_id = $request->input('classification_id');
        $article->title = $request->input('title');
        $article->content = $request->input('content');
        $article->cover_id = $request->input('cover_id') ?? 0;
        if ($article->save()) {
            if($random = $request->input('random')){
                AssetModel::setRandom($article->id, $random);
            }
            return $this->response->array($this->apiSuccess());
        } else {
            return $this->response->array($this->apiError());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        ]);

        $article = Article::find($request->input('id'));
        if (!$article) {
            return $this->response->array($this->apiSuccess('not found', 404));
        }
        $data = $request->only(['title', 'content','classification_id','cover_id']);
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

}
