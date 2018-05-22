<?php

namespace App\Http\Controllers\Api\Opalus;


use App\Helper\Tools;
use App\Http\AdminTransformer\DesignCaseTransformer;
use Illuminate\Http\Request;
use App\Models\DesignCaseModel;
use App\Models\DesignCompanyModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;


class DesignCaseController extends Controller
{

    /**
     * @api {get} /opalus/design_case/list  设计公司案例列表
     * @apiVersion 1.0.0
     * @apiName OpalusDesignCase list
     * @apiGroup OpalusDesignCase
     *
     * @apiParam {integer} page 页码
     * @apiParam {integer} per_page  页面数量
     * @apiParam {integer} sort 创建时间排序 0.创建时间正序；1.创建时间倒序；2.推荐倒序；
     * @apiParam {integer} type 0.全部；-1.未公开；1.公开；
     * @apiParam {integer} status 状态 0.未审核；1.已审核；默认：全部；
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
        $per_page = $request->input('per_page') ?? 100;
        $sort = $request->input('sort') ?? 0;
        $type = $request->input('type') ?? 0;
        $status = $request->input('status') ?? 0;
        $design_company_id = $request->input('design_company_id') ?? null;

        $query = DesignCaseModel::with('DesignCompany');

        if ($design_company_id) {
            $query->where('design_company_id', $design_company_id);
        }

        // 类型
        switch ($type){
            case -1:
                $query->where('open', 0);
                break;
            case 1:
                $query->where('open', 1);
                break;

        }

        // 状态
        switch ($status){
            case null:
                break;
            case -1:
                $query->where('status', 0);
                break;
            case 1:
                $query->where('status', 1);
                break;
        }

        //排序
        switch ($sort){
            case 0:
                $query->orderBy('id', 'desc');
                break;
            case 1:
                $query->orderBy('id', 'asc');
                break;
            case 2:
                $query->orderBy('open_time', 'desc');
            break;
        }

        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new DesignCaseTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /opalus/design_case/show 案例详情
     * @apiVersion 1.0.0
     * @apiName OpalusDesignCase show
     * @apiGroup OpalusDesignCase
     *
     * @apiParam {integer} id ID
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        if(!$design = DesignCaseModel::find($id)){
            return $this->response->array($this->apiError('not found design case', 404));
        }

        return $this->response->item($design, new DesignCaseTransformer)->setMeta($this->apiMeta());
    }
}
