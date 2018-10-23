<?php

namespace App\Http\Controllers\Api\Wx;

use App\Http\WxTransformer\SmallItemTransformer;
use App\Jobs\SendOneSms;
use App\Models\DesignCompanyModel;
use App\Models\SmallItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SmallDemandController extends BaseController
{

    /**
     * @api {post} /wechat/demand/nameRelease 小程序创建项目
     * @apiVersion 1.0.0
     * @apiName wechatSmallDemand release
     * @apiGroup wechatDemandType
     *
     * @apiParam {string} item_name 项目名称
     */
    public function release(Request $request)
    {
        $item_name = $request->input('item_name');
        if(empty($item_name)){
            return $this->response->array($this->apiError('项目名称不能为空', 412));
        }
        $smallItem = new SmallItem();
        $smallItem->item_name = $item_name;
        if($smallItem->save()){
            return $this->response->item($smallItem, new SmallItemTransformer())->setMeta($this->apiMeta());
        }
        return $this->response->array($this->apiError('发布项目失败', 412));
    }

    /**
     * @api {put} /wechat/demand/smallUpdate 添加联系人信息
     * @apiVersion 1.0.0
     * @apiName wechatSmallDemand update
     * @apiGroup wechatDemandType
     *
     * @apiParam {integer} id 项目ID
     * @apiParam {string} user_name 联系人
     * @apiParam {string} phone 手机号
     * @apiParam {string} sms_code 验证码
     *
     */
    public function update(Request $request)
    {
        $id = (int)$request->input('id');
        $all['user_name'] = $request->input('user_name');
        $all['phone'] = $request->input('phone');
        //验证手机验证码
        $key = 'sms_code:' . strval($all['phone']);
        $sms_code_value = Cache::get($key);
        if (intval($request->input('sms_code')) !== intval($sms_code_value)) {
            return $this->response->array($this->apiError('验证码错误', 412));
        } else {
            Cache::forget($key);
        }
        $smallItem = SmallItem::find($id);
        if(!$smallItem){
            return $this->response->array($this->apiError('没有找到该项目', 404));
        }
        if($smallItem->update($all)){
            $text = '【太火鸟铟果】小程序项目获客。设计需求名称：'.$smallItem->item_name.'；客户姓名：'.$request->input('user_name').'；联系方式：'.$request->input('phone');
//            $this->dispatch(new SendOneSms(config('constant.new_item_send_phone') , $text));
            $this->dispatch(new SendOneSms(15810295774 , $text));
            return $this->response->item($smallItem, new SmallItemTransformer())->setMeta($this->apiMeta());
        }
        return $this->response->array($this->apiError('更改项目失败', 412));
    }

    /**
     * @api {post} /wechat/demand/designAdd 设计公司客户
     * @apiVersion 1.0.0
     * @apiName wechatSmallDemand designAdd
     * @apiGroup wechatDemandType
     *
     * @apiParam {integer} design_company_id 设计公司ID
     * @apiParam {string} user_name 联系人
     * @apiParam {string} phone 手机号
     * @apiParam {string} sms_code 验证码
     */
    public function designAdd(Request $request)
    {
        $smallItem = new SmallItem();
        $smallItem->design_company_id = $request->input('design_company_id');
        $design_company = DesignCompanyModel::find($request->input('design_company_id'));
        if(!$design_company){
            return $this->response->array($this->apiError('没有找到设计公司', 404));
        }
        $smallItem->user_name = $request->input('user_name');
        $smallItem->item_name = '';
        $smallItem->is_ok = '';
        $smallItem->summary = '';
        $smallItem->phone = $request->input('phone');
        if($smallItem->save()){
            $text = '【太火鸟铟果】小程序榜单获客。榜单公司名称：'.$design_company->company_name.'；客户姓名：'.$request->input('user_name').'；联系方式：'.$request->input('phone');
//            $this->dispatch(new SendOneSms(config('constant.new_item_send_phone') , $text));
            $this->dispatch(new SendOneSms(15810295774 , $text));
            return $this->response->item($smallItem, new SmallItemTransformer())->setMeta($this->apiMeta());
        }
        return $this->response->array($this->apiError('添加失败', 412));
    }
}