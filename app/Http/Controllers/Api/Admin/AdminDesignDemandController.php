<?php
/**
 * 设计需求控制器
 *
 * @User yht
 * @time 2018-10-22
 */

namespace App\Http\Controllers\Api\Admin;

use App\Helper\Tools;
use Illuminate\Http\Request;
use Dingo\Api\Exception\StoreResourceFailedException;
use App\Http\AdminTransformer\AdminDesignDemandListTransformer;

use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\DesignDemand;

class AdminDesignDemandController extends BaseController
{
    /**
     * @api {get} /admin/designDemand/lists 发布的设计需求列表
     * @apiVersion 1.0.0
     * @apiName AdminDesignDemand lists
     * @apiGroup AdminDesignDemand
     *
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
     * @apiParam {integer} status -1.未通过审核；1.审核中；2.已发布
     * @apiParam {integer} sort 0.升序；1.降序（默认）;2.推荐降序；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *          "data": [
     *          {
     *              "id": 1,                    //需求ID
     *              "user_id": 12,              //用户ID
     *              "demand_company_id": 1,     //需求公司ID
     *              "status": 0,                //状态：-1.关闭 0.待发布 1. 审核中 2.已发布
     *              "type": 1,                  //设计类型：1.产品设计；2.UI UX 设计；3. 平面设计 4.H5 5.包装设计 6.插画设计
     *              "design_types": "\"[1,2]\"",//设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计 4.其他；[1,2]
     *              "name": "测试1",            //项目名称
     *              "cycle": 1,                 //设计周期：1.1个月内；2.1-2个月；3.2-3个月；4.3-4个月；5.4个月以上
     *              "design_cost": 1,           //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     *              "follow_count": 0,          //关注数量
     *              "created_at": 1540281300,
     *              "updated_at": 1540281300
     *          }
     *       ]
     *  }
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $status = in_array($request->input('status'), [-1, 1, 2]) ? $request->input('status') : null;
        $sort = in_array($request->input('sort'), [0, 1, 2]) ? $request->input('sort') : 0;

        $demand = DesignDemand::with('demandCompany');
        if ($status !== null && $status !== '') {
            $demand->where('status', $status);
        }

        //排序
        switch ($sort) {
            case 0:
                $demand->orderBy('created_at', 'desc');
                break;
            case 1:
                $demand->orderBy('created_at', 'asc');
                break;
            case 2:
                $demand->orderBy('id', 'desc');
                break;
        }
        $design_demand = $demand->paginate($per_page);
        return $this->response->paginator($design_demand, new AdminDesignDemandListTransformer)->setMeta($this->apiMeta());

    }

    /**
     * @api {put} /admin/designDemand/auditStatus 设计需求信息审核
     * @apiVersion 1.0.0
     * @apiName AdminDesignDemand auditStatus
     * @apiGroup AdminDesignDemand
     *
     * @apiParam {integer} demand_id 需求状态
     * @apiParam {integer} status -1: 失败 2: 成功
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
    public function auditStatus(Request $request)
    {

        $this->validate($request, [
            'demand_id' => 'required',
            'status' => 'required',
        ]);
        $demand_id = $request->input('demand_id');
        $status = $request->input('status');

        if (!in_array($status, [-1, 2])) {
            return $this->response->array($this->apiSuccess('状态参数错误', 403));
        }

        $design_demand = DesignDemand::where('id', $demand_id)->first();
        if (!$design_demand) {
            return $this->response->array($this->apiSuccess('设计需求不存在', 404));
        }

        $design = DesignDemand::where('id', $demand_id)->update(['status'=>$status]);
        if (!$design) {
            return $this->response->array($this->apiError('修改失败', 500));
        }

        // 系统消息通知
        $tools = new Tools();
        $title = '设计需求审核';
        $content = '';
        switch ($status) {
            case -1:
                $content = '设计需求未通过审核，请修改资料重新提交';
                break;
            case 2:
                $content = '设计需求已通过审核';
                break;
        }
        $tools->message($design_demand->user_id, $title, $content, 1, null);

        return $this->response->array($this->apiSuccess('Success', 200));
    }
}