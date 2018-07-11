<?php

namespace App\Http\Controllers\Api\Jd;

use App\Http\JdTransformer\DemandCompanyTransformer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\DemandCompany;
use App\Http\Controllers\Controller;


class JdDemandCompanyController extends Controller
{
    /**
     * @api {get} /jd/demandCompany/lists 需求公司列表
     * @apiVersion 1.0.0
     * @apiName JdDemandCompany lists
     * @apiGroup JdDemandCompany
     *
     * @apiParam {integer} per_page 分页数量  默认15
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0.升序；1.降序（默认）
     * @apiParam {integer} type_verify_status 0.审核中；1.审核通过；2.未通过审核 3.审核中
     * @apiParam {integer} evt 查询条件：1.ID; 2.公司名称；3.短名称；4.用户ID；5.--；
     * @apiParam {string} val 查询值
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
        $login_user_id = $this->auth_user_id;
        $login_user = User::find($login_user_id);
        if($login_user->source_admin != 1){
            return $this->response->array($this->apiSuccess('登陆用户没有权限查看', 403));
        }
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type_verify_status = in_array($request->input('type_verify_status'), [0, 1, 2, 3]) ? $request->input('type_verify_status') : null;
        $sort = $request->input('sort') ? (int)$request->input('sort') : 0;
        $evt = $request->input('evt') ? (int)$request->input('evt') : 1;
        $val = $request->input('val') ? $request->input('val') : '';

        $query = DemandCompany::query()->with('user');
        if ($type_verify_status !== null && $type_verify_status !== '') {
            $query->where('verify_status', $type_verify_status);
        }

        if ($val) {
            switch ($evt) {
                case 1:
                    $query->where('id', (int)$val);
                    break;
                case 2:
                    $query->where('company_name', 'like', '%' . $val . '%');
                    break;
                case 3:
                    $query->where('company_abbreviation', 'like', '%' . $val . '%');
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
        }

        $lists = $query->paginate($per_page);
        return $this->response->paginator($lists, new DemandCompanyTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /jd/demandCompany/show 需求公司详情
     * @apiVersion 1.0.0
     * @apiName JdDemandCompany show
     * @apiGroup JdDemandCompany
     *
     * @apiParam {integer} id 需求公司ID
     * @apiParam {string} token
     */
    public function show(Request $request)
    {
        $login_user_id = $this->auth_user_id;
        $login_user = User::find($login_user_id);
        if($login_user->source_admin != 1){
            return $this->response->array($this->apiSuccess('登陆用户没有权限查看', 403));
        }
        $id = $request->input('id');
        if (!$demand = DemandCompany::find($id)) {
            return $this->response->array($this->apiError('not found demandcompany', 404));
        }

        return $this->response->item($demand, new DemandCompanyTransformer())->setMeta($this->apiMeta());
    }

}
