<?php
namespace App\Http\Controllers\Api\V1;

use App\Helper\Tools;
use App\Http\Transformer\MessageTransformer;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;

class MessageController extends BaseController
{
    /**
     * @api {get} /message/getMessageQuantity 获取系统通知数量
     * @apiVersion 1.0.0
     * @apiName message getMessageQuantity
     * @apiGroup Message
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *      "data":{
                "quantity": 10,
                "message": 4,
                "notice": 6,
     *          "design_notice": 0,  // 设计项目管理通知
     *      }
     *  }
     */
    public function getMessageQuantity()
    {
        $tools = new Tools();
        $data = $tools->getMessageQuantity($this->auth_user_id);

        return $this->response->array($this->apiSuccess('Success', 200, $data));
    }

    /**
     * @api {get} /message/getMessageList 获取系统通知列表
     * @apiVersion 1.0.0
     * @apiName message getMessageList
     * @apiGroup Message
     *
     * @apiParam {string} token
     * @apiParam {int} per_page;
     * @apiParam {int} page
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *      "data":[
                {
                "id": 11,
                "type": 1,                                  // 1.系统通知。2.项目通知。3.资金通知
                "title": "",                                // 标题
                "content": "【esisd】需求公司已确认合同",      // 内容
                "target_id": null,                          // 目标ID
                "created_at": 1495104499,
                "status": 1,                                // 状态 0.未读；1.已读
                "is_url": 0                                 // 是否有链接 0：无；1.有；
                },
     *      ]
     *  }
     */
    public function getMessageList(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;

        $lists =Message::where('user_id', $this->auth_user_id)->orderby('id', 'desc')->paginate($per_page);

        return $this->response->paginator($lists, new MessageTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /message/trueRead 新消息确认阅读
     * @apiVersion 1.0.0
     * @apiName message trueRead
     * @apiGroup Message
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
        $id = (int) $request->input('id');
        $tools = new Tools();
        if($tools->haveRead($id)){
            return $this->response->array($this->apiSuccess());
        }else{
            return $this->response->array($this->apiError());
        }


    }

    /**
     * @api {get} /message/getMessageProjectNotice 获取项目通知数量
     * @apiVersion 1.0.0
     * @apiName message getMessageProjectNotice
     * @apiGroup Message
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *      "data":{
     *          "design_notice": 0,  // 设计项目管理通知
     *      }
     *  }
     */
    public function getMessageProjectNotice()
    {
        //初始数据
        $data = [
            'design_notice' => 0,
        ];
        //判断用户ID
        if (!$this->auth_user_id) {
            return $data;
        }
        //查询数据
        $user = User::find($this->auth_user_id);

        if (!$user) {
            return $data;
        }

        if ($user->design_notice_count < 0) {
            $user->design_notice_count = 0;
            $user->save();
        }

        if (isset($user->design_notice_count)) {
            $data['design_notice'] = (int)$user->design_notice_count;
        }
        //成功返回
        return $this->response->array($this->apiSuccess('Success', 200, $data));
    }

    /**
     * @api {put} /message/trueAllRead 全部确认阅读
     * @apiVersion 1.0.0
     * @apiName message trueAllRead
     * @apiGroup Message
     *
     * @apiParam {string} token
     */
    public function trueAllRead()
    {
        $user = User::find($this->auth_user_id);
        if (!$user) {
            return $this->response->array($this->apiError('没有找到用户' , 400));
        }
        $user->message_count = 0;
        $user->notice_count = 0;
        if ($user->save()) {
            return $this->response->array($this->apiSuccess('全部阅读成功', 200));
        }

        return $this->response->array($this->apiError('全部阅读失败' , 412));

    }

}
