<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\PayOrderEvent;
use App\Http\AdminTransformer\PayOrderTransformer;
use App\Models\User;
use App\Models\FundLog;
use App\Models\PayOrder;
use App\Models\DesignResult;
use App\Service\Pay;
use App\Helper\Tools;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PayOrderActionController extends BaseController
{
    /**
     * @api {get} /admin/payOrder/lists 项目支付单列表
     * @apiVersion 1.0.0
     * @apiName item lists
     * @apiGroup AdminPayOrder
     *
     * @apiParam {string} token
     * @apiParam {integer} type 0.全部；支付类型：1.预付押金；2.项目款；5.设计成果；
     * @apiParam {integer} pay_type  支付方式； 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
     * @apiParam {integer} status 状态：0.未支付；1.支付成功；
     * @apiParam {integer} bank_transfer 银行转账状态：0.未上传转账凭证；1.已上传转账凭证；
     * @apiParam {integer} per_page 分页数量  默认15
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0.升序；1.降序（默认）；
     *
     * @apiSuccessExample 成功响应:
     * {
     * "data": [
     *      {
     *          "id": 1,
     *          "uid": "zf59005935a069f",  //支付单号
     *          "user_id": 1,
     *          "type": 1,  //支付类型：1.预付押金；2.项目款；3.首付款；4.阶段款；5.设计成果；
     *          "item_id": 0,
     *          "status": 0,  //状态：-2.订单异常关闭(解散订单并退款)；-1.关闭；0.未支付；1.支付成功；2.退款；
     *          "summary": "发布需求保证金",
     *          "created_at": "2017-04-26 16:24:21",
     *          "updated_at": "2017-04-26 16:24:21",
     *          "pay_type": 0,  //支付方式； 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
     *          "pay_no": "",   //对应平台支付交易号
     *          "amount": "0.00",  //支付金额
     *          "item_name": "",   //项目名称
     *          "company_name": "",  //公司名称
     *          "bank_transfer": 0,  // 银行转账状态：0.未上传转账凭证；1.已上传转账凭证；
     *          "assets": [],  // 银行转账凭证
     *          "user": {
     *              "id": 1,
     *              "account": "18629493221",
     *              "phone": "18629493221",
     *              "username": "",
     *              "email": null,
     *              "status": 0,
     *              "item_sum": 0,
     *              "created_at": "2017-03-31 03:00:18",
     *              "updated_at": "2017-05-05 17:11:46",
     *              "design_company_id": 1,
     *              "type": 1,
     *              "logo": 0,
     *              "role_id": 1,
     *              "logo_image": []
     *          }
     *      }
     * ],
     * "meta": {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *      "pagination": {
     *          "total": 1,
     *          "count": 1,
     *          "per_page": 10,
     *          "current_page": 1,
     *          "total_pages": 1,
     *          "links": []
     *      }
     *   }
     * }
     */
    public function lists(Request $request)
    {
        //支付方式； 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
        $pay_type = in_array($request->input('pay_type'), [1, 2, 3, 4, 5]) ? $request->input('pay_type') : null;
        //支付单类型 支付类型：1.预付押金；2.项目款；
        $type = in_array($request->input('type'), [1,2,3,4,5]) ? $request->input('type') : null;

        $status = in_array($request->input('status'), [0, 1]) ? $request->input('status') : null;

        $per_page = $request->input('per_page') ?? $this->per_page;

        $bank_transfer = in_array($request->input('bank_transfer'), [0, 1]) ? $request->input('bank_transfer') : null;

        $evt = $request->input('evt') ? (int)$request->input('evt') : 1;
        $val = $request->input('val') ? $request->input('val') : '';

        if ($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $query = PayOrder::query();

        if ($type !== null) {
            $query->where('type', $type);
        }else{
            $query->whereIn('type', [1,2,3,4]);
        }
        if ($status !== null) {
            $query->where('status', $status);
        }
        if ($pay_type !== null) {
            $query->where('pay_type', $pay_type);
        }
        if ($bank_transfer !== null) {
            $query->where('bank_transfer', $bank_transfer);
        }

        if ($val) {
            switch ($evt) {
                case 1:
                    $query->where('uid', $val);
                    break;
                case 2:
                    $query->where('item_id', (int)$val);
                    break;
                case 3:
                    $query->where('user_id', (int)$val);
                    break;
                case 4:
                    $query->where('user_id', (int)$val);
                    break;
                default:
                    $query->where('uid', $val);
            }
        }

        $lists = $query->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($lists, new PayOrderTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /admin/payOrder/truePay 后台确认项目支付单付款
     * @apiVersion 1.0.0
     * @apiName pay truePay
     * @apiGroup AdminPayOrder
     *
     * @apiParam {string} token
     * @apiParam {integer} pay_order_id 支付单ID
     * @apiParam {string} pay_no  银行交易单号
     * @apiParam {integer} bank_id 银行id
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function truePay(Request $request)
    {
        $rules = [
            'pay_order_id' => 'required|exists:pay_order,id',
            'pay_no' => 'required',
            'bank_id' => 'required|integer',
        ];

        $all = $request->only(['pay_order_id', 'pay_no', 'bank_id']);

        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try {
            DB::beginTransaction();
            $pay_order = PayOrder::find($all['pay_order_id']);
            $pay_order->pay_type = 5; //银行转账
            $pay_order->pay_no = $all['pay_no'];
            $pay_order->status = 1; //支付成功
            $pay_order->bank_id = $all['bank_id'];
            $res = $pay_order->save();

            // 支付成功需要处理的业务
            $pay = new Pay($pay_order);
            $pay->paySuccess();
            if($res){
                $tools = new Tools;
                $message = '您的设计成果订单【'.$pay_order->uid.'】支付凭证平台已确认，请前往订单列表查看';
                $tools->message($pay_order->user_id,'设计成果订单',$message,5,$pay_order->id,null);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->response->array($this->apiError('error', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {post} /admin/payOrder/dissolution 解散支付订单
     * @apiVersion 1.0.0
     * @apiName pay closeOrder
     * @apiGroup pay
     * @apiParam {string} token
     * @apiParam {integer} id 订单id
     * @apiParam {integer} design 设计方金额
     * @apiParam {integer} demand 需求方金额
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *         "message": "Success",
     *         "status_code": 200
     *     }
     * }
     */
    public function payOrderDissolution(Request $request)
    {
        $all = $request->all();
        $rules = [
            'id' => 'required|integer',
            'design' => 'required',
            'demand' => 'required'
        ];
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException(403,$validator->errors());
        }
        $pay = new PayOrder;
        $pay_data = $pay->where(['id'=>$all['id'],'type'=>5])->first();
        if(!$pay_data){
            return $this->apiError('设计成果订单不存在',404);
        }
        if(empty($pay_data->user_id) || empty($pay_data->design_user_id)){
            return $this->apiError('设计成果订单错误',403);
        }
        $design = $all['design'];//设计方金额
        $demand = $all['demand'];//需求方金额
        $amount = $pay_data->amount;
        $price = $design + $demand;
        if($amount != $price){
            return $this->apiError('金额不正确',403);
        }
        $design_result = DesignResult::where('id',$pay_data->design_result_id)->first();
        if(!$design_result){
            return $this->apiError('设计成果不存在',404);
        }
        if($design_result->sell == 2){
            return $this->apiError('设计成果已确认',400);
        }
        DB::beginTransaction();
        try {
            $user = new User();
            $pay_data->status = -2;
            //下架状态时改变为上架
            if($design_result->sell < 2){
                $design_result->sell = 0;
                $design_result->status = -1;
                $design_result->save();
            }
            $pay_data->save();
            $user->totalIncrease($pay_data->user_id,$demand); //需求方
            $user->totalIncrease($pay_data->design_user_id,$design); //设计方
            $fund_log = new FundLog();
            //需求公司资金流水记录
            $fund_log->outFund($pay_data->user_id, $demand, $pay_data->pay_type, $pay_data->design_user_id, '设计成果【' . $design_result->title . '】订单解散退款');
            //设计公司资金流水记录
            $fund_log->outFund($pay_data->design_user_id, $design, $pay_data->pay_type, $pay_data->user_id, '设计成果【' . $design_result->title . '】订单解散退款');
            DB::commit();
            return $this->apiSuccess('Success',200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->apiError('Error',400);
        }
    }

}
