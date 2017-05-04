<?php

namespace App\Http\Controllers\Api\Admin;


use Illuminate\Http\Request;
use App\Models\DesignCompanyModel;
use Illuminate\Support\Facades\Log;


class AdminDesignCompanyController extends BaseController
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
     * @param  \App\Models\DesignCompanyModel  $designCompanyModel
     * @return \Illuminate\Http\Response
     */
    public function edit(DesignCompanyModel $designCompanyModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DesignCompanyModel  $designCompanyModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(DesignCompanyModel $designCompanyModel)
    {
        //
    }


    /**
     * @api {put} /admin/designCompany/verifyStatus 设计公司通过审核
     * @apiVersion 1.0.0
     * @apiName AdminDesignCompany verifyStatus
     * @apiGroup AdminDesignCompany
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
        $design_company = DesignCompanyModel::where('id' , $id)->first();
        if(!$design_company){
            return $this->response->array($this->apiSuccess('设计公司不存在' , 200));
        }
        $design = DesignCompanyModel::verifyStatus($id , 1);
        if(!$design){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/designCompany/unVerifyStatus 设计公司审核中
     * @apiVersion 1.0.0
     * @apiName AdminDesignCompany unVerifyStatus
     * @apiGroup AdminDesignCompany
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
        $design_company = DesignCompanyModel::where('id' , $id)->first();
        if(!$design_company){
            return $this->response->array($this->apiSuccess('设计公司不存在' , 200));
        }
        $design = DesignCompanyModel::verifyStatus($id , 0);
        if(!$design){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {put} /admin/designCompany/okStatus 设计公司正常
     * @apiVersion 1.0.0
     * @apiName AdminDesignCompany okStatus
     * @apiGroup AdminDesignCompany
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
    public function okStatus(Request $request)
    {
        Log::info(111);
        $id = $request->input('id');
        $design_company = DesignCompanyModel::where('id' , $id)->first();
        Log::info($design_company);
        Log::info(222);

        if(!$design_company){
            return $this->response->array($this->apiSuccess('设计公司不存在' , 200));
        }
        $design = DesignCompanyModel::unStatus($id , 0);
        if(!$design){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/designCompany/unStatus 设计公司禁用
     * @apiVersion 1.0.0
     * @apiName AdminDesignCompany unStatus
     * @apiGroup AdminDesignCompany
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
    public function unStatus(Request $request)
    {
        $id = $request->input('id');
        $design_company = DesignCompanyModel::where('id' , $id)->first();
        if(!$design_company){
            return $this->response->array($this->apiSuccess('设计公司不存在' , 200));
        }
        $design = DesignCompanyModel::unStatus($id , -1);
        if(!$design){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->array($this->apiSuccess());
    }
}
