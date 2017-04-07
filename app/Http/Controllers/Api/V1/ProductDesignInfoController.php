<?php

namespace App\Http\Controllers\Api\V1;

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
     * @apiParam {string} name 项目名称
     * @apiParam {decimal} target_retail_price 目标零售价格
     * @apiParam {integer} annual_sales	int(10)	年销量
     * @apiParam {integer} service_life	tinyint(4) 产品使用寿命:1.一次性；2.经常使用；3.产期使用；4.购买周期内
     * @apiParam {string} competitive_brand 竞争品牌
     * @apiParam {string} competing_product 竞品
     * @apiParam {string} target_brand 目标品牌
     * @apiParam {integer} brand_positioning 品牌定位：1.领导2.跟随3.高端4.中端5.低端
     * @apiParam {string} product_size 预期产品尺寸
     * @apiParam {string} reference_model 参考机型
     * @apiParam {integer} percentage_completion 产品内部框架完成百分比：1.无；2.30%；3.50%；4.70%；5.100%;
     * @apiParam {integer} special_technical_require 特殊技术要求 class_id
     * @apiParam {integer} design_cost 设计费用：
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} divided_into_cooperation 是否考虑销售分成入股 1.否；2.是；
     * @apiParam {integer} stock_cooperation 是否考虑设计入股合作方式1.否；2.是
     * @apiParam {string} summary 备注
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
        $all = $request->all();
        $rules = [
            'name' => 'max:50',
            'target_retail_price' => 'numeric',
            'annual_sales' => 'integer',
            'service_life' => ['integer', Rule::in([1, 2, 3, 4])],
            'competitive_brand' => 'max:50',
            'competing_product' => 'max:50',
            'target_brand' => 'max:50',
            'brand_positioning' => ['integer', Rule::in([1, 2, 3, 4, 5])],
            'product_size' => 'max:50',
            'reference_model' => 'max:50',
            'percentage_completion' => ['integer', Rule::in([1, 2, 3, 4, 5])],
            'special_technical_require' => 'integer',
            'design_cost' => 'integer',
            'province' => 'integer',
            'city' => 'integer',
            'divided_into_cooperation' => ['integer', Rule::in([1, 2])],
            'stock_cooperation' => ['integer', Rule::in([1, 2])],
            'summary' => 'max:500',
        ];
        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try{
            if(!$item = Item::find($item_id)){
                return $this->response->array($this->apiError('not found!', 404));
            }else if(!in_array($item->design_type, [1, 2, 3])){
                return $this->response->array($this->apiError());
            }

            $design = ProductDesign::firstOrCreate(['item_id' => intval($item_id)]);
            $design->update($all);
        }catch(\Exception $e){
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->array($this->apiSuccess());
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
