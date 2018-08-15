<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ItemTransformer;
use App\Models\GraphicDesign;
use App\Models\Item;
use App\Models\PackDesign;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PackDesignInfoController extends BaseController
{
    /*
    id	                int(10)	否
    item_id	            int(10)			项目ID
    present_situation	tinyint(4)		null	项目现状 1.设计理念与需求明确 2.设计理念与需求部分明确/待定 3. 无设计理念
    existing_content	tinyint(4)		null	项目现有内容 1.所需设计内容齐全 2.有核心视觉元素与标识使用手册 3.只有核心视觉元素 4.只有标识使用手册 5.没有任何设计元素
    product_features	varchar(500)	null	项目详细描述
    status	            tinyint(4)		0	    状态：
    */


    /**
     * @api {put} /PackDesign/{item_id} 更改 包装设计详细信息
     * @apiVersion 1.0.0
     * @apiName demand update
     * @apiGroup demandPackDesign
     *
     * @apiParam {integer} present_situation 项目现状 1.设计理念与需求明确 2.设计理念与需求部分明确/待定 3. 无设计理念
     * @apiParam {array} existing_content 项目现有内容  1.核心视觉元素 2.标识使用手册 3.其他
     * @apiParam {string} product_features 项目详细描述
     * @apiParam {string} other_content 其他设计内容：
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
            'present_situation' => 'required|integer',
            'existing_content' => 'required',
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
            else if ((int)$item->type !== 5 || $item->user_id != $this->auth_user_id || 1 != $item->status) {
                return $this->response->array($this->apiError());
            }

            $item->stage_status = 3;
            $item->design_types = '[1]';
            $item->save();

            $design = PackDesign::firstOrCreate(['item_id' => intval($item_id)]);
            $all['existing_content'] = implode('&', $all['existing_content']);

            $all['other_content'] = $request->input('other_content') ?? '';
            $design->update($all);
        } catch (\Exception $e) {
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }

}
