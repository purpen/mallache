<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\ItemStatusEvent;
use App\Http\Transformer\ContractTransformer;
use App\Models\Contract;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContractController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * @api {post} /contract 保存合同
     * @apiVersion 1.0.0
     * @apiName contract store
     * @apiGroup contract
     *
     * @apiParam {integer} item_demand_id 项目需求id
     * @apiParam {string} demand_company_name 需求公司名称
     * @apiParam {string} demand_company_address 需求公司地址
     * @apiParam {string} demand_company_phone 需求公司电话
     * @apiParam {string} demand_company_legal_person 需求公司法人
     * @apiParam {string} design_company_name 设计公司名称
     * @apiParam {string} design_company_address 设计公司地址
     * @apiParam {string} design_company_phone 设计公司电话
     * @apiParam {string} design_company_legal_person 设计公司法人
     * @apiParam {string} design_type 设计类型
     * @apiParam {string} design_type_paragraph 设计类型款项
     * @apiParam {string} design_type_contain 设计类型包含
     * @apiParam {string} total 总额
     * @apiParam {string} project_start_date 项目启动日期
     * @apiParam {string} determine_design_date 设计确定日期
     * @apiParam {string} structure_layout_date 结构布局验证日期
     * @apiParam {string} design_sketch_date 效果图日期
     * @apiParam {string} end_date 最后确认日期
     * @apiParam {string} one_third_total 30%总额
     * @apiParam {integer} exterior_design_percentage 外观设计百分比
     * @apiParam {string} exterior_design_money 外观设计金额
     * @apiParam {string} exterior_design_phase 外观设计阶段
     * @apiParam {integer} exterior_modeling_design_percentage 外观建模设计百分比
     * @apiParam {string} exterior_modeling_design_money 外观建模设计金额
     * @apiParam {string} design_work_content 设计工作内容
     * @apiParam {string} exterior_modeling_design_money 外观建模设计金额
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *      "id": 1,
     *      "item_demand_id": 1,
     *      "design_company_id": 47,
     *      "demand_company_name": "",
     *      "demand_company_address": "",
     *      "demand_company_phone": "",
     *      "demand_company_legal_person": "",
     *      "design_company_name": "",
     *      "design_company_address": "",
     *      "design_company_phone": "",
     *      "design_company_legal_person": "",
     *      "design_type": "",
     *      "design_type_paragraph": "",
     *      "design_type_contain": "",
     *      "total": "",
     *      "project_start_date": 0,
     *      "determine_design_date": 0,
     *      "structure_layout_date": 0,
     *      "design_sketch_date": 0,
     *      "end_date": 0,
     *      "one_third_total": "",
     *      "exterior_design_percentage": 0,
     *      "exterior_design_money": "",
     *      "exterior_design_phase": "",
     *      "exterior_modeling_design_percentage": 0,
     *      "exterior_modeling_design_money": "",
     *      "design_work_content": "",
     *      "unique_id": "ht59018f4e78ebe"
     *      "status": 0
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *  }
     */
    public function store(Request $request)
    {
        $item = Item::where('id' , $request->input('item_demand_id'))->first();
        if(!$item){
            return $this->response->array($this->apiError('没有找到该项目' , 404));
        }
        if($item->status !== 5){
            return $this->response->array($this->apiError('设计公司还没有选定' ,403));
        }
        $design = DesignCompanyModel::where('user_id' , $this->auth_user_id)->first();
        if(!$design){
            return $this->response->array($this->apiError('设计公司不存在'));
        }
        if($item->design_company_id !== $design->id){
            return $this->response->array($this->apiError('没有权限添加合同' , 403));
        }
        $all['item_demand_id'] = $item->id;
        $all['design_company_id'] = $design->id;
        $all['demand_company_name'] = $request->input('demand_company_name');
        $all['demand_company_address'] = $request->input('demand_company_address');
        $all['demand_company_phone'] = $request->input('demand_company_phone');
        $all['demand_company_legal_person'] = $request->input('demand_company_legal_person');
        $all['design_company_name'] = $request->input('design_company_name');
        $all['design_company_address'] = $request->input('design_company_address');
        $all['design_company_phone'] = $request->input('design_company_phone');
        $all['design_company_legal_person'] = $request->input('design_company_legal_person');
        $all['design_type'] = $request->input('design_type');
        $all['design_type_paragraph'] = $request->input('design_type_paragraph');
        $all['design_type_contain'] = $request->input('design_type_contain');
        $all['total'] = $request->input('total');
        $all['project_start_date'] = $request->input('project_start_date');
        $all['determine_design_date'] = $request->input('determine_design_date');
        $all['structure_layout_date'] = $request->input('structure_layout_date');
        $all['design_sketch_date'] = $request->input('design_sketch_date');
        $all['end_date'] = $request->input('end_date');
        $all['one_third_total'] = $request->input('one_third_total');
        $all['exterior_design_percentage'] = $request->input('exterior_design_percentage');
        $all['exterior_design_money'] = $request->input('exterior_design_money');
        $all['exterior_design_phase'] = $request->input('exterior_design_phase');
        $all['exterior_modeling_design_percentage'] = $request->input('exterior_modeling_design_percentage');
        $all['exterior_modeling_design_money'] = $request->input('exterior_modeling_design_money');
        $all['design_work_content'] = $request->input('design_work_content');
        $all['exterior_modeling_design_money'] = $request->input('exterior_modeling_design_money');
        $all['unique_id'] = uniqid('ht');
        $rules = [
            'item_demand_id'  => 'required|integer',
            'design_company_id'  => 'required|integer',
            'demand_company_name'  => 'required',
            'demand_company_address'  => 'required',
            'demand_company_phone'  => 'required',
            'demand_company_legal_person'  => 'required',
            'design_company_name'  => 'required',
            'design_company_address'  => 'required',
            'design_company_phone'  => 'required',
            'design_company_legal_person'  => 'required',
            'design_type'  => 'required',
            'design_type_paragraph'  => 'required',
            'design_type_contain'  => 'required',
            'total'  => 'required',
            'project_start_date'  => 'required',
            'determine_design_date'  => 'required',
            'structure_layout_date'  => 'required',
            'design_sketch_date'  => 'required',
            'end_date'  => 'required',
            'one_third_total'  => 'required',
            'exterior_design_percentage'  => 'required',
            'exterior_design_money'  => 'required',
            'exterior_design_phase'  => 'required',
            'exterior_modeling_design_percentage'  => 'required',
            'exterior_modeling_design_money'  => 'required',
            'design_work_content'  => 'required',
        ];

        $messages = [
            'item_demand_id.required' => '项目需求id不能为空',
            'design_company_id.required' => '设计公司id不能为空',
            'demand_company_name.required' => '需求公司名称不能为空',
            'demand_company_address.required' => '需求公司地址不能为空',
            'demand_company_phone.required' => '需求公司电话不能为空',
            'demand_company_legal_person.required' => '需求公司法人不能为空',
            'design_company_name.required' => '设计公司名称不能为空',
            'design_company_address.required' => '设计公司地址不能为空',
            'design_company_phone.required' => '设计公司电话不能为空',
            'design_company_legal_person.required' => '设计公司法人不能为空',
            'design_type.required' => '设计类型不能为空',
            'design_type_paragraph.required' => '设计类型几款不能为空',
            'design_type_contain.required' => '设计类型包含不能为空',
            'total.required' => '总额不能为空',
            'project_start_date.required' => '项目启动日期不能为空',
            'determine_design_date.required' => '设计确定日期不能为空',
            'structure_layout_date.required' => '结构布局验证日期不能为空',
            'design_sketch_date.required' => '效果图日期不能为空',
            'end_date.required' => '最后确认日期不能为空',
            'one_third_total.required' => '30%总额不能为空',
            'exterior_design_percentage.required' => '外观设计百分比不能为空',
            'exterior_design_money.required' => '外观设计金额不能为空',
            'exterior_design_phase.required' => '外观设计阶段不能为空',
            'exterior_modeling_design_percentage.required' => '外观建模设计百分比不能为空',
            'exterior_modeling_design_money.required' => '外观建模设计金额不能为空',
            'design_work_content.required' => '设计工作内容不能为空',
        ];
        $validator = Validator::make($all , $rules, $messages);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        try{
            if($item->contract_id === 0){
                $contract = Contract::create($all);
                $item->contract_id = $contract->id;
                $item->status = 6;
                $item->save();

                event(new ItemStatusEvent($item));
            }else{
                return $this->response->array($this->apiError());
            }
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError('项目合同已创建' , 403));
        }
        return $this->response->item($contract, new ContractTransformer())->setMeta($this->apiMeta());

    }


    /**
     * @api {get} /contract/{unique_id} 合同id查看信息
     * @apiVersion 1.0.0
     * @apiName contract show
     * @apiGroup contract
     *
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *      "id": 1,
     *      "item_demand_id": 1,
     *      "design_company_id": 47,
     *      "demand_company_name": "",
     *      "demand_company_address": "",
     *      "demand_company_phone": "",
     *      "demand_company_legal_person": "",
     *      "design_company_name": "",
     *      "design_company_address": "",
     *      "design_company_phone": "",
     *      "design_company_legal_person": "",
     *      "design_type": "",
     *      "design_type_paragraph": "",
     *      "design_type_contain": "",
     *      "total": "",
     *      "project_start_date": 0,
     *      "determine_design_date": 0,
     *      "structure_layout_date": 0,
     *      "design_sketch_date": 0,
     *      "end_date": 0,
     *      "one_third_total": "",
     *      "exterior_design_percentage": 0,
     *      "exterior_design_money": "",
     *      "exterior_design_phase": "",
     *      "exterior_modeling_design_percentage": 0,
     *      "exterior_modeling_design_money": "",
     *      "design_work_content": "",
     *      "unique_id": "ht59018f4e78ebe"
     *      "status": 0
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *  }
     */
    public function show($unique_id)
    {
        $contract = Contract::where('unique_id' , $unique_id)->first();
        if(!$contract){
            return $this->response->array($this->apiSuccess('没有找到该合同' , 200));
        }
        return $this->response->item($contract, new ContractTransformer())->setMeta($this->apiMeta());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function edit(Contract $contract)
    {
        //
    }

    /**
     * @api {put} /contract/{id} 更改合同
     * @apiVersion 1.0.0
     * @apiName contract update
     * @apiGroup contract
     *
     * @apiParam {integer} item_demand_id 项目需求id
     * @apiParam {string} demand_company_name 需求公司名称
     * @apiParam {string} demand_company_address 需求公司地址
     * @apiParam {string} demand_company_phone 需求公司电话
     * @apiParam {string} demand_company_legal_person 需求公司法人
     * @apiParam {string} design_company_name 设计公司名称
     * @apiParam {string} design_company_address 设计公司地址
     * @apiParam {string} design_company_phone 设计公司电话
     * @apiParam {string} design_company_legal_person 设计公司法人
     * @apiParam {string} design_type 设计类型
     * @apiParam {string} design_type_paragraph 设计类型款项
     * @apiParam {string} design_type_contain 设计类型包含
     * @apiParam {string} total 总额
     * @apiParam {string} project_start_date 项目启动日期
     * @apiParam {string} determine_design_date 设计确定日期
     * @apiParam {string} structure_layout_date 结构布局验证日期
     * @apiParam {string} design_sketch_date 效果图日期
     * @apiParam {string} end_date 最后确认日期
     * @apiParam {string} one_third_total 30%总额
     * @apiParam {integer} exterior_design_percentage 外观设计百分比
     * @apiParam {string} exterior_design_money 外观设计金额
     * @apiParam {string} exterior_design_phase 外观设计阶段
     * @apiParam {integer} exterior_modeling_design_percentage 外观建模设计百分比
     * @apiParam {string} exterior_modeling_design_money 外观建模设计金额
     * @apiParam {string} design_work_content 设计工作内容
     * @apiParam {string} exterior_modeling_design_money 外观建模设计金额
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *      "id": 1,
     *      "item_demand_id": 1,
     *      "design_company_id": 47,
     *      "demand_company_name": "",
     *      "demand_company_address": "",
     *      "demand_company_phone": "",
     *      "demand_company_legal_person": "",
     *      "design_company_name": "",
     *      "design_company_address": "",
     *      "design_company_phone": "",
     *      "design_company_legal_person": "",
     *      "design_type": "",
     *      "design_type_paragraph": "",
     *      "design_type_contain": "",
     *      "total": "",
     *      "project_start_date": 0,
     *      "determine_design_date": 0,
     *      "structure_layout_date": 0,
     *      "design_sketch_date": 0,
     *      "end_date": 0,
     *      "one_third_total": "",
     *      "exterior_design_percentage": 0,
     *      "exterior_design_money": "",
     *      "exterior_design_phase": "",
     *      "exterior_modeling_design_percentage": 0,
     *      "exterior_modeling_design_money": "",
     *      "design_work_content": "",
     *      "unique_id": "ht59018f4e78ebe"
     *      "status": 0
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *  }
     */
    public function update(Request $request , $id)
    {
        $user_id = $this->auth_user_id;

        $contract = Contract::where('id' , $id)->first();
        if(!$contract){
            return $this->response->array($this->apiError('没有找到合同!', 404));
        }
        if($contract->status == 1){
            return $this->response->array($this->apiSuccess('合同已确认，不能修改', 200));
        }
        $design = DesignCompanyModel::where('user_id' , $user_id)->first();
        if(!$design){
            return $this->response->array($this->apiSuccess('没有找到设计公司', 404));
        }

        if($contract->design_company_id !== $design->id){
            return $this->response->array($this->apiSuccess('没有权限修改', 403));
        }
        $all['item_demand_id'] = $request->input('item_demand_id');
        $all['demand_company_name'] = $request->input('demand_company_name');
        $all['demand_company_address'] = $request->input('demand_company_address');
        $all['demand_company_phone'] = $request->input('demand_company_phone');
        $all['demand_company_legal_person'] = $request->input('demand_company_legal_person');
        $all['design_company_name'] = $request->input('design_company_name');
        $all['design_company_address'] = $request->input('design_company_address');
        $all['design_company_phone'] = $request->input('design_company_phone');
        $all['design_company_legal_person'] = $request->input('design_company_legal_person');
        $all['design_type'] = $request->input('design_type');
        $all['design_type_paragraph'] = $request->input('design_type_paragraph');
        $all['design_type_contain'] = $request->input('design_type_contain');
        $all['total'] = $request->input('total');
        $all['project_start_date'] = $request->input('project_start_date');
        $all['determine_design_date'] = $request->input('determine_design_date');
        $all['structure_layout_date'] = $request->input('structure_layout_date');
        $all['design_sketch_date'] = $request->input('design_sketch_date');
        $all['end_date'] = $request->input('end_date');
        $all['one_third_total'] = $request->input('one_third_total');
        $all['exterior_design_percentage'] = $request->input('exterior_design_percentage');
        $all['exterior_design_money'] = $request->input('exterior_design_money');
        $all['exterior_design_phase'] = $request->input('exterior_design_phase');
        $all['exterior_modeling_design_percentage'] = $request->input('exterior_modeling_design_percentage');
        $all['exterior_modeling_design_money'] = $request->input('exterior_modeling_design_money');
        $all['design_work_content'] = $request->input('design_work_content');
        $all['exterior_modeling_design_money'] = $request->input('exterior_modeling_design_money');
        $rules = [
            'item_demand_id'  => 'required|integer',
            'design_company_id'  => 'required|integer',
            'demand_company_name'  => 'required',
            'demand_company_address'  => 'required',
            'demand_company_phone'  => 'required',
            'demand_company_legal_person'  => 'required',
            'design_company_name'  => 'required',
            'design_company_address'  => 'required',
            'design_company_phone'  => 'required',
            'design_company_legal_person'  => 'required',
            'design_type'  => 'required',
            'design_type_paragraph'  => 'required',
            'design_type_contain'  => 'required',
            'total'  => 'required',
            'project_start_date'  => 'required',
            'determine_design_date'  => 'required',
            'structure_layout_date'  => 'required',
            'design_sketch_date'  => 'required',
            'end_date'  => 'required',
            'one_third_total'  => 'required',
            'exterior_design_percentage'  => 'required',
            'exterior_design_money'  => 'required',
            'exterior_design_phase'  => 'required',
            'exterior_modeling_design_percentage'  => 'required',
            'exterior_modeling_design_money'  => 'required',
            'design_work_content'  => 'required',
        ];

        $messages = [
            'item_demand_id.required' => '项目需求id不能为空',
            'design_company_id.required' => '设计公司id不能为空',
            'demand_company_name.required' => '需求公司名称不能为空',
            'demand_company_address.required' => '需求公司地址不能为空',
            'demand_company_phone.required' => '需求公司电话不能为空',
            'demand_company_legal_person.required' => '需求公司法人不能为空',
            'design_company_name.required' => '设计公司名称不能为空',
            'design_company_address.required' => '设计公司地址不能为空',
            'design_company_phone.required' => '设计公司电话不能为空',
            'design_company_legal_person.required' => '设计公司法人不能为空',
            'design_type.required' => '设计类型不能为空',
            'design_type_paragraph.required' => '设计类型几款不能为空',
            'design_type_contain.required' => '设计类型包含不能为空',
            'total.required' => '总额不能为空',
            'project_start_date.required' => '项目启动日期不能为空',
            'determine_design_date.required' => '设计确定日期不能为空',
            'structure_layout_date.required' => '结构布局验证日期不能为空',
            'design_sketch_date.required' => '效果图日期不能为空',
            'end_date.required' => '最后确认日期不能为空',
            'one_third_total.required' => '30%总额不能为空',
            'exterior_design_percentage.required' => '外观设计百分比不能为空',
            'exterior_design_money.required' => '外观设计金额不能为空',
            'exterior_design_phase.required' => '外观设计阶段不能为空',
            'exterior_modeling_design_percentage.required' => '外观建模设计百分比不能为空',
            'exterior_modeling_design_money.required' => '外观建模设计金额不能为空',
            'design_work_content.required' => '设计工作内容不能为空',
        ];
        $validator = Validator::make($all , $rules, $messages);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $contract->update($all);
        if(!$contract){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($contract, new ContractTransformer())->setMeta($this->apiMeta());

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contract $contract)
    {
        //
    }
}
