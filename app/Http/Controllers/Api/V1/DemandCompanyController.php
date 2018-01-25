<?php
/**
 * 需求公司基本信息控制器
 *
 * @User llh
 * @time 2017-3-31
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DemandCompanyTransformer;
use App\Models\AssetModel;
use App\Models\DemandCompany;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DemandCompanyController extends BaseController
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * @api {post} /demandCompany 保存需求用户信息(停用)
     * @apiVersion 1.0.0
     * @apiName demandCompany store
     * @apiGroup demandCompany
     *
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} company_abbreviation 公司简称
     * @apiParam {integer} company_size 公司规模
     * @apiParam {string} company_web 公司网站
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} position 职位
     * @apiParam {string} contact_name 联系人
     * @apiParam {integer} phone 手机
     * @apiParam {string} email 邮箱
     * @apiParam {string} random 图片上传随机数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *   "data": {
     *          "id": 1,
     *          "company_name": null,  //公司名称
                "company_abbreviation": null, //简称
                "company_size": null, //公司规模；1...
                "company_web": null,  //公司网址
                "company_province": null, //省份
                "company_city": null,  //城市
                "company_area": null,   //区县
                "address": null,    //详细地址
 *              "position"：''  //职位
                "contact_name": null,   //联系人
                "phone": null,
                "email": null
     *    }
     *  }
     */
    public function store(Request $request)
    {
        //表单验证
        $rules = [
            'company_name' => 'required|max:50',
            'company_abbreviation' => 'required|max:50',
            'company_size' => 'required|integer',
            'company_web' => 'required|max:50',
            'province' => 'required|integer',
            'city' => 'integer',
            'area' => 'integer',
            'address' => 'required|max:50',
            'contact_name' => 'required|max:20',
            'phone' => 'required',
            'email' => 'required|email',
            'position' => 'required|max:20',
            'registration_number' => 'min:15|max:18',
        ];
        $all = $request->all();
        $all['user_id'] = $this->auth_user_id;
        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $asset = new AssetModel();
        $logo_id = $asset->getAssetId(7, $request->input('random'));
        $all['logo'] = $logo_id;

        try{
            $user = User::where('id' , $this->auth_user_id)->first();
            $demand = DemandCompany::create($all);
            $user->demand_company_id = $demand->id;
            if($demand){
                $user->save();
            }
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->item($demand, new DemandCompanyTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /demandCompany 获取需求用户信息
     * @apiVersion 1.0.0
     * @apiName demandCompany show
     * @apiGroup demandCompany
     *
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
    public function show()
    {
        $demand = DemandCompany::where('user_id', $this->auth_user_id)->first();
        if(!$demand){
            return $this->response->array($this->apiSuccess('Success', 200, []));
        }

        return $this->response->item($demand, new DemandCompanyTransformer)->setMeta($this->apiMeta());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * @api {post} /demandCompany 更新需求用户信息
     * @apiVersion 1.0.0
     * @apiName demandCompany update
     * @apiGroup demandCompany
     *
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} company_abbreviation 公司简称
     * @apiParam {integer} company_size 公司规模
     * @apiParam {string} company_web 公司网站
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} position 职位
     * @apiParam {string} contact_name 联系人
     * @apiParam {integer} phone 手机
     * @apiParam {string} email 邮箱
     * @apiParam {integer} company_type 企业类型：1.普通；2.多证合一（不含社会统一信用代码）；3.多证合一（含社会统一信用代码）
     * @apiParam {string} registration_number 注册号
     * @apiParam {string}   legal_person 法人姓名
     * @apiParam {integer}   document_type 法人证件类型：1.身份证；2.港澳通行证；3.台胞证；4.护照；
     * @apiParam {string}   document_number 证件号码
     * @apiParam {integer}   company_property 企业性质：1.初创企业、2.私企、3.国有企业、4.事业单位、5.外资、6.合资、7.上市公司
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     },
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
     *          "document_image":[],  //法人证件，
     *          "verify_summary": '',  // 审核备注
     *      },
     *   }
     *  }
     */
    public function update(Request $request)
    {
        $rules = [
            'company_name' => 'max:50',
            'company_abbreviation' => 'max:50',
            'company_size' => 'integer',
            'company_web' => 'max:50',
            'province' => 'integer',
            'city' => 'integer',
            'area' => 'integer',
            'address' => 'max:50',
            'contact_name' => 'max:20',
            'email' => 'email',
            'position' => 'max:20',
            'company_type' => 'integer',
            'registration_number' => 'min:15|max:18',
            'legal_person' => 'max:20',
            'document_type' => 'integer',
            'document_number' => 'max:20',
            'company_property' => 'integer',
        ];
        $all = $request->except(['token']);

        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        foreach($all as $k => $v){
            if(empty($v) && $v !== 0 && $v !== "0")
                unset($all[$k]);
        }

        // 判断是否修改需要审核的信息
        $verify = [
            'company_name',
            'document_type',
            'registration_number',
            'legal_person',
            'document_type',
            'document_number',
            'contact_name',
            'position',
            'phone',
            'email'
        ];
        if(!empty(array_intersect($verify, array_keys($all)))){
            $all['verify_status'] = 3;
        }

        $demand = DemandCompany::where('user_id', $this->auth_user_id)->first();

        $demand->update($all);
        if(!$demand){
            return $this->response->array($this->apiError());
        }

        return $this->response->item($demand, new DemandCompanyTransformer)->setMeta($this->apiMeta());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
