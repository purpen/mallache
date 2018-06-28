<?php

namespace App\Http\Controllers\Api\Admin;


// 发票控制器
use App\Http\AdminTransformer\InvoiceTransformer;
use App\Models\Invoice;
use App\Servers\PayToDesignCompany;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends BaseController
{
    /**
     * @api {get} /admin/invoice/pullLists 需要设计公司给平台开具发票列表
     * @apiVersion 1.0.0
     * @apiName AdminInvoice pullLists
     * @apiGroup AdminInvoice
     *
     * @apiParam {integer} status 状态：0.全部 1. 未开发票 2. 已开发票 3. 收到发票
     * @apiParam {integer} per_page 每页数量
     * @apiParam {integer} page 页数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "data": [
     *          {
     *              "id": 1,
     *              "type": 2,  // 收发类型：1. 收 2. 开
     *              "pay_type": 1, // 支付类型： 1. 首付款 2. 阶段款
     *              "company_type": 1, //公司类型 1. 需求公司 2. 设计公司
     *              "target_id": 9,     // 公司ID
     *              "company_name": "", // 公司名称
     *              "duty_number": "",  // 税号
     *              "price": "19080.00", // 金额
     *              "item_id": 181, // 项目ID
     *              "item_stage_id": null, // 项目阶段
     *              "user_id": null, // 操作用户
     *              "summary": "", // 备注
     *              "status": 1,  // 状态：1. 未开发票 2. 已开发票 3. 收到发票
     *              "taxable_type": 1, // 纳税类型 1. 一般纳税人 2. 小额纳税人
     *              "invoice_type": 1, // 发票类型 1. 专票 2. 普票
     *              "logistics_id": 1, // 物流公司ID
     *              "logistics_name": null, // 物流公司名称
     *              "logistics_number": 124232, // 物流单号
     *          }
     *      ],
     *          "meta": {
     *              "message": "Success",
     *              "status_code": 200,
     *              "pagination": {
     *              "total": 1,
     *              "count": 1,
     *              "per_page": 10,
     *              "current_page": 1,
     *              "total_pages": 1,
     *              "links": []
     *          }
     *      }
     * }
     */
    public function pullLists(Request $request)
    {
        $status = $request->input('status');
        $per_page = $request->input('per_page') ?? $this->per_page;

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1,2,3',
        ]);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $query = Invoice::query()->where('type', 1);

        if ($status != 0) {
            $query = $query->where('status', $status);
        }

        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new InvoiceTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {get} /admin/invoice/pushLists 需要平台给需求公司开具发票列表
     * @apiVersion 1.0.0
     * @apiName AdminInvoice pushLists
     * @apiGroup AdminInvoice
     *
     * @apiParam {integer} status 状态：0.全部 1. 未开发票 2. 已开发票 3. 收到发票
     * @apiParam {integer} per_page 每页数量
     * @apiParam {integer} page 页数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "data": [
     *          {
     *              "id": 1,
     *              "type": 2,  // 收发类型：1. 收 2. 开
     *              "pay_type": 1, // 支付类型： 1. 首付款 2. 阶段款
     *              "company_type": 1, //公司类型 1. 需求公司 2. 设计公司
     *              "target_id": 9,     // 公司ID
     *              "company_name": "", // 公司名称
     *              "duty_number": "",  // 税号
     *              "price": "19080.00", // 金额
     *              "item_id": 181, // 项目ID
     *              "item_stage_id": null, // 项目阶段
     *              "user_id": null, // 操作用户
     *              "summary": "", // 备注
     *              "status": 1  // 状态：1. 未开发票 2. 已开发票 3. 收到发票
     *          }
     *      ],
     *          "meta": {
     *              "message": "Success",
     *              "status_code": 200,
     *              "pagination": {
     *              "total": 1,
     *              "count": 1,
     *              "per_page": 10,
     *              "current_page": 1,
     *              "total_pages": 1,
     *              "links": []
     *          }
     *      }
     * }
     */
    public function pushLists(Request $request)
    {
        $status = $request->input('status');
        $per_page = $request->input('$per_page') ?? $this->per_page;

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1,2,3',
        ]);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $query = Invoice::query()->where('type', 2);

        if ($status != 0) {
            $query = $query->where('status', $status);
        }

        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new InvoiceTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /admin/invoice/trueInvoice 确认收到设计公司发票
     * @apiVersion 1.0.0
     * @apiName AdminInvoice trueInvoice
     * @apiGroup AdminInvoice
     *
     * @apiParam {integer} id 发票记录ID
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
    public function trueInvoice(Request $request)
    {
        $id = $request->input('id');
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->response->array($this->apiError('not found', 404));
        }
        // 设计公司的发票
        if ($invoice->type != 1 || $invoice->company_type != 2) {
            return $this->response->array($this->apiError('request error', 403));
        }
        if ($invoice->status == 3) {
            return $this->response->array($this->apiSuccess());
        }

        try {
            DB::beginTransaction();
            $invoice->status = 3;
            $invoice->user_id = $this->auth_user_id;
            $invoice->save();

            // 完善平台发票信息

            //将对应款项打入设计公司钱包
            $pay = new PayToDesignCompany($invoice);
            $pay->pay();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {put} /admin/invoice/trueDemandInvoice 确认给需求公司的发票已开
     * @apiVersion 1.0.0
     * @apiName AdminInvoice trueDemandInvoice
     * @apiGroup AdminInvoice
     *
     * @apiParam {integer} id 发票记录ID
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
    public function trueDemandInvoice(Request $request)
    {
        $id = $request->input('id');
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->response->array($this->apiError('not found', 404));
        }

        // 需求公司发票
        if ($invoice->type != 2 || $invoice->company_type != 1) {
            return $this->response->array($this->apiError('request error', 403));
        }

        if ($invoice->status == 2 || $invoice->status == 3) {
            return $this->response->array($this->apiSuccess());
        }

        try {
            DB::beginTransaction();
            $invoice->status = 2;
            $invoice->user_id = $this->auth_user_id;
            $invoice->save();

            // 完善发票中需求公司的发票信息，如需求公司信息不完善，不通过--


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->array($this->apiSuccess());
    }

}