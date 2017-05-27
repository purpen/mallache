<?php
namespace App\Http\Controllers\Api\Admin;

use App\Events\ItemStatusEvent;
use App\Helper\Tools;
use App\Http\AdminTransformer\ItemTransformer;
use App\Http\Controllers\Controller;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
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
     * @apiParam {integer} per_page 分页数量  默认15
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0.升序；1.降序（默认）；
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200,
                "pagination": {
                    "total": 3,
                    "count": 1,
                    "per_page": 1,
                    "current_page": 1,
                    "total_pages": 3,
                    "links": {
                    "next": "http://saas.me/demand/itemList?page=2"
                }
     *      },
     *      "data": [
            {
            "item": {
                "id": 1,
                "user_id": 1,
                "design_type": 1,
                "status": 2,
                "created_at": "2017-04-06 14:17:09",
                "updated_at": "2017-04-11 17:43:47",
                "recommend": "1",
                "ord_recommend": "",
                "type": 2,
                "design_company_id": 0,
                "contract_id": 0,
                "price": "0.00",
                "company_name": "",
                "company_abbreviation": "",
                "company_size": 0,
                "company_web": "",
                "company_province": 0,
                "company_city": 0,
                "company_area": 0,
                "address": "",
                "contact_name": "",
                "phone": "",
                "email": "",
                "quotation_id": 0,
                "stage_status": 0,
                "type_value": "UI UX设计类型",
                "design_type_value": "app设计",
                "company_province_value": "",
                "company_city_value": "",
                "company_area_value": "",
                "status_value": "项目进行中",
                "user": {
                    "id": 2,
                    "account": "18132382134",
                    "phone": "18132382134",
                    "username": "",
                    "email": null,
                    "design_company_id": 48,
                    "status": 0,
                    "item_sum": 0,
                    "created_at": "2017-04-14 10:35:13",
                    "updated_at": "2017-05-05 17:27:52",
                    "type": 0,
                    "logo": 0,
                    "role_id": 0,
                    "demand_company_id": 0,
                    "logo_image": []
                }
            },
            "info": {
                "id": 2,
                "item_id": 1,
                "system": 1,
                "design_content": 0,
                "page_number": 0,
                "name": "",
                "stage": 0,
                "complete_content": 0,
                "other_content": "",
                "style": 0,
                "start_time": 0,
                "cycle": 0,
                "design_cost": 0,
                "province": 0,
                "city": 0,
                "summary": "",
                "artificial": 0,
                "created_at": "2017-04-06 18:03:16",
                "updated_at": "2017-04-06 18:03:16",
                "image": [],
                "system_value": "IOS",
                "design_content_value": "",
                "stage_value": "",
                "complete_content_value": "",
                "design_cost_value": "",
                "province_value": "",
                "city_value": ""
            }
            },
     * }
     */
    public function itemList(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;

        if($request->input('sort') == 0 && $request->input('sort') !== null)
        {
            $sort = 'asc';
        }
        else
        {
            $sort = 'desc';
        }

        $query = Item::with(['user',]);
        switch ($request->input('type')){
            case 1:
                $query = $query->where('status', 1);
                break;
            case 2:
                $query = $query->where('status', 2);
                break;
            default:
        }
        $lists = $query->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($lists, new ItemTransformer)->setMeta($this->apiMeta());
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
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try{
            $design = DesignCompanyModel::where(['status' => 1, 'verify_status' => 1])
                ->whereIn('id', $all['recommend'])
                ->get()->pluck('id')->all();
            if(empty($design)){
                return $this->response->array($this->apiError('推荐设计公司都不符合条件', 403));
            }

            $item = Item::find($all['item_id']);
            //验证
            $ord_recommend = $item->ord_recommend;
            if(!empty($ord_recommend)){
                $ord_recommend_arr = explode(',', $ord_recommend);
                $design = array_diff($design, $ord_recommend_arr);
            }

            $item->recommend = $item->recommend . ',' . implode(',', $design);
            $item->save();
        }
        catch (\Exception $e){
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
        if(!$item){
            return $this->response->array($this->apiError('not found', 404));
        }

        if($item->status != 2){
            return $this->response->array($this->apiError('当前状态不可操作', 403));
        }

        if(empty($item->recommend)){
            return $this->response->array($this->apiError('当前项目没有推荐设计公司', 403));
        }

        $item->status = 3;
        $item->save();

        //触发事件
        event(new ItemStatusEvent($item));

        return $this->response->array($this->apiSuccess());
    }

}