<?php
/**
 * Created by PhpStorm.
 * User: llh
 * Date: 2017/5/22
 * Time: 15:33
 */

namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\WithdrawOrderTransformer;
use App\Models\FundLog;
use App\Models\User;
use App\Models\WithdrawOrder;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WithdrawOrderActionController extends BaseController
{
    /**
     * @api {get} /admin/withdrawOrder/lists 提现项目列表
     * @apiVersion 1.0.0
     * @apiName withdrawOrder lists
     * @apiGroup AdminWithdrawOrder
     *
     * @apiParam {string} token
     * @apiParam {int} status 状态：0.申请；1.同意；
     * @apiParam {integer} per_page 分页数量  默认15
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0.升序；1.降序（默认;
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function lists(Request $request)
    {
        $status = in_array($request->input('status'), [0,1]) ? $request->input('status') : null;
        $per_page = $request->input('per_page') ?? $this->per_page;
        if($request->input('sort') == 0 && $request->input('sort') !== null)
        {
            $sort = 'asc';
        }
        else
        {
            $sort = 'desc';
        }
        $query = WithdrawOrder::query();

        if($status !== null){
            $query->where('status', $status);
        }

        $lists = $query->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($lists, new WithdrawOrderTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /admin/withdrawOrder/trueWithdraw  确认提现单已提现
     * @apiVersion 1.0.0
     * @apiName withdrawOrder trueWithdraw
     * @apiGroup AdminWithdrawOrder
     *
     * @apiParam {string} token
     * @apiParam {integer} withdraw_order_id 提现单ID
     * @apiParam {string} summary 备注
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function trueWithdraw(Request $request)
    {
        $rules = [
            'withdraw_order_id' => 'required|integer',
        ];

        $all = $request->only(['withdraw_order_id']);

        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $withdraw_order_id = (int)$request->input('withdraw_order_id');
        $summary = $request->input('summary') ?? '';

        if(!$withdraw_order = WithdrawOrder::find($withdraw_order_id)){
            return $this->response->array($this->apiError('NOT FOUND', 404));
        }

        try{
            DB::beginTransaction();

            $withdraw_order->status = 1;
            $withdraw_order->summary = $summary;
            $withdraw_order->true_time = date("Y-m-d H:i:s");
            $withdraw_order->true_user_id = $this->auth_user_id;
            $withdraw_order->save();

            //减少 用户总金额和冻结金额
            $user = new User();
            $user->totalAndFrozenDecrease($withdraw_order->user_id, $withdraw_order->amount);

            //交易流水记录
            $fund_log = new FundLog();
            $fund_log->outFund($withdraw_order->user_id, $withdraw_order->amount, 5,$withdraw_order->uid, '提现');

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

}