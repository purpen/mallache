<?php
namespace App\Http\Controllers\Api\Admin;

use App\Models\Weight;
use Dingo\Api\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\StoreResourceFailedException;

class WeightController extends Controller
{
    /**
     * @api {post} /admin/weight/save 保存权重
     * @apiVersion 1.0.0
     * @apiName Weight Preservation
     * @apiGroup Weight
     * @apiParam {integer} case 案列数量
     * @apiParam {integer} area 地区
     * @apiParam {integer} score 评分
     * @apiParam {integer} last_time 最近推荐时间
     * @apiParam {integer} success_rate 成功率
     * @apiParam {integer} average_price 接单均价
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     * {
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function save(Request $request)
    {
        $rules = [
            'case' => 'required|integer',
            'area' => 'required|integer',
            'score' => 'required|integer',
            'last_time' => 'required|integer',
            'success_rate' => 'required|integer',
            'average_price' => 'required|integer',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $list['area'] = $request['area'];
        $list['case'] = $request['case'];
        $list['score'] = $request['score'];
        $list['last_time'] = $request['last_time'];
        $list['success_rate'] = $request['success_rate'];
        $list['average_price'] = $request['average_price'];
        $weight = new Weight;
        $wight = 0;
        foreach ($list as $v){
            $wight += $v;
        }
        if($wight != 100){
            return $this->response->array($this->apiError('权重不等于100', 403));
        }
        $res = $weight->saveWeight($list);
        if(!empty($res)){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->array($this->apiError('保存失败', 500));
    }

    /**
     * @api {get} /admin/weight/show 权重详情
     * @apiVersion 1.0.0
     * @apiName Weight details
     * @apiGroup Weight
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * },
     * "data": {
     * "score": 10, //评价
     * "case": 20, //案例
     * "last_time": 30, //最近推荐时间
     * "success_rate": 10, //接单成功率
     * "average_price": 10, //接单单价
     * "area": 20 //地区
     * }
     * }
     */
    public function show()
    {
        $weight = new Weight;
        $res = $weight->getWeight();
        if(!empty($res)){
            return $this->response->array($this->apiSuccess('Success',200,$res));
        }
        $list['area'] = 0;
        $list['case'] = 0;
        $list['score'] = 0;
        $list['last_time'] = 0;
        $list['success_rate'] = 0;
        $list['average_price'] = 0;
        return $this->response->array($this->apiSuccess('Success',200,$list));
    }

}
