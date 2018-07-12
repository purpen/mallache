<?php

namespace App\Http\Controllers\Api\Jd;

use App\Http\JdTransformer\ItemListTransformer;
use App\Http\JdTransformer\ItemTransformer;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;

class JdItemController extends Controller
{
    /**
     * @api {get} /jd/item/lists 项目列表
     * @apiVersion 1.0.0
     * @apiName JdItem lists
     * @apiGroup JdItem
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
        $login_user_id = $this->auth_user_id;
        $source_admin = User::sourceAdmin($login_user_id);
        if($source_admin != 1){
            return $this->response->array($this->apiSuccess('登陆用户没有权限查看', 403));
        }
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


        $lists = $query->where('source' , 1)->paginate($per_page);

        return $this->response->paginator($lists, new ItemListTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /jd/item/show 项目详情
     * @apiVersion 1.0.0
     * @apiName JdItem show
     * @apiGroup JdItem
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
        $login_user_id = $this->auth_user_id;
        $source_admin = User::sourceAdmin($login_user_id);
        if($source_admin != 1){
            return $this->response->array($this->apiSuccess('登陆用户没有权限查看', 403));
        }
        $id = $request->input('id');
        if (!$item = Item::find($id)) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        return $this->response->item($item, new ItemTransformer())->setMeta($this->apiMeta());
    }

}
