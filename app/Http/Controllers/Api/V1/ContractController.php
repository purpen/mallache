<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\ItemStatusEvent;
use App\Http\AdminTransformer\ItemTransformer;
use App\Http\Transformer\ContractTransformer;
use App\Models\Contract;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use App\Models\ItemStage;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * @apiParam {string} item_content 项目内容
     * @apiParam {string} design_work_content 设计工作内容
     * @apiParam {string} title 合同名称
     * @apiParam {array} item_stage 项目阶段 [['sort' => '1','percentage' => '0.1 百分比', 'amount' => '1.99 金额', 'title' => '阶段名称'， 'time' => '2012-12']]
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
     *      "total": "",
 *          "item_content": '',
     *      "design_work_content": "",
     *      "unique_id": "ht59018f4e78ebe"
     *      "status": 0,
     *      "item_stage":
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

        $all = $request->all();
        $all['item_demand_id'] = $item->id;
        $all['design_company_id'] = $design->id;
//        $all['demand_company_name'] = $request->input('demand_company_name');
//        $all['demand_company_address'] = $request->input('demand_company_address');
//        $all['demand_company_phone'] = $request->input('demand_company_phone');
//        $all['demand_company_legal_person'] = $request->input('demand_company_legal_person');
//        $all['design_company_name'] = $request->input('design_company_name');
//        $all['design_company_address'] = $request->input('design_company_address');
//        $all['design_company_phone'] = $request->input('design_company_phone');
//        $all['design_company_legal_person'] = $request->input('design_company_legal_person');
//
//        $all['item_content'] = $request->input('item_content');
//        $all['design_work_content'] = $request->input('design_work_content');
//        $all['title'] = $request->input('title');
        $all['unique_id'] = uniqid('ht');
