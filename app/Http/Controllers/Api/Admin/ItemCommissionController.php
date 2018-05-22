<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\ItemCommissionTransformer;
use App\Models\ItemCommission;
use Illuminate\Http\Request;

class ItemCommissionController extends BaseController
{
    /**
     * @api {get} /itemCommission/lists 收取项目佣金列表
     * @apiVersion 1.0.0
     * @apiName ItemCommission lists
     * @apiGroup ItemCommission
     *
     * @apiParam {string} token
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

        $lists = ItemCommission::query()->orderBy('id', 'desc')->paginate($per_page);

        return $this->response->paginator($lists, new ItemCommissionTransformer())->setMeta($this->apiMeta());
    }

}