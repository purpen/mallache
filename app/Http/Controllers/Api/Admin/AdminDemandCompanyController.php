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
     * @api {put} /admin/demandCompany/verifyStatus 需求公司信息审核
     * @apiVersion 1.0.0
     * @apiName AdminDemandCompany verifyStatus
     * @apiGroup AdminDemandCompany
     *
     * @apiParam {integer} id
     * @apiParam {integer} status 0: 未审核 1: 成功 2: 失败 3: 审核中
     * @apiParam {string} verify_summary 审核备注
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
        $this->validate($request,[
            'status' => 'required',
            'id' => 'required',
        ]);
        $id = $request->input('id');
        $status = $request->input('status');
        $verify_summary = $request->input('verify_summary','');

        $demand_company = DemandCompany::where('id' , $id)->first();
        if(!$demand_company){
            return $this->response->array($this->apiSuccess('需求公司不存在' , 404));
        }

        if(!in_array($status,[0,1,2,3])){
            return $this->response->array($this->apiSuccess('状态参数错误' , 403));
        }

        $demand = DemandCompany::verifyStatus($id , $status, $verify_summary);
        if(!$demand){
            return $this->response->array($this->apiError('修改失败' , 500));
        }

        // 系统消息通知
        $tools = new Tools();
        $title = '公司信息审核';
        $content = '';
        switch ($status){
            case 0:
                $content = '公司信息变更为未审核状态，请修改资料重新提交';
                break;
            case 1:
                $content = '公司信息已通过审核';
                break;
            case 2:
                $content = '公司信息未通过审核，请修改资料重新提交';
                break;
            case 3:
                $content = '公司信息正在审核中';
                break;
        }

        $tools->message($demand_company->user_id, $title, $content, 1, null);

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/demandCompany/unVerifyStatus 需求公司审核中(停用)
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
     * @api {put} /admin/demandCompany/noVerifyStatus 需求公司未通过审核（停用）
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
     * @apiParam {integer} type_verify_status 0.审核中；1.审核通过；2.未通过审核 3.审核中
     * @apiParam {integer} evt 查询条件：1.ID; 2.公司名称；3.短名称；4.用户ID；5.--；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *          "id": 1,
     *          "company_name": "nihao",
     *          "company_size": 1,
     *          "company_web": "http://www.baidu.com",
     *          "province": 1,
     *          "city": 4,
     *          "area": 5,
     *          "address": "beijing",
     *          "contact_name": "lisna",
     *          "phone": 18629493221,
     *          "email": "qq@qq.com",
     *          "logo_image": [],
     *          "verify_status": 0, //审核状态
     *          "license_image": [],  //营业执照附件
     *          "position": "",     //职位
     *          "company_type": 0,  // 企业类型：1.普通；2.多证合一（不含社会统一信用代码）；3.多证合一（含社会统一信用代码）
     *          "company_type_value": "",
     *          "registration_number": "",  //注册号
     *          "legal_person": "",         //法人姓名
     *          "document_type": 0,        //法人证件类型：1.身份证；2.港澳通行证；3.台胞证；4.护照；
     *          "document_type_value": "",
     *          "document_number": "",     //证件号码
     *          "company_property": 0,     //企业性质：1.初创企业、2.私企、3.国有企业、4.事业单位、5.外资、6.合资、7.上市公司
     *          "company_property_value": ""
     *          "document_image":[],  //法人证件
     *          "verify_summary": '',  // 审核备注
     *      },
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type_verify_status = in_array($request->input('type_verify_status'), [0,1,2,3]) ? $request->input('type_verify_status') : null;
        $sort = $request->input('sort') ? (int)$request->input('sort') : 0;
        $evt = $request->input('evt') ? (int)$request->input('evt') : 1;
        $val = $request->input('val') ? $request->input('val') : '';

        $query = DemandCompany::query()->with('user');
        if($type_verify_status !== null && $type_verify_status !== ''){
            $query->where('verify_status', $type_verify_status);
        }

        if ($val) {
            switch($evt) {
                case 1:
                    $query->where('id', (int)$val);
                    break;
                case 2:
                    $query->where('company_name', $val);
                    break;
                case 3:
                    $query->where('company_abbreviation', $val);
                    break;
                case 4:
                    $query->where('user_id', (int)$val);
                    break;
                default:
                    $query->where('id', (int)$val);
            }
        }

        //排序
        switch ($sort){
            case 0:
                $query->orderBy('id', 'desc');
                break;
            case 1:
                $query->orderBy('id', 'asc');
                break;
        }

        $lists = $query->paginate($per_page);
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
