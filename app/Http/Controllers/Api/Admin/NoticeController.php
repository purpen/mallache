<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\Transformer\NoticeTransformer;
use App\Models\AssetModel;
use App\Models\Notice;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoticeController extends BaseController
{
    /**
     * @api {post} /admin/notice 添加
     * @apiVersion 1.0.0
     * @apiName notice noticeStore
     * @apiGroup AdminNotice
     *
     * @apiParam {string} title *标题
     * @apiParam {string} summary 简述
     * @apiParam {text} content 内容
     * @apiParam {string} url 链接
     * @apiParam {integer} evt 目标：0.全部；1.需求方；2.设计公司；
     * @apiParam {integer} cover_id 封面图ID
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
        $rules = [
            'title' => 'required|max:100',
            'content' => 'required',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $all['user_id'] = $this->auth_user_id;
        if (!$notice = Notice::create($all)) {
            return $this->response->array($this->apiError('添加失败', 500));
        }

        if($random = $request->input('random')){
            AssetModel::setRandom($notice->id, $random);
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/notice 更新
     * @apiVersion 1.0.0
     * @apiName notice noticeUpdate
     * @apiGroup AdminNotice
     *
     * @apiParam {integer} id 文章ID
     * @apiParam {string} title *标题
     * @apiParam {string} summary 简述
     * @apiParam {text} content 内容
     * @apiParam {integer} evt 目标：0.全部；1.需求方；2.设计公司；
     * @apiParam {integer} cover_id 封面图ID
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
        $rules = [
            'id' => 'required|integer',
            'title' => 'required|max:100',
            'content' => 'required',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $notice = Notice::find($request->input('id'));
        if (!$notice) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$notice = $notice->update($all)) {
            return $this->response->array($this->apiError('更新失败', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /admin/notice 详情
     * @apiVersion 1.0.0
     * @apiName notice noticeShow
     * @apiGroup AdminNotice
     *
     * @apiParam {integer} id ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "data": {
     *          "type": 1,
     *          "title": "产品名称",
     *          "summary": "产品简介",
     *          "user_id": 255, // 创建人ID
     *          "content": "产品内容",
     *          "url": "www.baidu.com",
     *          "evt": 1,  // 目标人群：0.全部； 1.需求方； 2.设计公司；
     *          "status": 0,
     *          "cover_id": 1,
     *          "cover": null,
     *      },
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function show(Request $request)
    {
        $id = $request->input('id');

        $notice = Notice::find($id);
        if (!$notice) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($notice, new NoticeTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /admin/notice/list 列表
     * @apiVersion 1.0.0
     * @apiName notice noticeList
     * @apiGroup AdminNotice
     *
     * @apiParam {integer} type 类型: 0.全部；
     * @apiParam {integer} status 状态: -1.未发布；0.全部；1.发布；
     * @apiParam {integer} evt 目标人群: -1.全部；1.需求方；2.设计公司；
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
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
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type = $request->input('type') ? (int)$request->input('type') : 0;
        $status = $request->input('status') ? (int)$request->input('status') : 0;
        $evt = $request->input('evt') ? (int)$request->input('evt') : 0;

        $query = array();
        if ($type) $query['type'] = $type;
        if ($status) {
            if ($status === -1) {
                $query['status'] = 0;
            } else {
                $query['status'] = 1;
            }
        }
        if ($evt) {
            if ($evt === -1) {
                $query['evt'] = 0;
            } else {
                $query['evt'] = $evt;
            }
        }

        $lists = Notice::where($query)
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return $this->response->paginator($lists, new NoticeTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /admin/notice/changeStatus 变更状态
     * @apiVersion 1.0.0
     * @apiName notice noticeChangeStatus
     * @apiGroup AdminNotice
     *
     * @apiParam {integer} id ID
     * @apiParam {integer} evt 状态 0.未发布；1.发布；
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
    public function changeStatus(Request $request)
    {
        $id = $request->input("id");
        $status = $request->input("evt");

        $notice = Notice::find($id);
        if (!$notice) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($status) {
            $notice->status = 1;
        } else {
            $notice->status = 0;
        }
        if (!$notice->save()) {
            return $this->response->array($this->apiError('Error', 500));
        } else {
            return $this->response->array($this->apiSuccess());
        }
    }

    /**
     * @api {delete} /admin/notice 删除
     * @apiVersion 1.0.0
     * @apiName notice noticeDelete
     * @apiGroup AdminNotice
     *
     * @apiParam {integer} id ID
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
    public function delete(Request $request)
    {
        $id = (int)$request->input('id');
        Notice::destroy($id);

        return $this->response->array($this->apiSuccess());
    }

}
