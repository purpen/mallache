<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\Transformer\DemandCompanyTransformer;
use Illuminate\Http\Request;
use App\Models\DemandCompany;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class AdminDemandCompanyController extends Controller
{
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
     */
    public function create()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }


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
        return $this->response->paginator($lists , new DemandCompanyTransformer())->setMeta($this->apiMeta());

    }
}
