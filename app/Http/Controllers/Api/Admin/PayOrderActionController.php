<?php
namespace App\Http\Controllers\Api\Admin;

use App\Events\PayOrderEvent;
use App\Http\AdminTransformer\PayOrderTransformer;
use App\Models\PayOrder;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
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
     * @apiParam {integer} type 0.全部；支付类型：1.预付押金；2.项目款；
     * @apiParam {integer} pay_type  支付方式； 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
     * @apiParam {integer} status 状态：0.未支付；1.支付成功；
     * @apiParam {integer} per_page 分页数量  默认15
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0.升序；1.降序（默认）；
     *
     * @apiSuccessExample 成功响应:
     * {
    "data": [
    {
    "id": 1,
    "uid": "zf59005935a069f",  //支付单号
    "user_id": 1,
    "type": 1,  //支付类型：1.预付押金；2.项目款；
    "item_id": 0,
    "status": 0,  //状态：0.未支付；1.支付成功；
    "summary": "发布需求保证金",
    "created_at": "2017-04-26 16:24:21",
    "updated_at": "2017-04-26 16:24:21",
    "pay_type": 0,  //支付方式； 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
    "pay_no": "",   //对应平台支付交易号
    "amount": "0.00",  //支付金额
    "item_name": "",   //项目名称
    "company_name": "",  //公司名称
    "user": {
    "id": 1,
    "account": "18629493221",
    "phone": "18629493221",
    "username": "",
    "email": null,
    "status": 0,
    "item_sum": 0,
    "created_at": "2017-03-31 03:00:18",
    "updated_at": "2017-05-05 17:11:46",
    "design_company_id": 1,
    "type": 1,
    "logo": 0,
    "role_id": 1,
    "logo_image": []
    }
    }
    ],
    "meta": {
    "meta": {
    "message": "Success",
    "status_code": 200
    },
    "pagination": {
    "total": 1,
    "count": 1,
    "per_page": 10,
    "current_page": 1,
    "total_pages": 1,
    "links": []
    }
    }
    }
     */
    public function lists(Request $request)
    {
        //支付方式； 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
        $pay_type = in_array($request->input('pay_type'), [1,2,3,4,5]) ? $request->input('pay_type') : null;
        //支付单类型 支付类型：1.预付押金；2.项目款；
        $type = in_array($request->input('type'), [1,2]) ? $request->input('type') : null;

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

        $query = PayOrder::query();

        if($type !== null){
            $query->where('type', $type);
        }
        if($status !== null){
            $query->where('status', $status);
        }
        if($pay_type !== null){
            $query->where('pay_status', $pay_type);
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
        ];

        $all = $request->only(['pay_order_id', 'pay_no']);

        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $pay_order = PayOrder::find($all['pay_order_id']);
        $pay_order->pay_type = 5; //银行转账
        $pay_order->pay_no = $all['pay_no'];
        $pay_order->status = 1; //支付成功
        $pay_order->save();

        event(new PayOrderEvent($pay_order));

        return $this->response->array($this->apiSuccess());
    }

}