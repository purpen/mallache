<?php

namespace App\Http\Controllers\Api\Admin;

use App\Service\Statistics;
use Dingo\Api\Http\Request;
use App\Models\DesignStatistics;
use App\Models\DesignCompanyModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\StoreResourceFailedException;
use App\Http\Transformer\DesignStatisticsTransformer;
use App\Http\Transformer\DesignCompanyStatisticsTransformer;
class DesignStatisticsController extends Controller
{
    /**
     * @api {post} /admin/saveIntervene 人工干预分值修改
     * @apiVersion 1.0.0
     * @apiName saveIntervene
     * @apiGroup DesignStatistics
     * @apiParam {integer} id 设计公司id
     * @apiParam {integer} num 分值
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *         "message": "Success",
     *         "status_code": 200
     *     }
     * }
     */
    public function saveIntervene(Request $request)
    {
        $rules = [
            'id' => 'required|integer',
            'num' => 'required|integer'
        ];
        $params = $request->all();
        $validator = Validator::make($params, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        //查询设计公司信息是否存在
        $data = DesignStatistics::select('id','intervene')->where('design_company_id',$params['id'])->first();
        if(empty($data)){
            //新增一条设计公司信息
            $res = DesignStatistics::insert(['design_company_id'=>$params['id'],'intervene'=>$params['num']]);
            if(empty($res)){
                return $this->response->array($this->apiError('保存失败', 500));
            }
        }else{
            //人工干预分值
            $res = DesignStatistics::where('id',$data['id'])->update(['intervene'=>$params['num']]);
            if(empty($res)){
                return $this->response->array($this->apiError('保存失败', 500));
            }
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /admin/statistics/list 设计公司信息列表
     * @apiVersion 1.0.0
     * @apiName statisticsList
     * @apiGroup DesignStatistics
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 条数
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     * {
     * "data": [
     *     {
     *        "id": 46,
     *        "design_company_id": 10,             //设计公司id
     *        "score": 0,                          //公司评分
     *        "jump_count": 0,                     //跳单次数
     *        "level": 0,                          //人工分级
     *        "average_price": "947.00",           //接单均价
     *        "last_time": "2018-08-16 14:43",     //最近接单时间
     *        "recommend_count": 122,              //推荐次数
     *        "cooperation_count": 3,              //合作次数
     *        "success_rate": 0.0246,              //接单成功率
     *        "case": 0,                           //作品案例数
     *        "intervene": 99,                     //人工干预分值
     *        "recommend_time": "2018-08-16 14:43" //最近推荐时间
     *        "company_name": "四人行设计团队"       //设计公司名称
     *      }
     * ],
     * "meta": {
     *     "message": "Success",
     *     "status_code": 200,
     *     "pagination": {
     *         "total": 26,
     *         "count": 10,
     *         "per_page": 10,
     *         "current_page": 2,
     *         "total_pages": 3,
     *         "links": []
     *    }
     * }
     * }
     */
    public function statisticsList(Request $request)
    {
        $per_page = (int)$request->input('per_page') ?? $this->per_page;
        //排序类型
        /*$type = (int)$request->input('type') ?? 0;
        //排序
        $request->input('sort') != 0 ? $sort = 'desc' : $sort = 'asc';*/
        $statissttics = DesignStatistics::query();
        /*if($type == 1){
            //分工干预分值
            $field = 'intervene';
        }else{
            //默认主键
            $field = 'id';
        }*/
        $lists = $statissttics->orderBy('intervene','desc')->paginate($per_page);
        if(!empty($lists)){
            foreach ($lists as $key => $val){
                $company_name = DesignCompanyModel::select('company_name','contact_name','phone','address')->where('id',(int)$val->design_company_id)->first();
                if(!empty($company_name)){
                    $lists{$key}->company_name = $company_name->company_name;
                }else{
                    $lists{$key}->company_name = '';
                }
            }
        }
        return $this->response->paginator($lists, new DesignStatisticsTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /admin/test/matching 测试设计公司匹配
     * @apiVersion 1.0.0
     * @apiName testMatching
     * @apiGroup DesignStatistics
     * @apiParam {string} token
     * @apiParam {integer} city 城市
     * @apiParam {integer} province 省份
     * @apiParam {string} design_types 设计类别：[1,2,3]
     * @apiParam {integer} design_cost 设计费用：1. 1-5万；2. 5-10万；3. 10-20；4. 20-30；5. 30-50；6. 50以上
     * @apiParam {integer} type 设计类型：1.产品设计；2.UI UX 设计；3. 平面设计 4.H5 5.包装设计 6.插画设计
     * @apiSuccessExample 成功响应:
     * {
     * "data": [
     *     {
     *         "company_name": "YANG DESGIN",      //公司名称
     *         "province_value": "北京市",          //省份
     *         "city_value": "市辖区",              //城市
     *         "address": "268号",                 //详细地址
     *         "contact_name": "杨先生",            //联系人姓名
     *         "phone": "198278787",               //手机
     *         "design_statistic": {               //公司统计信息
     *              "design_company_id": 7,        //设计公司ID
     *              "score": 0,                    //公司评分
     *              "jump_count": 0,               //跳单次数
     *              "level": 0,                    //人工分级
     *              "average_price": "8.60",       //接单均价
     *              "last_time": 0,                //最近接单时间
     *              "recommend_count": 117,        //推荐次数
     *              "cooperation_count": 5,        //合作次数
     *              "success_rate": 0.0427,        //成功率
     *              "case": 3,                     //作品案例数
     *              "intervene": 0,                //人工干预分值
     *              "recommend_time": 1534331831   //最近推荐时间
     *          }
     *      }
     * ],
     * "meta": {
     *     "message": "Success",
     *     "status_code": 200,
     *     "pagination": {
     *         "total": 4,
     *         "count": 4,
     *         "per_page": 4,
     *         "current_page": 1,
     *         "total_pages": 1,
     *         "links": []
     *      }
     * }
     */
    public function testMatching(Request $request)
    {
        $params = $request->all();
        $rules = [
            'type' => 'required|integer',
            'design_types' => 'required',
            'city' => 'required|integer',
            'province' => 'required|integer',
            'design_cost' => 'required|integer'
        ];
        $validator = Validator::make($params, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $statistics = new Statistics;
        //测试设计公司匹配
        $lists = $statistics->testMatching($params);
        if(empty($lists)){
            return $this->apiSuccess('Success','200',[]);
        }
        return $this->response->paginator($lists,new DesignCompanyStatisticsTransformer)->setMeta($this->apiMeta());
    }

}
