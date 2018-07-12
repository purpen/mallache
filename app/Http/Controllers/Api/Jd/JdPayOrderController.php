<?php

namespace App\Http\Controllers\Api\Jd;

use App\Http\JdTransformer\PayOrderTransformer;
use App\Models\PayOrder;
use App\Models\User;
use Illuminate\Http\Request;

class JdPayOrderController extends BaseController
{
    /**
     * @api {get} /jd/payOrder/lists 项目支付单列表
     * @apiVersion 1.0.0
     * @apiName JdPayOrder lists
     * @apiGroup JdPayOrder
     *
     * @apiParam {string} token
     * @apiParam {integer} type 0.全部；支付类型：1.预付押金；2.项目款；
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
     *          "type": 1,  //支付类型：1.预付押金；2.项目款；
     *          "item_id": 0,
     *          "status": 0,  //状态：0.未支付；1.支付成功；
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
        $login_user_id = $this->auth_user_id;
        $source_admin = User::sourceAdmin($login_user_id);
        if($source_admin != 1){
            return $this->response->array($this->apiSuccess('登陆用户没有权限查看', 403));
        }
        //支付方式； 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
//        $pay_type = in_array($request->input('pay_type'), [1, 2, 3, 4, 5]) ? $request->input('pay_type') : null;
        //支付单类型 支付类型：1.预付押金；2.项目款；
        $type = in_array($request->input('type'), [1, 2]) ? $request->input('type') : null;

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
        }
        if ($status !== null) {
            $query->where('status', $status);
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

        $lists = $query->where('source' , 1)->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($lists, new PayOrderTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {get} /jd/payOrder/show 项目支付单详情
     * @apiVersion 1.0.0
     * @apiName JdPayOrder show
     * @apiGroup JdPayOrder
     *
     * @apiParam {string} token
     * @apiParam {integer} id 项目支付单ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *      "data": {
     *
     *      }
     *  }
     */
    public function show(Request $request)
    {
        $login_user_id = $this->auth_user_id;
        $source_admin = User::sourceAdmin($login_user_id);
        if($source_admin != 1){
            return $this->response->array($this->apiSuccess('登陆用户没有权限查看', 403));
        }
        $id = $request->input('id');
        if (!$payOrder = PayOrder::find($id)) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        return $this->response->item($payOrder, new PayOrderTransformer())->setMeta($this->apiMeta());
    }
}
