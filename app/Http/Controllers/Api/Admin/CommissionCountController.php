<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\CommissionCountTransformer;
use App\Models\CommissionCount;
use App\Models\DesignCompanyModel;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * 平台佣金免费次数控制器
 *
 * Class CommissionCountController
 * @package App\Http\Controllers\Api\Admin
 */
class CommissionCountController extends BaseController
{
    /**
     * @api {post} /admin/commissionCount/add 添加设计公司佣金免费次数
     * @apiVersion 1.0.0
     * @apiName AdminCommissionCount addCount
     * @apiGroup AdminCommissionCount
     *
     * @apiParam {string} token
     * @apiParam {array} design_company_id_arr 设计公司ID数组
     * @apiParam {integer} count 增加次数
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function addCount(Request $request)
    {
        $rules = [
            'design_company_id_arr' => 'required|array',
            'count' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $design_company_id_arr = (array)$request->input('design_company_id_arr');
        $count = (int)$request->input('count');

        // 判断有效设计公司ID
        $design_company_id_arr = DesignCompanyModel::query()
            ->select('id')
            ->whereIn('id', $design_company_id_arr)
            ->get()->pluck('id')
            ->all();

        try {
            DB::beginTransaction();
            CommissionCount::addCount($design_company_id_arr, $count);
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
        }

        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {get} /admin/commissionCount/lists 设计公司优惠次数列表
     * @apiVersion 1.0.0
     * @apiName AdminCommissionCount lists
     * @apiGroup AdminCommissionCount
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

        $lists = CommissionCount::query()->orderBy('id', 'desc')->paginate($per_page);

        return $this->response->paginator($lists, new CommissionCountTransformer())->setMeta($this->apiMeta());
    }

}