<?php
namespace App\Http\Controllers\Api\V1;

use App\Helper\Tools;
use App\Http\Transformer\MessageTransformer;
use App\Models\Message;
use Illuminate\Http\Request;

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
                "quantity": 2;
     *      }
     *  }
     */
    public function getMessageQuantity()
    {
        $tools = new Tools();
        $quantity = $tools->getMessageQuantity($this->auth_user_id);

        return $this->response->array($this->apiSuccess('Success', 200, ['quantity' => $quantity]));
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
     *      "data":{
    "quantity": 2;
     *      }
     *  }
     */
    public function getMessageList(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;

        $lists =Message::where('user_id', $this->auth_user_id)->orderby('id', 'desc')->paginate($per_page);

        return $this->response->paginator($lists, new MessageTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /message/trueRead 新消息确认阅读
     * @apiVersion 1.0.0
     * @apiName message trueRead
     * @apiGroup Message
     *
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
    public function trueRead()
    {
        $tools = new Tools();
        $tools->emptyMessageQuantity($this->auth_user_id);

        return $this->response->array($this->apiSuccess());
    }

}