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
}
