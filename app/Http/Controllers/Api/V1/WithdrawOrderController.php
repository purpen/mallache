<?php
/**
 * 提现相关操作
 * User: llh
 * Date: 2017/5/22
 * Time: 14:01
 */

namespace App\Http\Controllers\Api\V1;


use App\Helper\Tools;
use App\Http\Transformer\WithdrawOrderTransformer;
use App\Models\WithdrawOrder;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Bank;

class WithdrawOrderController extends BaseController
{
    /**
     * @api {post} /withdraw/create 用户提现
     * @apiVersion 1.0.0
     * @apiName withdraw create
     * @apiGroup Withdraw
     *
     * @apiParam {integer} bank_id 银行账户ID；
     * @apiParam {float} amount 提现金额
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *     "data": {
     *
     *      }
     *   }
     */
    public function create(Request $request)
    {
        $rules = [
            'bank_id' => 'required|integer',
            'amount' => 'required|numeric',
        ];
        $payload = $request->only('bank_id', 'amount');
        $validator = Validator::make($payload, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('请求参数格式不正确！', $validator->errors());
        }

        $amount = $payload['amount'];
        $bank_id = $payload['bank_id'];

        //可提现金额
        $cash = $this->auth_user->cash;
        if($amount > $cash){
            return $this->response->array($this->apiError('可提现金额不足', 403));
        }

        if(!$bank = Bank::where(['id' => $bank_id, 'status' => 0])->first()){
            return $this->response->array($this->apiError('该银行卡不存在', 404));
        }

        if($this->auth_user_id === $bank->user_id){
            return $this->response->array($this->apiError('银行卡错误', 403));
        }

        try{
            DB::beginTransaction();

            $withdraw = WithdrawOrder::create([
                'uid' => Tools::orderId($this->auth_user_id),
                'user_id' => $this->auth_user_id,
                'type' => 1,
                'amount' => $amount,
                'account_name' => $bank->account_name,
                'account_number' => $bank->account_number,
                'account_bank_id' => $bank->account_bank_id,
                'branch_name' => $bank->branch_name,
                'status' => 0,
            ]);

            //账户冻结 提现金额
            $this->auth_user->frozenIncrease($this->auth_user_id, $amount);

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
        }


        if($withdraw){
            return $this->response->item($withdraw, new WithdrawOrderTransformer)->setMeta($this->apiSuccess());
        }
    }

}