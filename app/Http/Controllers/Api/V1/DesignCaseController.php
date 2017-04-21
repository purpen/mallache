<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignCaseTransformer;
use App\Models\AssetModel;
use App\Models\DesignCaseModel;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DesignCaseController extends BaseController
{
    /**
     * @api {get} /designCase  用户id查看设计公司案例展示
     * @apiVersion 1.0.0
     * @apiName designCase index
     * @apiGroup designCase
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "prize": 1,
     *      "prize_val": "德国红点设计奖",
     *      "title": "1",
     *      "prize_time": "1991-01-20",
     *      "sales_volume": 1,
     *      "sales_volume_val": "100-500w",
     *      "customer": "1",
     *      "field": 2,
     *      "field_val": "消费电子",
     *      "profile": "1",
     *      "status": 0,
     *      "case_image": [],
     *      "industry": 2,
     *      "industry_val": "消费零售",
     *      "system": 2,
     *      "system_val": null,
     *      "design_content": 2,
     *      "design_content_val": null,
     *      "type": 1,
     *      "type_val": "产品设计",
     *      "design_type": 1,
     *      "design_type_val": "产品策略",
     *      "other_prize": ""
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     *
     */
    public function index()
    {
        $user_id = intval($this->auth_user_id);
        $designCase = DesignCaseModel::where('user_id', $user_id)->get();
        if(!$designCase){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->collection($designCase, new DesignCaseTransformer())->setMeta($this->apiMeta());

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @api {post} /designCase 保存设计公司案例
     * @apiVersion 1.0.0
     * @apiName designCase store
     * @apiGroup designCase
     * @apiParam {string} title 标题
     * @apiParam {integer} prize 奖项
     * @apiParam {string} prize_time 获奖时间
     * @apiParam {integer} mass_production 是否量产
     * @apiParam {integer} sales_volume 销售金额
     * @apiParam {string} customer 服务客户
     * @apiParam {string} profile   功能描述
     * @apiParam {integer} type   设计类型
     * @apiParam {integer} design_type   设计类别
     * @apiParam {integer} field 所属领域 class_id
     * @apiParam {integer} industry   所属行业
     * @apiParam {integer} system   系统
     * @apiParam {integer} design_content   设计内容
     * @apiParam {string} other_prize   其他奖项
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *    "data": {
     *      "id": 23,
     *      "prize": 1,
     *      "prize_val": "德国红点设计奖",
     *      "title": "1",
     *      "prize_time": "1991-01-20",
     *      "sales_volume": 1,
     *      "sales_volume_val": "100-500w",
     *      "customer": "1",
     *      "field": 2,
     *      "field_val": "消费电子",
     *      "profile": "1",
     *      "status": 0,
     *      "case_image": [],
     *      "industry": 2,
     *      "industry_val": "消费零售",
     *      "system": 2,
     *      "system_val": null,
     *      "design_content": 2,
     *      "design_content_val": null,
     *      "type": 1,
     *      "type_val": "产品设计",
     *      "design_type": 1,
     *      "design_type_val": "产品策略",
     *      "other_prize": ""
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *  }
     */
    public function store(Request $request)
    {
        Log::info($request->all());
        // 验证规则
        $rules = [
            'title'  => 'required|max:50',
            'mass_production'  => 'required|integer',
            'customer'  => 'required|max:50',
            'profile'  => 'required|max:500',
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '最多50字符',
            'mass_production.required' => '是否量产不能为空',
            'customer.required' => '服务客户不能为空',
            'customer.max' => '最多50字符',
            'profile.required' => '项目描述不能为空',
            'profile.max' => '最多500字符',
        ];
        $all['title'] = $request->input('title');
        $all['prize'] = $request->input('prize');
        if($all['prize'] == 20){
            $all['other_prize'] = $request->input('other_prize');
        }
        $all['prize_time'] = $request->input('prize_time');
        $all['sales_volume'] = $request->input('sales_volume');
        $all['mass_production'] = $request->input('mass_production');
        $all['customer'] = $request->input('customer');
        $all['field'] = $request->input('field');
        $all['profile'] = $request->input('profile');
        $all['user_id'] = $this->auth_user_id;
        $all['type'] = $request->input('type');
        $all['design_type'] = $request->input('design_type');
        $all['industry'] = $request->input('industry');
        $all['system'] = $request->input('system');
        $all['design_content'] = $request->input('design_content');
        $status = $request->input('status');
        if($status == null){
            $all['status'] = 0;
        }
        if($request->input('field') == null){
            $all['field'] = 0;
        }
        if($request->input('type') == null){
            $all['type'] = 0;
        }
        if($request->input('design_type') == null){
            $all['design_type'] = 0;
        }
        if($request->input('industry') == null){
            $all['industry'] = 0;
        }
        if($request->input('system') == null){
            $all['system'] = 0;
        }
        if($request->input('design_content') == null){
            $all['design_content'] = 0;
        }
        $validator = Validator::make($all , $rules, $messages);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        try{
            $designCase = DesignCaseModel::create($all);
            $random = $request->input('random');
            AssetModel::setRandom($designCase->id , $random);
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError());
        }

        return $this->response->item($designCase, new DesignCaseTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designCase/3  公司案例ID查看详情
     * @apiVersion 1.0.0
     * @apiName designCase show
     * @apiGroup designCase
     *
     * @apiParam {integer} id 案例ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *    "data": {
     *      "id": 23,
     *      "prize": 1,
     *      "prize_val": "德国红点设计奖",
     *      "title": "1",
     *      "prize_time": "1991-01-20",
     *      "sales_volume": 1,
     *      "sales_volume_val": "100-500w",
     *      "customer": "1",
     *      "field": 2,
     *      "field_val": "消费电子",
     *      "profile": "1",
     *      "status": 0,
     *      "case_image": [],
     *      "industry": 2,
     *      "industry_val": "消费零售",
     *      "system": 2,
     *      "system_val": null,
     *      "design_content": 2,
     *      "design_content_val": null,
     *      "type": 1,
     *      "type_val": "产品设计",
     *      "design_type": 1,
     *      "design_type_val": "产品策略",
     *      "other_prize": ""
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     */
    public function show(Request $request)
    {
        $id = intval($request->input('id'));
        $designCase = DesignCaseModel::where('id', $id)->first();
        if(!$designCase){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->item($designCase, new DesignCaseTransformer())->setMeta($this->apiMeta());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DesignCaseModel $designCaseModel
     * @return \Illuminate\Http\Response
     */
    public function edit(DesignCaseModel $designCaseModel)
    {
        //
    }

    /**
     * @api {put} /designCase/12 根据公司案例ID更新案例数据
     * @apiVersion 1.0.0
     * @apiName designCase update
     * @apiGroup designCase
     * @apiParam {string} title 标题
     * @apiParam {integer} prize 奖项
     * @apiParam {string} prize_time 获奖时间
     * @apiParam {integer} mass_production 是否量产
     * @apiParam {string} sales_volume 销售金额
     * @apiParam {string} customer 服务客户
     * @apiParam {string} profile   功能描述
     * @apiParam {integer} type   设计类型
     * @apiParam {integer} design_type   设计类别
     * @apiParam {integer} field 所属领域 class_id
     * @apiParam {integer} industry   所属行业
     * @apiParam {integer} system   系统
     * @apiParam {integer} design_content   设计内容
     * @apiParam {string} other_prize   其他奖项
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
     *    "data": {
     *      "id": 23,
     *      "prize": 1,
     *      "prize_val": "德国红点设计奖",
     *      "title": "1",
     *      "prize_time": "1991-01-20",
     *      "sales_volume": 1,
     *      "sales_volume_val": "100-500w",
     *      "customer": "1",
     *      "field": 2,
     *      "field_val": "消费电子",
     *      "profile": "1",
     *      "status": 0,
     *      "case_image": [],
     *      "industry": 2,
     *      "industry_val": "消费零售",
     *      "system": 2,
     *      "system_val": null,
     *      "design_content": 2,
     *      "design_content_val": null,
     *      "type": 1,
     *      "type_val": "产品设计",
     *      "design_type": 1,
     *      "design_type_val": "产品策略",
     *      "other_prize": ""
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     */
    public function update(Request $request , $id)
    {
        // 验证规则
        $rules = [
            'title'  => 'required|max:50',
            'mass_production'  => 'required|integer',
            'customer'  => 'required|max:50',
            'profile'  => 'required|max:500',
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '最多50字符',
            'mass_production.required' => '是否量产不能为空',
            'customer.required' => '服务客户不能为空',
            'customer.max' => '最多50字符',
            'profile.required' => '项目描述不能为空',
            'profile.max' => '最多500字符',
        ];
        $validator = Validator::make($request->only(['title' , 'mass_production' , 'customer' , 'field' , 'profile' , 'status']), $rules, $messages);

        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $all = $request->except(['token']);
        $status = $request->input('status');
        if($status == null){
            $all['status'] = 0;
        }
        //检验是否存在该案例
        $case = DesignCaseModel::find($id);
        if(!$case){
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的案例
        if($case->user_id != $this->auth_user_id){
            return $this->response->array($this->apiError('not found!', 404));
        }
        $designCase = DesignCaseModel::where('id', intval($id))->first();
        $designCase->update($all);
        if(!$designCase){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($designCase, new DesignCaseTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /designCase/3 根据公司案例ID删除案例
     * @apiVersion 1.0.0
     * @apiName designCase delete
     * @apiGroup designCase
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *  }
     */
    public function destroy($id)
    {
        //检验是否存在该案例
        $case = DesignCaseModel::find($id);
        if(!$case){
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的案例
        if($case->user_id != $this->auth_user_id){
            return $this->response->array($this->apiError('not found!', 404));
        }
        $designCase = $case->delete();
        if(!$designCase){
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }
}
