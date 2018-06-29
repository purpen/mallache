<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\ItemCommissionTransformer;
use App\Models\ItemCommission;
use Illuminate\Http\Request;

class ItemCommissionController extends BaseController
{
    /**
     * @api {get} /admin/itemCommission/lists 收取项目费用列表
     * @apiVersion 1.0.0
     * @apiName ItemCommission lists
     * @apiGroup ItemCommission
     *
     * @apiParam {string} token
     * @apiParam {string} type 类型：0. 全部 1. 项目佣金 2. 扣税
     * @apiParam {integer} per_page 页面条数
     * @apiParam {integer} page 页面数
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type = $request->input('type') ?? 0;

        $query = ItemCommission::query();
        if ($type) {
            $query = $query->where('type', $type);
        }

        $lists = $query->orderBy('id', 'desc')->paginate($per_page);

        return $this->response->paginator($lists, new ItemCommissionTransformer())->setMeta($this->apiMeta());
    }

}