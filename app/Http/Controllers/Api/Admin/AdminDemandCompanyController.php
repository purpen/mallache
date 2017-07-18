<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helper\Tools;
use App\Http\AdminTransformer\DemandCompanyTransformer;
use Illuminate\Http\Request;
use App\Models\DemandCompany;
use App\Http\Controllers\Controller;


class AdminDemandCompanyController extends Controller
{
    /**
     * @api {put} /admin/demandCompany/verifyStatus 需求公司通过审核
     * @apiVersion 1.0.0
     * @apiName AdminDemandCompany verifyStatus
     * @apiGroup AdminDemandCompany
     *
     * @apiParam {integer} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *  "meta": {
     *    "code": 200,
     *    "message": "Success.",
     *  }
     * }
     */
    public function verifyStatus(Request $request)
    {
        $id = $request->input('id');
        $demand_company = DemandCompany::where('id' , $id)->first();
        if(!$demand_company){
            return $this->response->array($this->apiSuccess('需求公司不存在' , 404));
        }
        $demand = DemandCompany::verifyStatus($id , 1);
        if(!$demand){
            return $this->response->array($this->apiError('修改失败' , 500));
        }

        // 系统消息通知
        $tools = new Tools();
        $title = '公司信息审核';
        $content = '公司信息已通过审核';
        $tools->message($demand_company->user_id, $title, $content, 1, null);

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/demandCompany/unVerifyStatus 需求公司审核中
     * @apiVersion 1.0.0
     * @apiName AdminDemandCompany unVerifyStatus
     * @apiGroup AdminDemandCompany
     *
     * @apiParam {integer} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *  "meta": {
     *    "code": 200,
     *    "message": "Success.",
     *  }
     * }
     */
    public function unVerifyStatus(Request $request)
    {
        $id = $request->input('id');
        $demand_company = DemandCompany::where('id' , $id)->first();
        if(!$demand_company){
            return $this->response->array($this->apiSuccess('需求公司不存在' , 404));
        }
        $demand = DemandCompany::verifyStatus($id , 0);
        if(!$demand){
            return $this->response->array($this->apiError('修改失败' , 500));
        }
        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {put} /admin/demandCompany/noVerifyStatus 需求公司未通过审核
     * @apiVersion 1.0.0
     * @apiName AdminDemandCompany noVerifyStatus
     * @apiGroup AdminDemandCompany
     *
     * @apiParam {integer} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *  "meta": {
     *    "code": 200,
     *    "message": "Success.",
     *  }
     * }
     */
    public function noVerifyStatus(Request $request)
    {
        $id = $request->input('id');
        $demand_company = DemandCompany::where('id' , $id)->first();
        if(!$demand_company){
            return $this->response->array($this->apiSuccess('需求公司不存在' , 404));
        }
        $demand = DemandCompany::verifyStatus($id , 2);
        if(!$demand){
            return $this->response->array($this->apiError('修改失败' , 500));
        }

        // 系统消息通知
        $tools = new Tools();
        $title = '公司信息审核';
        $content = '公司信息未通过审核，请修改资料重新提交';
        $tools->message($demand_company->user_id, $title, $content, 1, null);

        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {get} /admin/demandCompany/lists 需求公司列表
     * @apiVersion 1.0.0
     * @apiName AdminDemandCompany lists
     * @apiGroup AdminDemandCompany
     *
     * @apiParam {integer} per_page 分页数量  默认15
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0.升序；1.降序（默认）
     * @apiParam {integer} type_verify_status 0.审核中；1.审核通过；2.未通过审核
     * @apiParam {string} token
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type_verify_status = in_array($request->input('type_verify_status'), [0,1,2]) ? $request->input('type_verify_status') : null;
        if($request->input('sort') == 0 && $request->input('sort') !== null)
        {
            $sort = 'asc';
        }
        else
        {
            $sort = 'desc';
        }

        $query = DemandCompany::query();
        if($type_verify_status !== null){
            $query->where('verify_status', $type_verify_status);
        }

        $lists = $query->orderBy('id', $sort)->paginate($per_page);
        return $this->response->paginator($lists , new DemandCompanyTransformer)->setMeta($this->apiMeta());

    }

    /**
     * @api {get} /admin/demandCompany/show 需求公司详情
     * @apiVersion 1.0.0
     * @apiName AdminDemandCompany show
     * @apiGroup AdminDemandCompany
     *
     * @apiParam {integer} id 需求公司ID
     * @apiParam {string} token
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        if(!$demand = DemandCompany::find($id)){
            return $this->response->array($this->apiError('not found demandcompany', 404));
        }

        return $this->response->item($demand, new DemandCompanyTransformer)->setMeta($this->apiMeta());
    }

}
