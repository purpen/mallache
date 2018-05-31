<?php

namespace App\Http\Controllers\Api\V1;


use App\Helper\Tools;
use App\Http\Transformer\ChildUserTransformer;
use App\Models\DesignCompanyModel;
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
        // 判断设计公司是否认证
        $designCompany = DesignCompanyModel::where('user_id' , $user_id)->first();
        if(!$designCompany){
            return $this->response->array($this->apiError('您不是设计公司', 404));
        }
        if ($designCompany->verify_status  !== 1) {
            return $this->response->array($this->apiError('该公司没有认证成功', 403));
        }
        //检测随机字符串是否存在，不存在从新创建
        $data['rand_string'] = Redis::get($user_id);
        if($data['rand_string'] !== null){
            return $this->response->array($this->apiSuccess('Success' , 200 , $data));
        }else{
            $urlValue = uniqid($user_id);
            //调用公共方法生成随机字符串
            $url_short = Tools::url_short($urlValue);
            //保存用户id为key的随机字符串
            Redis::set($user_id , $url_short);
            //保存随机字符串为key对应的用户id
            Redis::set($url_short , $user_id);

            //设置用户id为key的有效时间
            Redis::EXPIRE($user_id , 60000);

            //设置随机字符串为key的有效时间
            Redis::EXPIRE($url_short , 60000);

            $data['rand_string'] = Redis::get($user_id);
            return $this->response->array($this->apiSuccess('Success' , 200 , $data));

        }



    }

    /**
     * @api {get} /urlValue 根据string查看用户id
     * @apiVersion 1.0.0
     * @apiName urlKey valueString
     * @apiGroup urlKey
     *
     * @apiParam {string} rand_string 随机字符串
     *
     * @apiSuccessExample 成功响应:
    {
        "data": {
            "id": 1,
            "username": "",
            "phone": "15810295774",
            "status": 0,
            "logo_image": "",
            "realname": "1",    //邀请人姓名
            "child_account": 0,     //0。子账户； 10。主账户
            "company_role": 0,      //0。成员；10。管理员； 20。超级管理员
            "design_company_name": "1", //设计公司全称
            "design_company_abbreviation": "1"  //设计公司简称
        },
        "meta": {
            "message": "Success",
            "status_code": 200
        }
    }
     */
    public function urlValue(Request $request)
    {
        $rand_string = $request->input('rand_string');
        $user_id = Redis::get($rand_string);
        if($user_id == null){
            return $this->response->array($this->apiError('该链接已过期，请联系邀请人' , 403));
        }
        $user = User::where('id' , $user_id)->first();
        //判断子账户的数量
        if($user->child_count >= config('constant.child_count')){
            return $this->response->array($this->apiError('当前只能邀请10个用户' , 403));
        }
        $realName = $user->realname;
        //如果设计公司用户表的真实姓名没有，把设计公司表的联系人姓名更新的用户表
        if($realName == null) {
            $design_company_id = $user->design_company_id;
            $design_company = DesignCompanyModel::where('id' , $design_company_id)->first();
            $user->realname = $design_company->contact_name;
            $user->save();
        }

        return $this->response->item($user, new ChildUserTransformer())->setMeta($this->apiMeta());

    }

}