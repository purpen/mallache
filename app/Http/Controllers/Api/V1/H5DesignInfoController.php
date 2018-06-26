<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ItemTransformer;
use App\Models\GraphicDesign;
use App\Models\H5Design;
use App\Models\Item;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class H5DesignInfoController extends BaseController
{
    /*
    id	                int(10)	否
    item_id	            int(10)			项目ID
    present_situation	tinyint(4)		null	项目现状 1.设计概念清晰 2.设计概念模糊 3. 无设计概念
    product_features	varchar(500)		null	项目详细描述
    status	            tinyint(4)		0	状态：
    */


    /**
     * @api {put} /H5Design/{item_id} 更改 H5详细信息
     * @apiVersion 1.0.0
     * @apiName demand update
     * @apiGroup demandH5Design
     *
     * @apiParam {integer} present_situation 	项目现状 1.设计概念清晰 2.设计概念模糊 3. 无设计概念
     * @apiParam {string} product_features 项目详细描述
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
            else if ((int)$item->type !== 4 || $item->user_id != $this->auth_user_id || 1 != $item->status) {
                return $this->response->array($this->apiError());
            }

            $item->stage_status = 3;
            $item->save();

            $design = H5Design::firstOrCreate(['item_id' => intval($item_id)]);
            $design->update($all);
        } catch (\Exception $e) {
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }

}
