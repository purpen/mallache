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
     * @api {put} /admin/designCompany/verifyStatus 设计公司信息审核
     * @apiVersion 1.0.0
     * @apiName AdminDesignCompany verifyStatus
     * @apiGroup AdminDesignCompany
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
        $this->validate($request, [
            'status' => 'required',
            'id' => 'required',
        ]);
        $id = $request->input('id');
        $status = $request->input('status');
        $verify_summary = $request->input('verify_summary', '');

        if (!in_array($status, [0, 1, 2, 3])) {
            return $this->response->array($this->apiSuccess('状态参数错误', 403));
        }

        $design_company = DesignCompanyModel::where('id', $id)->first();
        if (!$design_company) {
            return $this->response->array($this->apiSuccess('设计公司不存在', 404));
        }

        $design = DesignCompanyModel::verifyStatus($id, $status, $verify_summary);
        if (!$design) {
            return $this->response->array($this->apiError('修改失败', 500));
        }

        // 系统消息通知
        $tools = new Tools();
        $title = '公司信息审核';
        $content = '';
        switch ($status) {
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
        $tools->message($design_company->user_id, $title, $content, 1, null);

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/designCompany/unVerifyStatus 设计公司审核中（停用）
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
        $design_company = DesignCompanyModel::where('id', $id)->first();
        if (!$design_company) {
            return $this->response->array($this->apiSuccess('设计公司不存在', 404));
        }
        $design = DesignCompanyModel::verifyStatus($id, 0);
        if (!$design) {
            return $this->response->array($this->apiError('修改失败', 500));
        }
        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {put} /admin/designCompany/noVerifyStatus 设计公司未通过审核（停用）
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
        $design_company = DesignCompanyModel::where('id', $id)->first();
        if (!$design_company) {
            return $this->response->array($this->apiSuccess('设计公司不存在', 404));
        }
        $design = DesignCompanyModel::verifyStatus($id, 2);
        if (!$design) {
            return $this->response->array($this->apiError('修改失败', 500));
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
        $design_company = DesignCompanyModel::where('id', $id)->first();

        if (!$design_company) {
            return $this->response->array($this->apiSuccess('设计公司不存在', 404));
        }
        $design = DesignCompanyModel::unStatus($id, 1);
        if (!$design) {
            return $this->response->array($this->apiError('修改失败', 500));
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
        $design_company = DesignCompanyModel::where('id', $id)->first();
        if (!$design_company) {
            return $this->response->array($this->apiSuccess('设计公司不存在', 404));
        }
        $design = DesignCompanyModel::unStatus($id, 0);
        if (!$design) {
            return $this->response->array($this->apiError('修改失败', 500));
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
     * @apiParam {integer} type_verify_status 0.未审核；1.审核通过；2.未通过审核 3.审核中
     * @apiParam {integer} evt 查询条件：1.ID; 2.公司名称；3.短名称；4.用户ID；5.--；
     * @apiParam {integer} val 查询值（根据查询条件判断）
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     * {[
     * {
     * "id": 1,
     * "user_id": 1,
     * "company_type": 1,
     * "company_type_val": "普通",
     * "company_name": "测试设计公司",
     * "company_abbreviation": "",
     * "registration_number": "12344556",
     * "province": 1,
     * "city": 2,
     * "area": 3,
     * "province_value": "",
     * "city_value": "",
     * "area_value": "",
     * "address": "北京朝阳",
     * "contact_name": "小王",
     * "position": "老总",
     * "phone": "18629493220",
     * "email": "qq@qq.com",
     * "company_size": 4,
     * "company_size_val": "100-300人之间",
     * "branch_office": 1,
     * "good_field": [
     * "1",
     * "2",
     * "3"
     * ],
     * "web": "www.tai.com",
     * "company_profile": "一家有价值的公司",
     * "design_type": "1,2,3,4,5",
     * "establishment_time": "2013-12-10",
     * "professional_advantage": "专业",
     * "awards": "就是专业",
     * "score": 70,
     * "status": 1,
     * "is_recommend": 0,
     * "verify_status": 1,
     * "logo": 0,
     * "logo_image": [],
     * "license_image": [],
     * "unique_id": "",
     * "created_at": 1491893664,
     * "users": {},
     * "city_arr": [
     * "",
     * ""
     * ],
     * "legal_person": "",
     * "document_type": 0,
     * "document_type_val": "",
     * "document_number": "",
     * "document_image": [],
     * "item_type": [
     * "产品设计",
     * "app设计",
     * "网页设计"
     * ],
     * "open": 0   // 公司资料是否公开：0.否；1.是；
     *      "is_test_data": 0  // 是否是测试数据 0.真实数据 1.测试数据
     * }
     * ],
     * "meta": {
     * "message": "Success",
     * "status_code": 200,
     * "pagination": {
     * "total": 3,
     * "count": 3,
     * "per_page": 10,
     * "current_page": 1,
     * "total_pages": 1,
     * "links": []
     * }
     * }
     * }
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type_verify_status = in_array($request->input('type_verify_status'), [0, 1, 2, 3]) ? $request->input('type_verify_status') : null;
        $type_status = in_array($request->input('type_status'), [0, 1]) ? $request->input('type_status') : null;
        $sort = in_array($request->input('sort'), [0, 1, 2]) ? $request->input('sort') : 0;
        $evt = $request->input('evt') ? (int)$request->input('evt') : 1;
        $val = $request->input('val') ? $request->input('val') : '';

        $query = DesignCompanyModel::with('user', 'user.designItem');
        if ($type_status !== null && $type_status !== '') {
            $query->where('status', $type_status);
        }
        if ($type_verify_status !== null && $type_verify_status !== '') {
            $query->where('verify_status', $type_verify_status);
        }

        if ($val) {
            switch ($evt) {
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
        switch ($sort) {
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
        return $this->response->paginator($lists, new AdminDesignCompanyTransformer)->setMeta($this->apiMeta());
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

        if (!$design_company = DesignCompanyModel::find($design_id)) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $design_company->open = 1;
        if (!$design_company->save()) {
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
        if (!$design = DesignCompanyModel::find($id)) {
            return $this->response->array($this->apiError('not found design company', 404));
        }

        return $this->response->item($design, new AdminDesignCompanyTransformer)->setMeta($this->apiMeta());
    }


    /**
     * @api {put} /admin/designCompany/isTest 设置设计公司是否是测试数据
     * @apiVersion 1.0.0
     * @apiName AdminDesignCompany isTest
     * @apiGroup AdminDesignCompany
     *
     * @apiParam {integer} id 设计公司ID
     * @apiParam {integer} is_test_data 是否是测试数据 0.真实数据 1.测试数据
     * @apiParam {string} token
     */
    public function setIsTestData(Request $request)
    {
        $id = $request->input('id');
        $is_test_data = $request->input('is_test_data');

        $design = DesignCompanyModel::find($id);
        if (!$design) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($is_test_data) {
            $design->is_test_data = 1;
        } else {
            $design->is_test_data = 0;
        }
        $design->save();

        return $this->response->array($this->apiSuccess());
    }
}
