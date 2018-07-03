<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\ItemStatusEvent;
use App\Helper\Tools;
use App\Http\AdminTransformer\ItemListTransformer;
use App\Http\AdminTransformer\ItemTransformer;
use App\Http\Controllers\Controller;
use App\Models\DesignCompanyModel;
use App\Models\FundLog;
use App\Models\Item;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ItemActionController extends Controller
{
    /**
     * @api {get} /admin/item/lists 项目列表
     * @apiVersion 1.0.0
     * @apiName item lists
     * @apiGroup AdminItem
     *
     * @apiParam {string} token
     * @apiParam {integer} type 0.全部；1.填写资料ing；2.等待推荐； 默认0；
     * @apiParam {int} evt 查询条件：1.ID；2.公司名称；3.联系人电话；8.用户ID；9.--；
     * @apiParam {string} val 查询值
     * @apiParam {integer} per_page 分页数量  默认15
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0.降序（默认）；1.升序；
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200,
     * "pagination": {
     * "total": 3,
     * "count": 1,
     * "per_page": 1,
     * "current_page": 1,
     * "total_pages": 3,
     * "links": {
     * "next": "http://saas.me/demand/itemList?page=2"
     * }
     *      },
     *      "data": [
     * {
     * "item": {
     * "id": 1,
     * "user_id": 1,
     * "design_type": 1,
     * "status": 2,
     * "created_at": "2017-04-06 14:17:09",
     * "updated_at": "2017-04-11 17:43:47",
     * "recommend": "1",
     * "ord_recommend": "",
     * "type": 2,
     * "design_company_id": 0,
     * "contract_id": 0,
     * "price": "0.00",
     * "company_name": "",
     * "company_abbreviation": "",
     * "company_size": 0,
     * "company_web": "",
     * "company_province": 0,
     * "company_city": 0,
     * "company_area": 0,
     * "address": "",
     * "contact_name": "",
     * "phone": "",
     * "email": "",
     * "quotation_id": 0,
     * "stage_status": 0,
     * "type_value": "UI UX设计类型",
     * "design_type_value": "app设计",
     * "company_province_value": "",
     * "company_city_value": "",
     * "company_area_value": "",
     * "status_value": "项目进行中",
     * "user": {
     * "id": 2,
     * "account": "18132382134",
     * "phone": "18132382134",
     * "username": "",
     * "email": null,
     * "design_company_id": 48,
     * "status": 0,
     * "item_sum": 0,
     * "created_at": "2017-04-14 10:35:13",
     * "updated_at": "2017-05-05 17:27:52",
     * "type": 0,
     * "logo": 0,
     * "role_id": 0,
     * "demand_company_id": 0,
     * "logo_image": []
     * }
     * },
     * "info": {
     * "id": 2,
     * "item_id": 1,
     * "system": 1,
     * "design_content": 0,
     * "page_number": 0,
     * "name": "",
     * "stage": 0,
     * "complete_content": 0,
     * "other_content": "",
     * "style": 0,
     * "start_time": 0,
     * "cycle": 0,
     * "design_cost": 0,
     * "province": 0,
     * "city": 0,
     * "summary": "",
     * "artificial": 0,
     * "created_at": "2017-04-06 18:03:16",
     * "updated_at": "2017-04-06 18:03:16",
     * "image": [],
     * "system_value": "IOS",
     * "design_content_value": "",
     * "stage_value": "",
     * "complete_content_value": "",
     * "design_cost_value": "",
     * "province_value": "",
     * "city_value": ""
     * }
     * },
     * }
     */
    public function itemList(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $sort = $request->input('sort') ? (int)$request->input('sort') : 0;
        $evt = $request->input('evt') ? (int)$request->input('evt') : 1;
        $val = $request->input('val') ? $request->input('val') : '';

        $query = Item::with(['user']);
        switch ($request->input('type')) {
            case 1:
                $query = $query->where('status', 1);
                break;
            case 2:
                $query = $query->where('status', 2);
                break;
            default:
        }

        if ($val) {
            switch ($evt) {
                case 1:
                    $query->where('id', (int)$val);
                    break;
                case 2:
                    $query->where('company_name', $val);
                    break;
                case 3:
                    $query->where('phone', $val);
                    break;
                case 8:
                    $query->where('user_id', $val);
                    break;
                default:
                    $query->where('id', (int)$val);
            }
        }

        //排序
        switch ($sort) {
            case 0:
                $query->orderBy('id', 'desc');
                break;
            case 1:
                $query->orderBy('id', 'asc');
                break;
        }


        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new ItemListTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /admin/item/addDesignToItem 为项目添加推荐公司
     * @apiVersion 1.0.0
     * @apiName item addDesignToItem
     * @apiGroup AdminItem
     *
     * @apiParam {string} token
     * @apiParam {integer} item_id 项目ID
     * @apiParam {array} recommend 设计公司ID数组
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function addDesignToItem(Request $request)
    {
        $rules = [
            'item_id' => 'required|exists:item,id',
            'recommend' => 'required|array',
        ];

        $all = $request->only(['item_id', 'recommend']);

        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try {
            $design = DesignCompanyModel::where(['status' => 1, 'verify_status' => 1])
                ->whereIn('id', $all['recommend'])
                ->get()->pluck('id')->all();
            if (empty($design)) {
                return $this->response->array($this->apiError('推荐设计公司都不符合条件', 403));
            }

            $item = Item::find($all['item_id']);
            if (!$item) {
                return $this->response->array($this->apiError('not found', 404));
            }

            if (2 != $item->status && 3 != $item->status) {
                return $this->response->array($this->apiError('当前状态不可操作', 403));
            }

            //验证
            $ord_recommend = $item->ord_recommend;
            if (!empty($ord_recommend)) {
                $ord_recommend_arr = explode(',', $ord_recommend);
                $design = array_diff($design, $ord_recommend_arr);
            }

            $item->recommend = implode(',', $design) . ',' . $item->recommend;
            $item->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {post} /admin/item/trueItem 确认给项目推荐的设计公司
     * @apiVersion 1.0.0
     * @apiName item trueItem
     * @apiGroup AdminItem
     *
     * @apiParam {string} token
     * @apiParam {integer} item_id 项目ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function trueItem(Request $request)
    {
        $item_id = $request->input('item_id');

        $item = Item::find($item_id);
        if (!$item) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($item->status != 2) {
            return $this->response->array($this->apiError('当前状态不可操作', 403));
        }

        if (empty($item->recommend)) {
            return $this->response->array($this->apiError('当前项目没有推荐设计公司', 403));
        }


        $demand_company = User::find($item->user_id)->demandCompany;
        if (!$demand_company || $demand_company->verify_status != 1) {
            return $this->response->array($this->apiError('该需求公司资料未审核', 403));
        }

        // 补充需求方信息
        $all['company_name'] = $demand_company->company_name;
        $all['company_abbreviation'] = $demand_company->company_abbreviation;
        $all['company_size'] = $demand_company->company_size;
        $all['company_web'] = $demand_company->company_web;
        $all['company_province'] = $demand_company->province;
        $all['company_city'] = $demand_company->city;
        $all['company_area'] = $demand_company->area;
        $all['address'] = $demand_company->address;
        $all['contact_name'] = $demand_company->contact_name;
        $all['phone'] = $demand_company->phone;
        $all['email'] = $demand_company->email;
        $all['position'] = $demand_company->position;
        $all['status'] = 3;

        $item->update($all);

        //触发事件
        event(new ItemStatusEvent($item));

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /admin/item/show 项目详情
     * @apiVersion 1.0.0
     * @apiName item show
     * @apiGroup AdminItem
     *
     * @apiParam {string} token
     * @apiParam {integer} id 项目ID
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
        $id = $request->input('id');
        if (!$item = Item::find($id)) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }


    /**
     * @api {post} /admin/item/closeItem 后台强制关闭当前项目并分配项目款
     * @apiVersion 1.0.0
     * @apiName item closeItem
     * @apiGroup AdminItem
     *
     * @apiParam {string} token
     * @apiParam {integer} item_id 项目ID
     * @apiParam {float} demand_amount 需求公司分配金额
     * @apiParam {float} design_amount 设计公司分配金额
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function closeItem(Request $request)
    {
        $this->validate($request, [
            'item_id' => 'required|integer',
            'demand_amount' => 'required|numeric',
            'design_amount' => 'required|numeric',
        ]);

        $item_id = $request->input('item_id');
        $demand_amount = sprintf("%.2f", $request->input('demand_amount'));
        $design_amount = sprintf("%.2f", $request->input('design_amount'));

        $item = Item::find($item_id);
        if (!$item) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        if ($item->rest_fund != bcadd($demand_amount, $design_amount, 2)) {
            return $this->response->array($this->apiError('资金总额错误！', 404));
        }

        DB::beginTransaction();
        $user_model = new User();
        $item_info = $item->itemInfo();

        // 需求用户ID
        $demand_user_id = $item->user_id;
        //设计公司用户ID
        $design_user_id = $item->designCompany->user_id;

        try {

            //减少需求公司账户金额（总金额、冻结金额）
            $user_model->totalAndFrozenDecrease($demand_user_id, $design_amount);
            // 减少需求公司冻结金额
            $user_model->frozenDecrease($demand_user_id, $demand_amount);

            // 增加设计公司账户金额
            $user_model->totalIncrease($design_user_id, $design_amount);

            $fund_log = new FundLog();
            //需求公司流水记录
            $fund_log->outFund($demand_user_id, $design_amount, 1, $design_user_id, '【' . $item_info['name'] . '】' . '向设计公司支付项目款');
            //设计公司流水记录
            $fund_log->inFund($design_user_id, $design_amount, 1, $demand_user_id, '【' . $item_info['name'] . '】' . '收到项目款');


            if ($design_amount > 0) {
                $tools = new Tools();
                //通知需求公司
                $title = '支付项目款';
                $content = '【' . $item_info['name'] . '】项目已向设计公司支付项目款';
                $tools->message($demand_user_id, $title, $content, 3, null);

                //通知设计公司
                $title1 = '收到项目款';
                $content1 = '【' . $item_info['name'] . '】项目已收到项目款';
                $tools->message($design_user_id, $title1, $content1, 3, null);
            }
            // 强制关闭项目
            $item->status = -3;
            $item->rest_fund = 0.00;
            $item->save();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }

        return $this->response->array($this->apiSuccess());
    }

}
