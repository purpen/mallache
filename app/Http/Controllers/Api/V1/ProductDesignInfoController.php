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
     * @api {put} /ProductDesign/{item_id} 更改 产品设计详细信息
     * @apiVersion 1.0.0
     * @apiName demand update
     * @apiGroup demandProductDesign
     *
     * @apiParam {json} design_types 设计类别：UXUI设计（1.app设计；2.网页设计；）。
     * @apiParam {integer} field //所属领域
     * @apiParam {string} product_features 产品功能或两点
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
            'field' => 'required|integer',
            'product_features' => 'required|max:500',
        ];
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try {
            if (!$item = Item::find($item_id)) {
                return $this->response->array($this->apiError('not found!', 404));
            } //判断设计类型，用户权限是否正确
            else if ((int)$item->type !== 1 || $item->user_id != $this->auth_user_id || 1 != $item->status) {
                return $this->response->array($this->apiError());
            }

            $item->stage_status = 3;
            $item->design_types = $request->input('design_types');
            $item->save();

            $design = ProductDesign::firstOrCreate(['item_id' => intval($item_id)]);
            $design->update($all);
        } catch (\Exception $e) {
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }

}
