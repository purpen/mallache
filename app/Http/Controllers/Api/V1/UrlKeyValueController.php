<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Transformer\UserTransformer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class UrlKeyValueController extends BaseController
{
    /**
     * @api {get} /urlKey 根据用户id生成string
     * @apiVersion 1.0.0
     * @apiName urlKey keyString
     * @apiGroup urlKey
     *
     * @apiParam {token} token
     *
     * @apiSuccessExample 成功响应:
        {
            "meta": {
                "message": "Success",
                "status_code": 200
            },
            "data": {
                "value": "15aa1f43bb989f"
            }
        }
     *
     */
    public function urlKey()
    {
        $user_id = $this->auth_user_id;
        $urlValue = uniqid($user_id);
        //保存用户id为key的随机字符串
        Redis::set($user_id , $urlValue);
        //保存随机字符串为key对应的用户id
        Redis::set($urlValue , $user_id);

        //设置用户id为key的有效时间
        Redis::EXPIRE($user_id , 600);

        //设置随机字符串为key的有效时间
        Redis::EXPIRE($urlValue , 600);

        $data['rand_string'] = Redis::get($user_id);
        return $this->response->array($this->apiSuccess('Success' , 200 , $data));

    }

    /**
     * @api {get} /urlValue 根据string查看用户id
     * @apiVersion 1.0.0
     * @apiName urlKey valueString
     * @apiGroup urlKey
     *
     * @apiParam {string} rand_string 随机字符串
     */
    public function urlValue(Request $request)
    {
        $rand_string = $request->input('rand_string');
        $user_id = Redis::get($rand_string);
        if($user_id == null){
            return $this->response->array($this->apiError('该链接已过期，请联系邀请人' , 403));
        }
        $user = User::where('id' , $user_id)->first();

        return $this->response->item($user, new UserTransformer())->setMeta($this->apiMeta());

    }

}