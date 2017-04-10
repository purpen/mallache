<?php

/**
 * 公共接口
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\CityTransformer;
use App\Models\ChinaCity;
use Dingo\Api\Contract\Http\Request;

class CommonController extends BaseController
{
    /**
     * @api {get} /city 获取城市列表
     * @apiVersion 1.0.0
     * @apiName city list
     * @apiGroup Common
     *
     * @apiParam {string} oid 城市唯一id（0）
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *   }
     *   "data":[
     *       {
     *          "oid": 1,
     *              "name": "北京",
     *              "pid": 0,
     *              "sort": 1
     *        }
     *      ]
     *  }
     */
    public function city(Request $request)
    {
        $oid = (int)$request->input('oid');

        $items = ChinaCity::fetchCity($oid);
        if($items->isEmpty()){
           return $this->response()->array($this->apiError('Not Fount', 404));
        }

        return $this->response()->collection($items, new CityTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /field 获取领域列表
     * @apiVersion 1.0.0
     * @apiName field list
     * @apiGroup Common
     *
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *   }
     *   "data":[
     *       {
     *          "1": "领域1",
     *          "2": "领域2",
     *          "3": "领域3"
     *        }
     *      ]
     *  }
     */
    public function Field()
    {
        $field = config('constant.field');
        return $this->response()->array($this->apiSuccess('Success', 200 ,$field));
    }
}