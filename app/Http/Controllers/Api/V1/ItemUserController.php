<?php

/**
 * 项目用户
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ItemUserTransformer;
use App\Http\Transformer\UserTransformer;
use App\Models\ItemUser;
use App\Models\User;
use Dingo\Api\Contract\Http\Request;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\StoreResourceFailedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ItemUserController extends BaseController
{

    /**
     * @api {post} /itemUsers 项目成员创建
     * @apiVersion 1.0.0
     * @apiName  itemUsers store
     * @apiGroup itemUsers
     * @apiParam {integer} user_id 用户id
     * @apiParam {integer} item_id 项目id
     * @apiParam {integer} level 默认1；级别：1.成员；3.项目负责人；5.商务负责人；
     * @apiParam {integer} is_creator 默认0；是否是创建者: 0.否；1.是；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 1,
                "user_id": 1,
                "item_id": 1,
                "level": 1,
                "is_creator": 1,
                "type": 1,
                "status": 1,
                "created_at": 1521547539
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function store(Request $request)
    {
        $user_id = $request->input('user_id') ? (int)$request->input('user_id') : 0;
        $item_id = $request->input('item_id') ? (int)$request->input('item_id') : 0;
        $level = $request->input('level') ? $request->input('level') : 1;
        $is_creator = $request->input('is_creator') ? $request->input('is_creator') : 0;


        $params = array(
            'user_id' => $user_id,
            'item_id' => $item_id,
            'level' => $level,
            'is_creator' => $is_creator,
            'type' => 1,
            'status' => 1,
        );

        try {
            $itemUsers = ItemUser::create($params);

        } catch (\Exception $e) {

            throw new HttpException($e->getMessage());
        }

        return $this->response->item($itemUsers, new ItemUserTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /itemUsers 项目成员列表
     * @apiVersion 1.0.0
     * @apiName itemUsers index
     * @apiGroup itemUsers
     *
     * @apiParam {integer} item_id 项目id
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
     * @apiParam {int} sort 0:升序；1.降序(默认)
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": [
                {
                    "id": 3,
                    "type": 2,
                    "account": "18132382133",
                    "username": "18132382133",
                    "email": null,
                    "phone": "18132382133",
                    "status": 0,
                    "item_sum": 0,
                    "price_total": "0.00",
                    "price_frozen": "0.00",
                    "cash": "0.00",
                    "logo_image": null,
                    "design_company_id": 52,
                    "role_id": 0,
                    "demand_company_id": 0,
                    "realname": null,
                    "child_account": 1,
                    "company_role": 20,
                    "invite_user_id": 0,
                    "design_company_name": "2222",
                    "design_company_abbreviation": "qwqwqe",
                    "created_at": 1494551407
                },
                {
                    "id": 1,
                    "type": 2,
                    "account": "15810295774",
                    "username": "",
                    "email": null,
                    "phone": "15810295774",
                    "status": 0,
                    "item_sum": 0,
                    "price_total": "0.00",
                    "price_frozen": "0.00",
                    "cash": "0.00",
                    "logo_image": null,
                    "design_company_id": 49,
                    "role_id": 20,
                    "demand_company_id": 0,
                    "realname": "1",
                    "child_account": 1,
                    "company_role": 20,
                    "invite_user_id": 0,
                    "design_company_name": "1",
                    "design_company_abbreviation": "1",
                    "created_at": 1492081381
                }
            ],
            "meta": {
                "message": "Success",
                "status_code": 200,
                "pagination": {
                    "total": 2,
                    "count": 2,
                    "per_page": 10,
                    "current_page": 1,
                    "total_pages": 1,
                    "links": []
                }
            }
        }
     */
    public function index(Request $request)
    {
        $item_id = $request->input('item_id') ? (int)$request->input('item_id') : 0;

        $per_page = $request->input('per_page') ?? $this->per_page;

        if($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $itemUsers = ItemUser::where('item_id' , $item_id)->get();
        $user_id = [];
        foreach ($itemUsers as $itemUser){
            $user_id[] = $itemUser->user_id;
        }
        $new_user_id = $user_id;
        $users = User::whereIn('id',$new_user_id)->orderBy('id', $sort)->paginate($per_page);
        return $this->response->paginator($users, new UserTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {get} /itemUsers  根据用户id查看项目成员详情
     * @apiVersion 1.0.0
     * @apiName itemUsers show
     * @apiGroup itemUsers
     *
     * @apiParam {integer} user_id 用户id
     * @apiParam {string} token
     *
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
            "id": 1,
            "user_id": 1,
            "item_id": 1,
            "level": 1,
            "is_creator": 1,
            "type": 1,
            "status": 1,
            "created_at": 1521547539
        },
        "meta": {
            "message": "Success",
            "status_code": 200
        }
        }
     */
    public function show(Request $request)
    {
        $user_id = $request->input('user_id');
        $itemUser = ItemUser::where('user_id' , $user_id)->first();

        if (!$itemUser) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($itemUser, new ItemUserTransformer())->setMeta($this->apiMeta());


    }

    /**
     * @api {put} /itemUsers/{id} 项目成员更新
     * @apiVersion 1.0.0
     * @apiName  itemUsers update
     * @apiGroup itemUsers
     * @apiParam {integer} user_id 用户id
     * @apiParam {integer} item_id 项目id
     * @apiParam {integer} level 默认1；级别：1.成员；3.项目负责人；5.商务负责人；
     * @apiParam {integer} is_creator 默认0；是否是创建者: 0.否；1.是；
     * @apiParam {string} token
     *
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
            "id": 1,
            "user_id": 1,
            "item_id": 1,
            "level": 1,
            "is_creator": 1,
            "type": 1,
            "status": 1,
            "created_at": 1521547539
        },
        "meta": {
            "message": "Success",
            "status_code": 200
        }
        }
     */
    public function update(Request $request , $id)
    {

        $user_id = $request->input('user_id') ? (int)$request->input('user_id') : 0;
        $item_id = $request->input('item_id') ? (int)$request->input('item_id') : 0;
        $level = $request->input('level') ? $request->input('level') : 1;
        $is_creator = $request->input('is_creator') ? $request->input('is_creator') : 0;


        $params = array(
            'user_id' => $user_id,
            'item_id' => $item_id,
            'level' => $level,
            'is_creator' => $is_creator,
            'type' => 1,
            'status' => 1,
        );

        //检验是否存在该项目用户
        $itemUser = ItemUser::find($id);
        if (!$itemUser) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $itemUser->update($params);
        if (!$itemUser) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($itemUser, new ItemUserTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /itemUsers/{id} 项目成员删除
     * @apiVersion 1.0.0
     * @apiName itemUsers delete
     * @apiGroup itemUsers
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *
     */
    public function destroy($id)
    {
        //检验是否存在
        $itemUser = ItemUser::find($id);
        if (!$itemUser) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的作品
        if ($itemUser->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('没有权限删除!', 403));
        }
        $ok = $itemUser->delete();
        if (!$ok) {
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }
}