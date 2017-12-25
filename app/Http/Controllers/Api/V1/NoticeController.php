<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Transformer\NoticeTransformer;
use App\Models\AssetModel;
use App\Models\Notice;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoticeController extends BaseController
{

    /**
     * @api {get} /notice 详情
     * @apiVersion 1.0.0
     * @apiName notice noticeShow
     * @apiGroup Notice
     *
     * @apiParam {integer} id ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "data": {
     *          "type": 1,
     *          "title": "名称",
     *          "summary": "简介",
     *          "user_id": 255, // 创建人ID
     *          "content": "内容",
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
     * @api {get} /notice/list 列表
     * @apiVersion 1.0.0
     * @apiName notice noticeList
     * @apiGroup Notice
     *
     * @apiParam {integer} evt 目标人群： 1.需求方；2.设计公司；
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
        $type = $request->input('type') ? (int)$request->input('type') : 1;
        $status = $request->input('status') ? (int)$request->input('status') : 1;
        $evt = $request->input('evt') ? (int)$request->input('evt') : 0;

        $query = array();
        if ($type) $query['type'] = $type;
        if ($status) $query['status'] = $status;
        if ($evt) {
            if ($evt === 1) {
                $query['evt'] = array(0, 1);
            } elseif ($evt === 2) {
                $query['evt'] = array(0, 2);
            }
        }

        $lists = Notice::where($query)
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return $this->response->paginator($lists, new NoticeTransformer)->setMeta($this->apiMeta());
    }

}
