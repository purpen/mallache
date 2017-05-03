<?php
namespace App\Http\Controllers\Api\V1;

use App\Helper\Tools;

class MessageController extends BaseController
{
    //获取系统通知数量
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
}