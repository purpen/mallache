<?php

/**
 * 项目用户
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ItemUserTransformer;
use App\Models\ItemUser;
use App\Models\User;
use Dingo\Api\Contract\Http\Request;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\StoreResourceFailedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ItemUserController extends BaseController
{

    /**
     * @api {post} /itemUsers 创建
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
     * @api {get} /itemUsers 列表
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
    public function index(Request $request)
    {
        $item_id = $request->input('item_id') ? (int)$request->input('item_id') : 0;

        $per_page = $request->input('per_page') ?? $this->per_page;

        if($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $itemUsers = ItemUser::where('item_id' , $item_id)->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($itemUsers, new ItemUserTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {get} /itemUsers/{id}  详情
     * @apiVersion 1.0.0
     * @apiName itemUsers show
     * @apiGroup itemUsers
     *
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
    public function show($id)
    {
        $itemUser = ItemUser::find($id);

        if (!$itemUser) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($itemUser, new ItemUserTransformer())->setMeta($this->apiMeta());


    }

    /**
     * @api {put} /itemUsers/{id} 更新
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
        //检验是否是当前用户创建的的项目用户
        if ($itemUser->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('没有权限更改!', 403));
        }
        $itemUser->update($params);
        if (!$itemUser) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($itemUser, new ItemUserTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /itemUsers/{id} 删除
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