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
            'amount' => 'required|numeric',
        ];
        $payload = $request->only('amount');
        $validator = Validator::make($payload, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不正确！', $validator->errors());
        }
        if (0.01 > $payload['amount']) {
            return $this->response->array($this->apiError('提现金额不能小于0.01', 403));
        }

        $amount = sprintf("%0.2f", $payload['amount']);

        //可提现金额
        $cash = $this->auth_user->cash;

        if ($amount > $cash) {
            return $this->response->array($this->apiError('可提现金额不足', 403));
        }

        if ($cash == 0) {
            return $this->response->array($this->apiError('提现金额不能为空', 403));
        }

        $user = $this->auth_user;
        $company = null;
        if ($user->type == 1) {  // 需求公司
            $company = $user->demandCompany;
        } else if ($user->type == 2) {  // 设计公司
            $company = $user->designCompany;
        }

        // 判断公司信息是否认证通过、对公账户信息是否填写
        if (!$company->isVerify()) {
            return $this->response->array($this->apiError('公司信息未认证通过', 403));
        }

        if (empty($company->account_name) || empty($company->account_number) || empty($company->bank_name)) {
            return $this->response->array($this->apiError('对公账户信息填写不完全', 403));
        }


        try {
            DB::beginTransaction();

            $withdraw = WithdrawOrder::query()->create([
                'uid' => Tools::orderId($this->auth_user_id),
                'user_id' => $this->auth_user_id,
                'type' => 1,
                'amount' => $amount,
                'account_name' => $company->account_name,
                'account_number' => $company->account_number,
                'branch_name' => $company->bank_name,
                'status' => 0,
            ]);

            //账户冻结 提现金额
            $this->auth_user->frozenIncrease($this->auth_user_id, $amount);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->response->array($this->apiError('Error', 500));
        }

        if ($withdraw) {
            return $this->response->item($withdraw, new WithdrawOrderTransformer)->setMeta($this->apiMeta());
        }
    }

    //

    /**
     * @api {get} /withdraw/lists 用户提现列表
     * @apiVersion 1.0.0
     * @apiName withdraw lists
     * @apiGroup Withdraw
     *
     * @apiParam {integer} status 状态：0.申请；1.同意；2.全部;
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 分页
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *     "data": {[
     *          "id": 32,
     *          "uid": "012211520000003378",
     *          "user_id": 33,
     *          "type": 1,
     *          "amount": "0.80",
     *          "account_name": "田帅",
     *          "account_number": "1234567890",
     *          "account_bank_id": 6,
     *          "branch_name": "朝阳区三里屯运行",
     *          "status": 0,
     *          "summary": "",
     *          "created_at": 1516593120,
     *          "updated_at": 1516593120,
     *          "true_time": null,
     *          "account_bank_value": "民生银行"
     *      ]}
     *   }
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $status = $request->input('status', '');

        $withdraw_order = WithdrawOrder::query();

        switch ($status) {
            case 0:
                $withdraw_order->where('status', '=', 0);
                break;
            case 1:
                $withdraw_order->where('status', '=', 1);
                break;
            case 2:
                break;
            default:
                break;
        }

        $result = $withdraw_order->where('user_id', '=', $this->auth_user_id)
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return $this->response->paginator($result, new WithdrawOrderTransformer)->setMeta($this->apiMeta());
    }

}