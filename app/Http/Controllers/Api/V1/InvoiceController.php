<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends BaseController
{
    /**
     * @api {put} /invoice/designTrueSend 设计公司确认发票已开出
     * @apiVersion 1.0.0
     * @apiName invoice designTrueSend
     * @apiGroup invoice
     *
     * @apiParam {integer} id 发票记录ID
     * @apiParam {integer} logistics_id 物流公司ID
     * @apiParam {integer} logistics_number 物流单号
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
    public function designTrueSend(Request $request)
    {
        $id = $request->input('id');
        $logistics_id = $request->input('logistics_id');
        $logistics_number = $request->input('logistics_number');

        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->response->array($this->apiError('not found', 404));
        }
        // 设计公司的发票
        if ($invoice->type != 1 || $invoice->company_type != 2) {
            return $this->response->array($this->apiError('request error', 403));
        }
        if ($invoice->status == 2 || $invoice->status == 3) {
            return $this->response->array($this->apiSuccess());
        }

        try {
            DB::beginTransaction();
            $invoice->status = 2;
            $invoice->user_id = $this->auth_user_id;
            $invoice->logistics_id = $logistics_id;
            $invoice->logistics_number = $logistics_number;
            $invoice->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {put} /invoice/demandTrueGet 需求公司确认收到发票
     * @apiVersion 1.0.0
     * @apiName invoice demandTrueGet
     * @apiGroup invoice
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
    public function demandTrueGet(Request $request)
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

        if ($invoice->status == 3) {
            return $this->response->array($this->apiSuccess());
        }

        try {
            DB::beginTransaction();
            $invoice->status = 3;
            $invoice->user_id = $this->auth_user_id;
            $invoice->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->array($this->apiSuccess());
    }
}