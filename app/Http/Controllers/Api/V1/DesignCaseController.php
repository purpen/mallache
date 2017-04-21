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
     * @apiParam {integer} prize 奖项:1.德国红点设计奖;2.德国IF设计奖;3.IDEA工业设计奖;4.中国红星奖;5.中国红棉奖;6.台湾金点奖;7.香港DFA设计奖 ;8.日本G-Mark设计奖;9.韩国好设计奖;10.新加坡设计奖;11.意大利—Compasso d`Oro设计奖;12.英国设计奖;20:其他
     * @apiParam {string} prize_time 获奖时间
     * @apiParam {integer} mass_production 是否量产
     * @apiParam {integer} sales_volume 销售金额:1.100-500w;2.500-1000w;3.1000-5000w;4.5000-10000w;5.10000w以上
     * @apiParam {string} customer 服务客户
     * @apiParam {string} profile   功能描述
     * @apiParam {integer} type   设计类型：1.产品设计；2.UI UX 设计；
     * @apiParam {integer} design_type   设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）
     * @apiParam {integer} field 所属领域 1.智能硬件;2.消费电子;3.交通工具;4.工业设备;5.厨电厨具;6.医疗设备;7.家具用品;8.办公用品;9.大家电;10.小家电;11.卫浴;12.玩具;13.体育用品;14.军工设备;15.户外用品
     * @apiParam {integer} industry 所属行业 1.制造业;2.消费零售;3.信息技术;4.能源;5.金融地产;6.服务业;7.医疗保健;8.原材料;9.工业制品;10.军工;11.公用事业
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
        // 验证规则
        $rules = [
            'title'  => 'required|max:50',
            'mass_production'  => 'required|integer',
            'customer'  => 'required|max:50',
            'profile'  => 'required|max:500',
            'field'  => 'integer',
            'type'  => 'integer',
            'design_type'  => 'integer',
            'industry'  => 'integer',
            'prize_time'  => 'date',
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '最多50字符',
            'mass_production.required' => '是否量产不能为空',
            'customer.required' => '服务客户不能为空',
            'customer.max' => '最多50字符',
            'profile.required' => '项目描述不能为空',
            'profile.max' => '最多500字符',
            'field.integer' => '所属领域必须为整形',
            'type.integer' => '设计类型必须为整形',
            'design_type.integer' => '设计类别必须为整形',
            'industry.integer' => '所属行业必须为整形',
            'prize_time.date' => '日期格式不正确',
        ];
        $all['title'] = $request->input('title');
        $all['prize'] = $request->input('prize');
        if($all['prize'] == 20){
            $all['other_prize'] = $request->input('other_prize');
        }
        $all['prize_time'] = $request->input('prize_time');
        $all['sales_volume'] = $request->input('sales_volume' , 0);
        $all['mass_production'] = $request->input('mass_production');
        $all['customer'] = $request->input('customer');
        $all['field'] = $request->input('field' , 0);
        $all['profile'] = $request->input('profile');
        $all['user_id'] = $this->auth_user_id;
        $all['type'] = $request->input('type' , 0);
        $all['design_type'] = $request->input('design_type' , 0);
        $all['industry'] = $request->input('industry' , 0);
        $all['status'] = $request->input('status' , 0);
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
     * @apiParam {integer} prize 奖项:1.德国红点设计奖;2.德国IF设计奖;3.IDEA工业设计奖;4.中国红星奖;5.中国红棉奖;6.台湾金点奖;7.香港DFA设计奖 ;8.日本G-Mark设计奖;9.韩国好设计奖;10.新加坡设计奖;11.意大利—Compasso d`Oro设计奖;12.英国设计奖;20:其他
     * @apiParam {string} prize_time 获奖时间
     * @apiParam {integer} mass_production 是否量产
     * @apiParam {integer} sales_volume 销售金额:1.100-500w;2.500-1000w;3.1000-5000w;4.5000-10000w;5.10000w以上
     * @apiParam {string} customer 服务客户
     * @apiParam {string} profile   功能描述
     * @apiParam {integer} type   设计类型：1.产品设计；2.UI UX 设计；
     * @apiParam {integer} design_type   设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）
     * @apiParam {integer} field 所属领域 1.智能硬件;2.消费电子;3.交通工具;4.工业设备;5.厨电厨具;6.医疗设备;7.家具用品;8.办公用品;9.大家电;10.小家电;11.卫浴;12.玩具;13.体育用品;14.军工设备;15.户外用品
     * @apiParam {integer} industry 所属行业 1.制造业;2.消费零售;3.信息技术;4.能源;5.金融地产;6.服务业;7.医疗保健;8.原材料;9.工业制品;10.军工;11.公用事业
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
            'field'  => 'integer',
            'type'  => 'integer',
            'design_type'  => 'integer',
            'industry'  => 'integer',
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '最多50字符',
            'mass_production.required' => '是否量产不能为空',
            'customer.required' => '服务客户不能为空',
            'customer.max' => '最多50字符',
            'profile.required' => '项目描述不能为空',
            'profile.max' => '最多500字符',
            'field.integer' => '所属领域必须为整形',
            'type.integer' => '设计类型必须为整形',
            'design_type.integer' => '设计类别必须为整形',
            'industry.integer' => '所属行业必须为整形',
        ];
        $validator = Validator::make($request->only(['type' , 'design_type' , 'industry' , 'title' , 'mass_production' , 'customer' , 'field' , 'profile' , 'status']), $rules, $messages);

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
