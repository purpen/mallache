<?php

/**
 * 公共接口
 */
namespace App\Http\Controllers\Api\V1;

use Dingo\Api\Contract\Http\Request;
use App\Helper\Tools;

class OpalusController extends BaseController
{
    /**
     * @api {get} /opalus/company_record/list 获取设计公司创新力排行列表
     * @apiVersion 1.0.0
     * @apiName company_record list
     * @apiGroup Opalus
     *
     * @apiParam {string} mark 配置名称
     * @apiParam {integer} no 期数 默认1
     * @apiParam {integer} size 显示数量，默认10
     * @apiParam {integer} sort 排序：0.总分（默认）；1.基础运作； 2.商业决策； 3.创新交付； 4.品牌溢价； 5.客观公信； 6.风险应激；
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *   "data":[
           {
                "_id": "5af2cca8b24b000a484e4f24",
                "ave_score": 89,  // 总分
                "base_average": 91, // 基础运作力
                "business_average": 97, // 商业决策力
                "credit_average": 88, // 风险应激力
                "deleted": 0,
                "design_average": 67, // 品牌溢价力
                "design_company": {
                    "_id": "5a951f5fb24b006f0fe726e1",
                    "d3ing_id": 0,
                    "description": "LKK洛可可是一家专注于为客户提升产品力的创新设计集团。LKK洛可可成立于2004年，并迅速由一家工业设计公司发展成为一家实力雄厚的国际整合创新设计集团",
                    "logo_url": "https://img.tianyancha.com/logo/lll/b5b9bf8c830c6f634ba120d4e0a7ad89.png@!watermark01",
                    "name": "北京洛可可科技有限公司",
                    "scope_business": "工程勘察设计；货物进出口"
                },
                "effect_average": 95,   // 客观公信力
                "innovate_average": 99, // 创新交付力
                "mark": "plan_a", // 配置名
                "no": 3,  // 期数
                "number": "180227170535840551", // 编号
                "status": 1,
                "type": 1
            }
     *        ]
     *  }
     */
    public function getCompanyRecord(Request $request)
    {
        $mark = $request->input('mark') ? $request->input('mark') : 'plan_a';
        $no = $request->input('no') ? (int)$request->input('no') : 1;
        $size = $request->input('size') ? (int)$request->input('size') : 10;
        $sort = $request->input('sort') ? (int)$request->input('sort') : 0;
        $url = config('app.opalus_api') . 'design_record/list';

        $param = [
          'mark'=> $mark,
          'no'=> $no,
          'sort'=> $sort,
          'per_page'=> $size,
        ];
        $result = Tools::request($url, $param, 'GET');
        $result = json_decode($result, true);
        if (!isset($result['code'])) {
            return $this->response()->array($this->apiError('返回数据格式错误', 500));       
        }
        if ($result['code']) {
            return $this->response()->array($this->apiError($result['message'], 500));       
        }

        return $this->response()->array($this->apiSuccess('Success', 200 , $result['data']['rows']));
    }
}


