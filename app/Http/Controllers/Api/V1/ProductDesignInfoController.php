<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ItemTransformer;
use App\Models\Item;
use App\Models\ProductDesign;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductDesignInfoController extends BaseController
{
    /*item_id	int(10)	否		项目ID
    name	varchar(50)	否	''	项目名称
    target_retail_price	decimal(10,2)	否	0	目标零售价格
    annual_sales	int(10)	否	0	年销量
    service_life	tinyint(4)	否	0	产品使用寿命:1.一次性；2.经常使用；3.产期使用；4.购买周期内
    competitive_brand	varchar(50)	否	''	竞争品牌
    competing_product	varchar(50)	否	''	竞品
    target_brand	varchar(50)	否	''	目标品牌
    brand_positioning	tinyint(4)	否	0	品牌定位：1.领导2.跟随3.高端4.中端5.低端
    product_size	varchar(50)	否	''	预期产品尺寸
    reference_model	varhar(50)	否	''	参考机型
    percentage_completion	tinyint(4)	否	0	产品内部框架完成百分比：1.无；2.30%；3.50%；4.70%；5.100%;
    special_technical_require	int(10)	否	0	特殊技术要求 class_id
    design_cost	tinyint(4)	否	0	设计费用：
    province	int(10)	否	0	省份
    city	int(10)	否	0	城市
    divided_into_cooperation	tinyint(4)	否	0	是否考虑销售分成入股 1.否；2.是；
    stock_cooperation	tinyint(4)	否	0	是否考虑设计入股合作方式1.否；2.是
    summary	varchar(500)	否	''	备注*/

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * @api {put} /ProductDesign/{item_id} 更改 产品设计详细信息
     * @apiVersion 1.0.0
     * @apiName demand update
     * @apiGroup demandProductDesign
     *
     * @apiParam {integer} stage_status //阶段；1.项目类型；2.需求信息；3.公司信息
     * @apiParam {string} name 项目名称
     * @apiParam {string} product_features 产品功能或两点
     * @apiParam {array} competing_product 竞品
     * @apiParam {integer} cycle 设计周期：1.1个月内；2.1-2个月；3.2个月；4.2-4个月；5.其他
     * @apiParam {integer} design_cost 设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
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
    public function update(Request $request, $item_id)
    {
//        dd($request->input('competing_product'));
        $all = $request->all();
        $rules = [
            'name' => 'required|max:50',
            'product_features' => 'required|max:500',
//            'competing_product' => 'array',
            'cycle' => 'required|integer',
            'design_cost' => 'required|integer',
            'province' => 'required|integer',
            'city' => 'required|integer',
        ];
        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try{
            if(!$item = Item::find($item_id)){
                return $this->response->array($this->apiError('not found!', 404));
            }
            //判断设计类型，用户权限是否正确
            else if ((int)$item->type !== 1 || $item->user_id != $this->auth_user_id || 1 != $item->status){
                return $this->response->array($this->apiError());
            }

            $item->stage_status = $request->input('stage_status') ?? 2;
            $item->save();

            $design = ProductDesign::where(['item_id' => intval($item_id)])->first();
//            $all['competing_product'] = implode('&', $request->input('competing_product'));
            $design->update($all);
        }catch(\Exception $e){
            dd($e);
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
