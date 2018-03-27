<?php

namespace App\Http\Controllers\Api\Opalus;


use App\Helper\Tools;
use App\Http\AdminTransformer\AdminDesignCompanyTransformer;
use Illuminate\Http\Request;
use App\Models\DesignCompanyModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;


class CompanyController extends Controller
{

    /**
     * @api {get} /opalus/company/list 设计公司列表
     * @apiVersion 1.0.0
     * @apiName OpalusDesignCompany list
     * @apiGroup OpalusDesignCompany
     *
     * @apiParam {integer} per_page 分页数量  默认100
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
        $per_page = $request->input('per_page') ?? 100;
        $type_verify_status = in_array($request->input('type_verify_status'), [0,1,2,3]) ? $request->input('type_verify_status') : null;
        $type_status = in_array($request->input('type_status'), [0,1]) ? $request->input('type_status') : null;
        $sort = in_array($request->input('sort'), [0,1,2]) ? $request->input('sort') : 0;
        $evt = $request->input('evt') ? (int)$request->input('evt') : 1;
        $val = $request->input('val') ? $request->input('val') : '';

        $query = DesignCompanyModel::with('user','user.designItem');
        if($type_status !== null && $type_status !== ''){
            $query->where('status', $type_status);
        }
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
            case 2:
                $query->orderBy('open_time', 'desc');
                break;
        }

        $lists = $query->paginate($per_page);
        return $this->response->paginator($lists , new AdminDesignCompanyTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /opalus/company/show 设计公司详情
     * @apiVersion 1.0.0
     * @apiName OpalusDesignCompany show
     * @apiGroup OpalusDesignCompany
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
