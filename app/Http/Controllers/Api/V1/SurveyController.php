<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;

class SurveyController extends BaseController
{
    /**
     * @api {get} /survey/designCompanySurvey 设计公司信息概况
     * @apiVersion 1.0.0
     * @apiName survey designCompanySurvey
     * @apiGroup survey
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     * "data": {
     * 'design_info_status' => 1,        // 设计公司基础信息 0：未完善；1.已完善
     * 'design_verify_status' => 1,      // 设计公司审核信息 审核状态：0.审核中；1.审核通过；2. 未通过审核
     * 'design_case_status' => 1,        // 设计案例是否添加 0：未完善；1.已完善
     * 'design_item_status' => 1,        // 接单设置是否添加 0：未完善；1.已完善
     * },
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function designCompanySurvey()
    {
        $design_info_status = 1;        // 设计公司基础信息
        $design_verify_status = 1;      // 设计公司审核信息
        $design_case_status = 1;        // 设计案例是否添加
        $design_item_status = 1;        // 接单设置是否添加

        if (!$design_company = $this->auth_user->designCompany) {
            return $this->response->array($this->apiError('无权限', 402));
        }

        // 设计公司基本信息
        $design_info = [
            'company_abbreviation',
            'province',
            'city',
            'area',
            'address',
            'company_size',
            'professional_advantage',
            'company_profile',
            'awards',
            'web',
        ];

        foreach ($design_info as $v) {
            if (empty($design_company->$v)) {
                $design_info_status = 0;
                break;
            }
        }

        $design_verify_status = $design_company->verify_status;

        if ($design_company->designCase->isEmpty()) {
            $design_case_status = 0;
        }

        if ($this->auth_user->designItem->isEmpty()) {
            $design_item_status = 0;
        }

        return $this->response->array($this->apiSuccess('Success', 200, compact('design_info_status', 'design_verify_status', 'design_case_status', 'design_item_status')));
    }

    /**
     * @api {get} /survey/demandCompanySurvey 需求公司信息概况
     * @apiVersion 1.0.0
     * @apiName survey demandCompanySurvey
     * @apiGroup survey
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     * "data": {
     * 'demand_info_status' => 1,        // 需求公司基础信息 0：未完善；1.已完善
     * 'demand_verify_status' => 1,      // 需求公司审核信息 审核状态：0.审核中；1.审核通过；2. 未通过审核
     * },
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function demandCompanySurvey()
    {
        $demand_info_status = 1;        // 需求公司基础信息
        $demand_verify_status = 1;      // 需求公司审核信息

        // 需求公司基本信息
        $demand_info = [
            'company_abbreviation',
            'province',
            'city',
            'area',
            'address',
            'company_size',
            'company_property',
            'company_web',
        ];

        if (!$demand_company = $this->auth_user->demandCompany) {
            return $this->response->array($this->apiError('无权限', 402));
        }

        foreach ($demand_info as $v){
            if(empty($demand_company->$v)){
                $demand_info_status = 0;
                break;
            }
        }

        $demand_verify_status = $demand_company->verify_status;

        return $this->response->array($this->apiSuccess('Success', 200, compact('demand_info_status', 'demand_verify_status')));
    }
}