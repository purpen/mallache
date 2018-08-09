<?php

namespace App\Http\Controllers\Api\Admin;

use Dingo\Api\Http\Request;
use App\Models\DesignStatistics;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\StoreResourceFailedException;
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
}
