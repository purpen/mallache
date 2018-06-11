<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignNoticeTransformer;
use App\Models\DesignNotice;
use Illuminate\Http\Request;

class DesignNoticeController extends BaseController
{
    /**
     * @api {get} /designNotice/lists 设计工具--消息通知列表
     * @apiVersion 1.0.0
     * @apiName designNotice lists
     * @apiGroup designNotice
     *
     * @apiParam {int} par_page 页面条数
     * @apiParam {int} page 页数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "data": [
     *          {
     *              "id": 1,
     *              "is_read": 0,
     *              "user_id": 6,
     *              "operation_log_id": 9,
     *              "operation_log": {
     *                  "type": 1  // 模块类型 1.项目 2.网盘
     *                  "model_id": 1 // 模块ID （与type配合使用）
     *                  "action_type": 1, // 1.创建主任务 2.创建子任务 3.更改任务名称 4.更改任务备注 5.更改任务优先级 6.父任务重做 7.父任务完成 8.子任务重做 9.子任务完成 10.更新了截至时间
     *                  "target_type": 1, // 目标类型 1.任务 2.
     *                  "title": "18629493333 创建了任务",
     *                  "content": "测试动态6",
     *                  "created_at": 1524911571
     *              }
     *          }
     *      ],
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200,
     *          "pagination": {
     *              "total": 1,
     *              "count": 1,
     *              "per_page": 10,
     *              "current_page": 1,
     *              "total_pages": 1,
     *              "links": []
     *          }
     *      }
     * }
     */
    public function lists(Request $request)
    {
        $par_page = $request->input('par_page') ?? $this->per_page;

        $user_id = $this->auth_user_id;

        $list = DesignNotice::where('user_id', $user_id)
            ->orderBy('id', 'desc')
            ->paginate($par_page);

        return $this->response->paginator($list, new DesignNoticeTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /designNotice/trueRead 设计工具--消息确认阅读
     * @apiVersion 1.0.0
     * @apiName designNotice trueRead
     * @apiGroup designNotice
     *
     * @apiParam {int} id 消息ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function trueRead(Request $request)
    {
        $id = $request->input('id');

        $design_notice = DesignNotice::find($id);
        if (!$design_notice || $this->auth_user_id != $design_notice->user_id) {
            return $this->response->array($this->apiSuccess());
        }

        $design_notice->is_read = 1;
        $design_notice->save();

        // 设计通知数量减少
        $user = $this->auth_user;
        $user->designNoticeCount();
//        $user->decrement('design_notice_count');


        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {delete} /designNotice/delete 设计工具--消息删除
     * @apiVersion 1.0.0
     * @apiName designNotice delete
     * @apiGroup designNotice
     *
     * @apiParam {int} id 消息ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');

        $design_notice = DesignNotice::find($id);
        if (!$design_notice || $this->auth_user_id != $design_notice->user_id) {
            return $this->response->array($this->apiSuccess());
        }

        // 设计通知数量减少
        $user = $this->auth_user;
//        $user->decrement('design_notice_count');

        $user->designNoticeCount();

        $design_notice->delete();

        return $this->response->array($this->apiSuccess());
    }
}