//        $all['item_stage'] = $request->input('item_stage');

        $all['total'] = $item->price;
        $rules = [
            'item_demand_id'  => 'required|integer',
            'demand_company_name'  => 'required',
            'demand_company_address'  => 'required',
            'demand_company_phone'  => 'required',
            'demand_company_legal_person'  => 'required',
            'design_company_name'  => 'required',
            'design_company_address'  => 'required',
            'design_company_phone'  => 'required',
            'design_company_legal_person'  => 'required',
            'item_content' => 'required',
            'design_work_content'  => 'required',
            'title'  => 'required|max:20',
//            'item_stage' => 'array',
        ];

        $messages = [
            'item_demand_id.required' => '项目需求id不能为空',
            'demand_company_name.required' => '需求公司名称不能为空',
            'demand_company_address.required' => '需求公司地址不能为空',
            'demand_company_phone.required' => '需求公司电话不能为空',
            'demand_company_legal_person.required' => '需求公司法人不能为空',
            'design_company_name.required' => '设计公司名称不能为空',
            'design_company_address.required' => '设计公司地址不能为空',
            'design_company_phone.required' => '设计公司电话不能为空',
            'design_company_legal_person.required' => '设计公司法人不能为空',
            'item_content' => '项目内容不能为空',
            'design_work_content.required' => '设计工作内容不能为空',
            'title.required' => '合同名称不能为空',
            'title.max' => '合同名称不能超过20个字符',
        ];
        $validator = Validator::make($all , $rules, $messages);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }


        //验证项目阶段数组数据
        if(!$this->validationItemStage($all['item_stage'], $all['total'])){
            return $this->response->array($this->apiError('项目阶段数据不正确' , 403));
        }

        try{
            DB::beginTransaction();

            $contract = Contract::create($all);

            foreach ($all['item_stage'] as $stage){
                $stage['item_id'] = $contract->item_demand_id;
                $stage['design_company_id'] = $design->id;
                ItemStage::create($stage);
            }
        }
        catch (\Exception $e){
            DB::rollBack();
            return $this->response->array($this->apiError('创建失败' , 500));
        }
        DB::commit();
        return $this->response->item($contract, new ContractTransformer())->setMeta($this->apiMeta());

    }

    /**
     * 判断项目阶段信息是否正确
     * @param $item_stage
     * @param $total
     * @return bool
     */
    protected function validationItemStage($item_stage, $total)
    {
        $percentage = 0;
        $amount = 0;
        $title_is_set = true;
        $time_is_set = true;
dd($item_stage);
        foreach ($item_stage as $stage){
            $percentage += $stage['percentage'] * 100;
            $amount += $stage['amount'] * 100;
            $title_is_set = !empty($stage['title']);
            $time_is_set = !empty($stage['time']);
        }
        if($percentage == 1*100 && $amount == $total * 100 && $title_is_set && $time_is_set){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @api {post} /contract/ok 合同发送按钮
     * @apiVersion 1.0.0
     * @apiName contract okContract
     * @apiGroup contract
     *
     * @apiParam {string} token
     * @apiParam {integer} item_demand_id 项目需求id
     *
     */
    public function okContract(Request $request)
    {
        $item = Item::where('id' , $request->input('item_demand_id'))->first();
        if(!$item){
            return $this->response->array($this->apiError('没有找到该项目' , 404));
        }
        $design = DesignCompanyModel::where('user_id' , $this->auth_user_id)->first();
        if(!$design){
            return $this->response->array($this->apiError('设计公司不存在'));
        }
        if($item->design_company_id !== $design->id){
            return $this->response->array($this->apiError('没有权限添加合同' , 403));
        }
        $contract = Contract::where('item_demand_id' , $request->input('item_demand_id'))->first();
        if(!$contract){
            return $this->response->array($this->apiError('没有找到该合同' , 404));
        }
        if($item->contract_id === 0) {
            $item->contract_id = $contract->id;
            $item->status = 6;
            $item->save();
            event(new ItemStatusEvent($item));
        }else{
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->item($item, new ItemTransformer())->setMeta($this->apiMeta());


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
     *      "total": "",
     *      "item_content": '',
     *      "design_work_content": "",
     *      "unique_id": "ht59018f4e78ebe"
     *      "status": 0,
     *      "item_stage":
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
        $item = Item::where('id' , $contract->item_demand_id)->first();
        if(!$item){
            return $this->response->array($this->apiSuccess('没有找到该项目' , 200));
        }
        if($item->type == 1){
            $contract->item_name = $item->productDesign->name ?? '';
        }elseif($item->type == 2){
            $contract->item_name = $item->uDesign->name ?? '';
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
     * @apiParam {string} demand_company_name 需求公司名称
     * @apiParam {string} demand_company_address 需求公司地址
     * @apiParam {string} demand_company_phone 需求公司电话
     * @apiParam {string} demand_company_legal_person 需求公司法人
     * @apiParam {string} design_company_name 设计公司名称
     * @apiParam {string} design_company_address 设计公司地址
     * @apiParam {string} design_company_phone 设计公司电话
     * @apiParam {string} design_company_legal_person 设计公司法人
     * @apiParam {string} item_content 项目内容
     * @apiParam {string} design_work_content 设计工作内容
     * @apiParam {string} title 合同名称
     * @apiParam {array} item_stage 项目阶段 [['sort' => '1','percentage' => '0.1 百分比', 'amount' => '1.99 金额', 'title' => '阶段名称'， 'time' => '2012-12']]
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
            return $this->response->array($this->apiSuccess('合同已确认，不能修改', 403));
        }
        $design = DesignCompanyModel::where('user_id' , $user_id)->first();
        if(!$design){
            return $this->response->array($this->apiSuccess('没有找到设计公司', 404));
        }

        if($contract->design_company_id !== $design->id){
            return $this->response->array($this->apiSuccess('没有权限修改', 403));
        }
//        $all['demand_company_name'] = $request->input('demand_company_name');
//        $all['demand_company_address'] = $request->input('demand_company_address');
//        $all['demand_company_phone'] = $request->input('demand_company_phone');
//        $all['demand_company_legal_person'] = $request->input('demand_company_legal_person');
//        $all['design_company_name'] = $request->input('design_company_name');
//        $all['design_company_address'] = $request->input('design_company_address');
//        $all['design_company_phone'] = $request->input('design_company_phone');
//        $all['design_company_legal_person'] = $request->input('design_company_legal_person');
//        $all['item_content'] = $request->input('item_content');
//        $all['design_work_content'] = $request->input('design_work_content');
//
//        $all['title'] = $request->input('title');
//        $all['item_stage'] = $request->input('item_stage');
        $all = $request->all();

        $rules = [
            'demand_company_name'  => 'required',
            'demand_company_address'  => 'required',
            'demand_company_phone'  => 'required',
            'demand_company_legal_person'  => 'required',
            'design_company_name'  => 'required',
            'design_company_address'  => 'required',
            'design_company_phone'  => 'required',
            'design_company_legal_person'  => 'required',

            'item_content' => 'required',
            'design_work_content'  => 'required',
            'title'  => 'required|max:20',
            'item_stage' => 'array',

        ];

        $messages = [
            'item_demand_id.required' => '项目需求id不能为空',
            'demand_company_name.required' => '需求公司名称不能为空',
            'demand_company_address.required' => '需求公司地址不能为空',
            'demand_company_phone.required' => '需求公司电话不能为空',
            'demand_company_legal_person.required' => '需求公司法人不能为空',
            'design_company_name.required' => '设计公司名称不能为空',
            'design_company_address.required' => '设计公司地址不能为空',
            'design_company_phone.required' => '设计公司电话不能为空',
            'design_company_legal_person.required' => '设计公司法人不能为空',
            'design_work_content.required' => '设计工作内容不能为空',
            'title.required' => '合同名称不能为空',
            'title.max' => '合同名称不能超过20个字符',
        ];
        $validator = Validator::make($all , $rules, $messages);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        //验证项目阶段数组数据
        if(!$this->validationItemStage($all['item_stage'], $contract->total)){
            return $this->response->array($this->apiError('项目阶段数据不正确' , 403));
        }

        try{
            DB::beginTransaction();

            $contract->update($all);

            //删除项目阶段信息
            ItemStage::where(['item_id' => $contract->item_demand_id, 'design_company_id' => $design->id])->delete();

            foreach ($all['item_stage'] as $stage){
                $stage['item_id'] = $contract->item_demand_id;
                $stage['design_company_id'] = $design->id;
                ItemStage::create($stage);
            }
        }
        catch (\Exception $e){
            DB::rollBack();
            return $this->response->array($this->apiError('创建失败' , 500));
        }
        DB::commit();
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
