<?php

/**
 * 项目级别配置
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ItemLevelTransformer;
use App\Http\Transformer\ItemListTransformer;
use App\Models\ItemLevel;
use App\Models\User;
use Dingo\Api\Contract\Http\Request;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\StoreResourceFailedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ItemLevelController extends BaseController
{

    /**
     * @api {post} /itemLevels 创建
     * @apiVersion 1.0.0
     * @apiName  itemLevels store
     * @apiGroup itemLevels
     * @apiParam {string} name 名称
     * @apiParam {string} summary 描述
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 2,
                "name": "test1",
                "summary": "test1",
                "user_id": 5,
                "type": 1,
                "status": 1,
                "created_at": 1521018316
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function store(Request $request)
    {
        $user_id = $this->auth_user_id;
        $user = User::where('id' , $user_id)->first();
        if($user->child_account == 1){
            return $this->response->array($this->apiError('该用户不是主账户', 403));
        }
        // 验证规则
        $rules = [
            'name' => 'max:20',
            'summary' => 'max:500',
        ];
        $messages = [
            'name.max' => '最多20字符',
            'summary.max' => '最多500字符',
        ];


        $type = $request->input('type') ? (int)$request->input('type') : 1;
        $name = $request->input('name') ? $request->input('name') : '';
        $summary = $request->input('summary') ? $request->input('summary') : '';


        $params = array(
            'name' => $name,
            'summary' => $summary,
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'status' => 1,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try {
            $itemLevels = ItemLevel::create($params);

        } catch (\Exception $e) {

            throw new HttpException($e->getMessage());
        }

        return $this->response->item($itemLevels, new ItemLevelTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /itemLevels 列表
     * @apiVersion 1.0.0
     * @apiName itemLevels index
     * @apiGroup itemLevels
     *
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
     * @apiParam {int} sort 0:升序；1.降序(默认)
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 2,
                "name": "test1",
                "summary": "test1",
                "user_id": 5,
                "type": 1,
                "status": 1,
                "created_at": 1521018316
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function index(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $user_id = $this->auth_user_id;
        $user = User::where('id' , $user_id)->first();
        if($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        if($user->child_account == 1){
            return $this->response->array($this->apiError('该用户不是主账户', 403));
        }
        $itemLevels = ItemLevel::orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($itemLevels, new ItemLevelTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {get} /itemLevels/{id}  详情
     * @apiVersion 1.0.0
     * @apiName itemLevels show
     * @apiGroup itemLevels
     *
     * @apiParam {string} token
     *
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 2,
                    "name": "test1",
                    "summary": "test1",
                    "user_id": 5,
                    "type": 1,
                    "status": 1,
                    "created_at": 1521018316
                },
                "meta": {
                    "message": "Success",
                    "status_code": 200
            }
        }
     */
    public function show($id)
    {
        $user_id = $this->auth_user_id;
        $user = User::where('id' , $user_id)->first();
        if($user->child_account == 1){
            return $this->response->array($this->apiError('该用户不是主账户', 403));
        }
        $itemLevels = ItemLevel::find($id);

        if (!$itemLevels) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($itemLevels, new ItemLevelTransformer())->setMeta($this->apiMeta());


    }

    /**
     * @api {put} /itemLevels/{id} 更新
     * @apiVersion 1.0.0
     * @apiName  itemLevels update
     * @apiGroup itemLevels
     * @apiParam {string} name 名称
     * @apiParam {string} summary 描述
     * @apiParam {string} token
     *
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 2,
                "name": "test1",
                "summary": "test1",
                "user_id": 5,
                "type": 1,
                "status": 1,
                "created_at": 1521018316
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function update(Request $request , $id)
    {
        $user_id = $this->auth_user_id;
        $user = User::where('id' , $user_id)->first();
        if($user->child_account == 1){
            return $this->response->array($this->apiError('该用户不是主账户', 403));
        }
        // 验证规则
        $rules = [
            'name' => 'max:20',
            'summary' => 'max:500',
        ];
        $messages = [
            'name.max' => '最多20字符',
            'summary.max' => '最多500字符',
        ];

        $type = $request->input('type') ? (int)$request->input('type') : 1;
        $name = $request->input('name') ? $request->input('name') : '';
        $summary = $request->input('summary') ? $request->input('summary') : '';

        $params = array(
            'name' => $name,
            'summary' => $summary,
            'user_id' => $this->auth_user_id,
            'type' => $type,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        //检验是否存在该作品
        $itemLevels = ItemLevel::find($id);
        if (!$itemLevels) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的案例
        if ($itemLevels->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('没有权限更改!', 403));
        }
        $itemLevels->update($params);
        if (!$itemLevels) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($itemLevels, new ItemLevelTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /itemLevels/{id} 删除
     * @apiVersion 1.0.0
     * @apiName itemLevels delete
     * @apiGroup itemLevels
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
        $user_id = $this->auth_user_id;
        $user = User::where('id' , $user_id)->first();
        if($user->child_account == 1){
            return $this->response->array($this->apiError('该用户不是主账户', 403));
        }
        //检验是否存在
        $itemLevels = ItemLevel::find($id);
        if (!$itemLevels) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的作品
        if ($itemLevels->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('没有权限删除!', 403));
        }
        $ok = $itemLevels->delete();
        if (!$ok) {
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }
}