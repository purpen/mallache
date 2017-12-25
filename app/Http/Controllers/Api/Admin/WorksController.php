<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Transformer\WorksListTransformer;
use App\Http\Transformer\WorksTransformer;
use App\Models\Works;
use App\Models\AssetModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Tools;

class WorksController extends Controller
{
    /**
     * @api {get} /admin/works/list 列表
     * @apiVersion 1.0.0
     * @apiName works index
     * @apiGroup AdminWorks
     *
     * @apiParam {integer} status 状态 0.全部；-1.禁用；1.启用；
     * @apiParam {integer} published 状态 -1.未发布；0.全部；1.发布；
     * @apiParam {integer} type 类型
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     * "data": [
     * {
     *      "id": 23,
     *      "user_id": 12,
     *      "company_id": 22,
     *      "tags": [],   // 标签
     *      "title": "杯子",
     *      "match_id": 12,   // 所属大赛ID
     *      "summary":  "备注",   // 备注
     *      "images": [],   // 图片列表
     *      "type": 1,  // 类型：1.默认；
     *      "cover": "",  // 封面图
     *      "cover_id": 2,
     *      "view_count": 22,   // 浏览量
     *      "published": 1, // 是否发布：0.否；1.是；
     *      "status": 0,  // 状态：0.禁用；1.启用；
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
        $type = $request->input('type') ? (int)$request->input('type') : 0;
        $status = $request->input('status') ? (int)$request->input('status') : 0;
        $published = $request->input('published') ? (int)$request->input('published') : 0;

        $query = array();
        if ($type) $query['type'] = $type;
        if ($published) $query['published'] = $published;
        if ($status) {
            if ($status === -1) {
                $query['status'] = 0;
            } else {
                $query['status'] = 1;
            }
        }

        $lists = Works::where($query)
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return $this->response->paginator($lists, new WorksListTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /admin/works 详情
     * @apiVersion 1.0.0
     * @apiName works edit
     * @apiGroup AdminWorks
     *
     * @apiParam {string} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     * "data": {
     *      "id": 23,
     *      "user_id": 12,
     *      "company_id": 22,
     *      "tags": [],   // 标签
     *      "title": "杯子",
     *      "match_id": 12,   // 所属大赛ID
     *      "summary":  "备注",   // 备注
     *      "images": [],   // 图片列表
     *      "type": 1,  // 类型：1.默认；
     *      "cover": "",  // 封面图
     *      "cover_id": 2,
     *      "view_count": 22,   // 浏览量
     *      "published": 1, // 是否发布：0.否；1.是；
     *      "status": 0,  // 状态：0.禁用；1.启用；
     * },
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function show(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $works = Works::find($request->input('id'));
        if (!$works) {
            return $this->response->array($this->apiSuccess('not found', 404));
        }

        return $this->response->item($works, new WorksTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /admin/works/{id} 更新
     * @apiVersion 1.0.0
     * @apiName AdminWorks update
     * @apiGroup works
     * @apiParam {string} title 标题
     * @apiParam {string} content 作品介绍
     * @apiParam {integer} type 类型:1.默认;
     * @apiParam {integer} match_id 所属大赛ID
     * @apiParam {string} tags 标签
     * @apiParam {integer} cover_id 封面图ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
     *    "data": {
     *      "id": 23,
     *      "user_id": 12,
     *      "company_id": 22,
     *      "match_id": 33,   // 所属大赛ID
     *      "tags": [],   // 标签
     *      "title": "杯子",
     *      "summary":  "备注",   // 备注
     *      "images": [],   // 图片列表
     *      "type": 1,  // 类型：1.默认；
     *      "cover": "",  // 封面图
     *      "cover_id": 2,
     *      "view_count": 22,   // 浏览量
     *      "published": 1, // 是否发布：0.否；1.是；
     *      "status": 0,  // 状态：0.禁用；1.启用；
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     */
    public function update(Request $request, $id)
    {
        // 验证规则
        $rules = [
            'title' => 'required|max:50',
            'content' => 'max:1000',
            'cover_id' => 'required|integer',
            'match_id' => 'required|integer',
            'type' => 'integer'
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '最多50字符',
            'content.max' => '内容最多1000字符',
            'type.integer' => '类型必须为整型',
            'match_id.required' => '请选择一个大赛',
            'match_id.integer' => '大赛ID为整型',
            'cover_id.required' => '请设置一张封面图',
            'cover_id.integer' => '封面ID为整型',
        ];

        $type = $request->input('type') ? (int)$request->input('type') : 1;
        $published = $request->input('published') ? (int)$request->input('published') : 1;
        $cover_id = $request->input('cover_id') ? (int)$request->input('cover_id') : null;
        $match_id = $request->input('match_id') ? (int)$request->input('match_id') : null;

        $params = array(
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'match_id' => $match_id,
            'type' => $type,
            'published' => $published,
            'status' => 1,
            'cover_id' => $cover_id,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        //检验是否存在该作品
        $works = Works::find($id);
        if (!$works) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        $works->update($params);
        if (!$works) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($works, new WorksTransformer())->setMeta($this->apiMeta());
    }
    /**
     * @api {delete} /admin/works/delete 删除
     * @apiVersion 1.0.0
     * @apiName works delete
     * @apiGroup AdminWorks
     *
     * @apiParam {integer} id
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
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        if(!$works = Works::find($id)){
            return $this->response->array($this->apiError('无法删除', 403));
        }

        $works->delete();

        return $this->response->array($this->apiSuccess('ok', 200));
    }

    /**
     * @api {put} /admin/works/verifyStatus 启用/禁用
     * @apiVersion 1.0.0
     * @apiName classification verifyStatus
     * @apiGroup AdminArticle
     *
     * @apiParam {integer} id
     * @apiParam {integer} status 状态 0.禁用 1.启用
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

        $works = Works::find($request->input('id'));
        if (!$works) {
            return $this->response->array($this->apiSuccess('not found', 404));
        }

        if ($request->input('status')) {
            $works->status = 1;
        } else {
            $works->status = 0;
        }
        $works->save();

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/works/published 发布
     * @apiVersion 1.0.0
     * @apiName works publish
     * @apiGroup AdminWorks
     *
     * @apiParam {integer} id
     * @apiParam {integer} published 是否发布： 0.否； 1.是；
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
    public function published(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'published' => 'required|integer',
        ]);

        $works = Works::find($request->input('id'));
        if (!$works) {
            return $this->response->array($this->apiSuccess('not found', 404));
        }

        if ($request->input('published')) {
            $works->published = 1;
        } else {
            $works->published = 0;
        }
        $works->save();

        return $this->response->array($this->apiSuccess());
    }

}
