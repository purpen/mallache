<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\AdminTransformer\DesignCaseTransformer;
use App\Models\DesignCaseModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DesignCaseController extends BaseController
{
    /**
     * @api {get} /admin/designCase/lists  设计公司案例列表
     * @apiVersion 1.0.0
     * @apiName designCase
     * @apiGroup AdminDesignCase
     *
     * @apiParam {integer} page 页码
     * @apiParam {integer} per_page  页面数量
     * @apiParam {integer} sort 创建时间排序 0.创建时间正序；1.创建时间倒序；2.推荐倒序；
     * @apiParam {integer} type 1.全部；2.未公开；3.公开；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": [
     *  {
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
     *      "other_prize": "",
     *      "mass_production": 1,
     *      "design_company":{},
     *  }
     * ],
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     *
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $sort = $request->input('sort');
        $type = $request->input('type');

        $query = DesignCaseModel::with('DesignCompany');

        //排序
        switch ($sort){
            case 0:
                $query->orderBy('id', 'asc');
                break;
            case 1:
                $query->orderBy('id', 'desc');
                break;
            case 2:
                $query->orderBy('open_time', 'desc');
            break;
        }

        // 类型
        switch ($type){
            case 2:
                $query->where('open', 0);
                break;
            case 3:
                $query->where('open', 1);
                break;

        }

        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new DesignCaseTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /admin/designCase/openInfo  开放设计案例
     * @apiVersion 1.0.0
     * @apiName designCase openInfo
     * @apiGroup AdminDesignCase
     *
     * @apiParam {integer} case_id 案例ID
     * @apiParam {integer} is_open 是否开发 0.否；1.是；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     *
     */
    public function openInfo(Request $request)
    {
        $this->validate($request, [
            'case_id' => 'required|integer',
            'is_open' => ['required', Rule::in([0, 1])],
        ]);

        $case_id = $request->input('case_id');
        $is_open = $request->input('is_open');
        if(!$case = DesignCaseModel::find($case_id)){
            return $this->response->array($this->apiError('not found', 404));
        }

        $case->open = $is_open;
        $case->open_time = date("Y-m-d H:i:s");
        $case->save();

        return $this->response->array($this->apiSuccess());
    }

}