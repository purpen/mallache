<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\QuotationTransformer;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use App\Models\ItemRecommend;
use App\Models\QuotationModel;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class QuotationController extends BaseController
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @api {post} /quotation 报价单添加
     * @apiVersion 1.0.0
     * @apiName quotation store
     * @apiGroup quotation
     * @apiParam {integer} item_demand_id 项目需求id
     * @apiParam {string} price 报价
     * @apiParam {string} summary 报价说明
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "data": {
     *       "id": 2,
     *       "user_id": 1,
     *       "item_demand_id": 1,
     *       "design_company_id": 27,
     *       "price": "1",
     *       "summary": "1",
     *       },
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *  }
     */
    public function store(Request $request)
    {
        $all['item_demand_id'] = $request->input('item_demand_id');
        $design = DesignCompanyModel::where('user_id' , $this->auth_user_id)->first();
        if(!$design){
            return $this->response->array($this->apiError('设计公司不存在'));
        }
        $all['design_company_id'] = $design->id;
        $all['price'] = $request->input('price');
        $all['summary'] = $request->input('summary');
        $all['user_id'] = $this->auth_user_id;
        // 验证规则
        $rules = [
            'item_demand_id'  => 'required|integer',
            'design_company_id'  => 'required|integer',
            'price'  => 'required|max:50',
            'summary'  => 'required|max:500',
        ];
        $messages = [
            'item_demand_id.required' => '项目需求id不能为空',
            'design_company_id.required' => '设计公司id不能为空',
            'price.required' => '报价不能为空',
            'summary.required' => '报价说明不能为空',
            'summary.max' => '最多500字符',
        ];
        $validator = Validator::make($all, $rules, $messages);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        try{
            $item_recommend = ItemRecommend::where('item_id' , $request->input('item_demand_id'))->where('design_company_id' , $design->id)->first();
            Log::info($item_recommend);
            $quotation = QuotationModel::firstOrCreate($all);
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError());
        }

        return $this->response->item($quotation, new QuotationTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /quotation/{id}  报价单ID查看详情
     * @apiVersion 1.0.0
     * @apiName quotation show
     * @apiGroup quotation
     *
     * @apiParam {integer} id 账单ID
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     * {
     *       "data": {
     *           "id": 3,
     *           "item_demand_id": 2,
     *           "design_company_id": 1,
     *           "price": "10000.00",
     *           "summary": "项目不错",
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function show(Request $request)
    {
        $id = intval($request->input('id'));
        $quotation = QuotationModel::where('id', $id)->first();
        if(!$quotation){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->item($quotation, new QuotationTransformer())->setMeta($this->apiMeta());
    }

    /**
     */
    public function edit(QuotationModel $quotationModel)
    {
        //
    }

    /**
     * @api {put} /quotation/1 根据报价单id更新
     * @apiVersion 1.0.0
     * @apiName quotation update
     * @apiGroup quotation
     * @apiParam {integer} item_demand_id 项目需求id
     * @apiParam {string} price 报价
     * @apiParam {string} summary 报价说明
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *           "id": 3,
     *           "item_demand_id": 2,
     *           "design_company_id": 1,
     *           "price": "10000.00",
     *           "summary": "项目不错",
     *       },
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *  }
     */
    public function update(Request $request , $id)
    {
        $all = $request->all();


        // 验证规则
        $rules = [
            'item_demand_id'  => 'required|integer',
            'price'  => 'required|max:50',
            'summary'  => 'required|max:500',
        ];
        $messages = [
            'item_demand_id.required' => '项目需求id不能为空',
            'price.required' => '报价不能为空',
            'summary.required' => '报价说明不能为空',
            'summary.max' => '最多500字符',
        ];
        $validator = Validator::make($all , $rules, $messages);


        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $quotation = QuotationModel::find($id);

        if(!$quotation){
            return $this->response->array($this->apiError('not found!', 404));
        }
        if($quotation->user_id != $this->auth_user_id){
            return $this->response->array($this->apiError('not found!', 404));
        }
        $design = DesignCompanyModel::where('user_id' , $this->auth_user_id)->first();
        if(!$design){
            return $this->response->array($this->apiError('设计公司不存在'));
        }
        $all = $request->except(['token']);
        $all['design_company_id'] = $design->id;
        $quotation = QuotationModel::where('id', $id)->first();
        $quotation->update($all);
        if(!$quotation){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($quotation, new QuotationTransformer())->setMeta($this->apiMeta());
    }

    /**
     */
    public function destroy(QuotationModel $quotationModel)
    {
        //
    }
}
