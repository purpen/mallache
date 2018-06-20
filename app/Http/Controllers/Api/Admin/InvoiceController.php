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
     * @apiParam {integer} status 状态：0.全部 1. 未开发票 2. 已开发票
     * @apiParam {integer} per_page 每页数量
     * @apiParam {integer} page 页数
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
    public function pullLists(Request $request)
    {
        $status = $request->input('status');
        $per_page = $request->input('per_page') ?? $this->per_page;

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1,2',
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
     * @apiParam {integer} status 状态：0.全部 1. 未开发票 2. 已开发票
     * @apiParam {integer} per_page 每页数量
     * @apiParam {integer} page 页数
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
    public function pushLists(Request $request)
    {
        $status = $request->input('status');
        $per_page = $request->input('$per_page') ?? $this->per_page;

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1,2',
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
     * @api {put} /admin/invoice/trueInvoice 确认发票已开
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

        if ($invoice->status == 1) {
            return $this->response->array($this->apiSuccess());
        }

        try {
            DB::beginTransaction();
            $invoice->status = 1;
            $invoice->save();

            // 完善发票信息

            // 如果是确认已收到设计公司的发票，将对应款项打入设计公司钱包
            if ($invoice->type == 1 && $invoice->company_type == 2) {
                $pay = new PayToDesignCompany($invoice);
                $pay->pay();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->array($this->apiSuccess());
    }

}