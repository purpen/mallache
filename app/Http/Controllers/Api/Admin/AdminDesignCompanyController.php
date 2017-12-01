<?php

namespace App\Http\Controllers\Api\Admin;


use App\Helper\Tools;
use App\Http\AdminTransformer\AdminDesignCompanyTransformer;
use Illuminate\Http\Request;
use App\Models\DesignCompanyModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class AdminDesignCompanyController extends Controller
{
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
            return $this->response->array($this->apiSuccess('设计公司不存在' , 404));
        }
        $design = DesignCompanyModel::verifyStatus($id , 1);
        if(!$design){
            return $this->response->array($this->apiError('修改失败' , 500));
        }

        // 系统消息通知
        $tools = new Tools();
        $title = '公司信息审核';
        $content = '公司信息已通过审核';
        $tools->message($design_company->user_id, $title, $content, 1, null);

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
            return $this->response->array($this->apiSuccess('设计公司不存在' , 404));
        }
        $design = DesignCompanyModel::verifyStatus($id , 0);
        if(!$design){
            return $this->response->array($this->apiError('修改失败' , 500));
        }
        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {put} /admin/designCompany/noVerifyStatus 设计公司未通过审核
     * @apiVersion 1.0.0
     * @apiName AdminDesignCompany noVerifyStatus
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
    public function noVerifyStatus(Request $request)
    {
        $id = $request->input('id');
        $design_company = DesignCompanyModel::where('id' , $id)->first();
        if(!$design_company){
            return $this->response->array($this->apiSuccess('设计公司不存在' , 404));
        }
        $design = DesignCompanyModel::verifyStatus($id , 2);
        if(!$design){
            return $this->response->array($this->apiError('修改失败' , 500));
        }

        // 系统消息通知
        $tools = new Tools();
        $title = '公司信息审核';
        $content = '公司信息未通过审核，请修改资料重新提交';
        $tools->message($design_company->user_id, $title, $content, 1, null);

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
        $id = $request->input('id');
        $design_company = DesignCompanyModel::where('id' , $id)->first();

        if(!$design_company){
            return $this->response->array($this->apiSuccess('设计公司不存在' , 404));
        }
        $design = DesignCompanyModel::unStatus($id , 1);
        if(!$design){
            return $this->response->array($this->apiError('修改失败' , 500));
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
            return $this->response->array($this->apiSuccess('设计公司不存在' , 404));
        }
        $design = DesignCompanyModel::unStatus($id , 0);
        if(!$design){
            return $this->response->array($this->apiError('修改失败' , 500));
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /admin/designCompany/lists 设计公司列表
     * @apiVersion 1.0.0
     * @apiName AdminDesignCompany lists
     * @apiGroup AdminDesignCompany
     *
     * @apiParam {integer} per_page 分页数量  默认15
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0.升序；1.降序（默认）;2.推荐降序；
     * @apiParam {integer} type_status 0.禁用; 1.正常；
     * @apiParam {integer} type_verify_status 0.审核中；1.审核通过；2.未通过审核
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
         * {[
            {
            "id": 1,
            "user_id": 1,
            "company_type": 1,
            "company_type_val": "普通",
            "company_name": "测试设计公司",
            "company_abbreviation": "",
            "registration_number": "12344556",
            "province": 1,
            "city": 2,
            "area": 3,
            "province_value": "",
            "city_value": "",
            "area_value": "",
            "address": "北京朝阳",
            "contact_name": "小王",
            "position": "老总",
            "phone": "18629493220",
            "email": "qq@qq.com",
            "company_size": 4,
            "company_size_val": "100-300人之间",
            "branch_office": 1,
            "good_field": [
                "1",
                "2",
                "3"
            ],
            "web": "www.tai.com",
            "company_profile": "一家有价值的公司",
            "design_type": "1,2,3,4,5",
            "establishment_time": "2013-12-10",
            "professional_advantage": "专业",
            "awards": "就是专业",
            "score": 70,
            "status": 1,
            "is_recommend": 0,
            "verify_status": 1,
            "logo": 0,
            "logo_image": [],
            "license_image": [],
            "unique_id": "",
            "created_at": 1491893664,
            "users": {},
            "city_arr": [
                "",
                ""
            ],
            "legal_person": "",
            "document_type": 0,
            "document_type_val": "",
            "document_number": "",
            "document_image": [],
            "item_type": [
                "产品设计",
                "app设计",
                "网页设计"
            ],
            "open": 0   // 公司资料是否公开：0.否；1.是；
            }
        ],
        "meta": {
            "message": "Success",
            "status_code": 200,
            "pagination": {
                "total": 3,
                "count": 3,
                "per_page": 10,
                "current_page": 1,
                "total_pages": 1,
                "links": []
            }
        }
    }
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type_verify_status = $request->input('type_verify_status') ? (int)$request->input('type_verify_status') : 0;
        $type_status = in_array($request->input('type_status'), [0,1]) ? $request->input('type_status') : null;
        $sort = in_array($request->input('sort'), [0,1,2]) ? $request->input('sort') : null;

        $query = DesignCompanyModel::with('user','user.designItem');
        if($type_status !== null){
            $query->where('status', $type_status);
        }
        if($type_verify_status){
            switch($type_verify_status){
                case -1:
                    $type_verify_status = 0;
                    break;
                case 1:
                    $type_verify_status = 1;
                    break;
                default:
                    $type_verify_status = -1;
            }
            $query->where('verify_status', $type_verify_status);
        }

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

        $lists = $query->orderBy('id', $sort)->paginate($per_page);
        return $this->response->paginator($lists , new AdminDesignCompanyTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /admin/designCompany/openInfo 公开或关闭设计公司资料
     * @apiVersion 1.0.0
     * @apiName AdminDesignCompany openInfo
     * @apiGroup AdminDesignCompany
     *
     * @apiParam {integer} design_id 设计公司id
     * @apiParam {integer} is_open 参数：0.关闭；1.公开
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
    public function openInfo(Request $request)
    {
        $this->validate($request, [
            'design_id' => 'required|integer',
            'is_open' => ['required', Rule::in([0, 1])],
        ]);

        $design_id = $request->input('design_id');
        $is_open = $request->input('is_open');

        if(!$design_company = DesignCompanyModel::find($design_id)){
            return $this->response->array($this->apiError('not found', 404));
        }

        $design_company->open = 1;
        if(!$design_company->save()){
            return $this->response->array($this->apiError('error', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /admin/designCompany/show 设计公司详细信息
     * @apiVersion 1.0.0
     * @apiName AdminDesignCompany show
     * @apiGroup AdminDesignCompany
     *
     * @apiParam {integer} id 设计公司ID
     * @apiParam {string} token
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        if(!$design = DesignCompanyModel::find($id)){
            return $this->response->array($this->apiError('not found design company', 404));
        }

        return $this->response->item($design, new AdminDesignCompanyTransformer)->setMeta($this->apiMeta());
    }
}
