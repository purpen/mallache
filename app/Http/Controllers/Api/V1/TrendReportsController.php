<?php

namespace App\Http\Controllers\Api\V1;



use App\Http\Transformer\TrendReportsTransformer;
use App\Models\AssetModel;
use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\TrendReports;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrendReportsController extends BaseController
{
    /**
     * @api {get} /trendReports 趋势报告详情
     * @apiVersion 1.0.0
     * @apiName trendReports trendReports
     * @apiGroup TrendReports
     *
     * @apiParam {integer} id 趋势报告id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "data": {
     *          "title": "这是第一篇",
     *          "cover_id": 1,
     *          "image": []
     *          "verify_status": 1 //只有等于1的情况下，才能下载
     *      },
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function show(Request $request)
    {
        //
        $type = $this->auth_user->type;
        if($type == 1){
            $design_company = $this->auth_user->design_company_id;
            if(!$design_company){
                return $this->response->array($this->apiError('not found design_company', 404));
            }
            $design_company = DesignCompanyModel::where('id' , $design_company)->first();
            $verify_status = $design_company->verify_status;
        }else{
            $demand_company = $this->auth_user->demand_company_id;
            if(!$demand_company){
                return $this->response->array($this->apiError('not found demand_company', 404));
            }
            $demand_company = DemandCompany::where('id' , $demand_company)->first();
            $verify_status = $demand_company->verify_status;

        }
        $id = $request->input('id');

        $trendReports = TrendReports::find($id);
        $trendReports->hits += 1;
        $trendReports->save();
        $trendReports->verify_status = $verify_status;
        if (!$trendReports) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($trendReports, new TrendReportsTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /trendReports/lists 趋势报告列表
     * @apiVersion 1.0.0
     * @apiName trendReports trendReportsLists
     * @apiGroup TrendReports
     *
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;

        $query = TrendReports::query();

        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new TrendReportsTransformer())->setMeta($this->apiMeta());
    }


}