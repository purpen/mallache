<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Transformer\TaskTransformer;
use App\Models\Task;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskController extends BaseController
{
    /**
     * @api {post} /tasks 创建
     * @apiVersion 1.0.0
     * @apiName  tasks store
     * @apiGroup tasks
     * @apiParam {string} name 名称
     * @apiParam {string} summary 备注
     * @apiParam {string} tags 标签
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} over_time 完成时间
     * @apiParam {integer} item_id 所属项目ID
     * @apiParam {integer} level 优先级：1.普通；5.紧级；8.非常紧级；
     * @apiParam {integer} type 类型;1.默认；
     * @apiParam {integer} stage 是否完成:0.未完成；1.进行中；2.已完成；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 3,
                "name": "name1",
                "tags": "tag1",
                "summary": "summary1",
                "user_id": 1,
                "execute_user_id": 0,
                "item_id": 0,
                "level": 1,
                "type": 1,
                "sub_count": 0,
                "sub_finfished_count": 0,
                "love_count": 0,
                "collection_count": 0,
                "stage": 0,
                "status": 1,
                "start_time": null,
                "over_time": null,
                "created_at": 1521447655
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function store(Request $request)
    {
        // 验证规则
        $rules = [
            'name' => 'required|max:100',
            'tags' => 'max:500',
            'summary' => 'max:1000',
        ];
        $messages = [
            'name.required' => '名称不能为空',
            'name.max' => '名称最多50字符',
            'tags.max' => '标签最多500字符',
            'summary.max' => '备注最多1000字符',
        ];


        $type = $request->input('type') ? (int)$request->input('type') : 1;
        $item_id = $request->input('item_id') ? (int)$request->input('item_id') : 0;
        $level = $request->input('level') ? (int)$request->input('level') : 1;
        $stage = $request->input('stage') ? (int)$request->input('stage') : 0;
        $start_time = $request->input('start_time') ? (int)$request->input('start_time') : null;
        $over_time = $request->input('start_time') ? (int)$request->input('over_time') : null;

        $params = array(
            'name' => $request->input('name'),
            'tags' => $request->input('tags'),
            'summary' => $request->input('summary'),
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'item_id' => $item_id,
            'level' => $level,
            'stage' => $stage,
            'start_time' => $start_time,
            'over_time' => $over_time,
            'status' => 1,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try {
            $tasks = Task::create($params);
        } catch (\Exception $e) {

            throw new HttpException($e->getMessage());
        }

        return $this->response->item($tasks, new TaskTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {get} /tasks  列表
     * @apiVersion 1.0.0
     * @apiName tasks index
     * @apiGroup tasks
     *
     * @apiParam {string} token
     *
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 3,
                "name": "name1",
                "tags": "tag1",
                "summary": "summary1",
                "user_id": 1,
                "execute_user_id": 0,
                "item_id": 0,
                "level": 1,
                "type": 1,
                "sub_count": 0,
                "sub_finfished_count": 0,
                "love_count": 0,
                "collection_count": 0,
                "stage": 0,
                "status": 1,
                "start_time": null,
                "over_time": null,
                "created_at": 1521447655
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function index()
    {
        $user_id = intval($this->auth_user_id);
        $tasks= Task::where(['user_id'=>$user_id,'status'=>1])->orderBy('id', 'desc')->get();

        return $this->response->collection($tasks, new TaskTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {get} /tasks/{id}  详情
     * @apiVersion 1.0.0
     * @apiName tasks show
     * @apiGroup tasks
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 3,
                "name": "name1",
                "tags": "tag1",
                "summary": "summary1",
                "user_id": 1,
                "execute_user_id": 0,
                "item_id": 0,
                "level": 1,
                "type": 1,
                "sub_count": 0,
                "sub_finfished_count": 0,
                "love_count": 0,
                "collection_count": 0,
                "stage": 0,
                "status": 1,
                "start_time": null,
                "over_time": null,
                "created_at": 1521447655
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
         */
    public function show($id)
    {
        $id = intval($id);
        $tasks = Task::find($id);

        if (!$tasks) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($tasks->status == 0) {
            return $this->response->array($this->apiError('该任务已禁用！', 404));
        }

        return $this->response->item($tasks, new TaskTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /tasks/{id} 修改
     * @apiVersion 1.0.0
     * @apiName  tasks update
     * @apiGroup tasks
     * @apiParam {string} name 名称
     * @apiParam {string} summary 备注
     * @apiParam {string} tags 标签
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} over_time 完成时间
     * @apiParam {integer} item_id 所属项目ID
     * @apiParam {integer} level 优先级：1.普通；5.紧级；8.非常紧级；
     * @apiParam {integer} type 类型;1.默认；
     * @apiParam {integer} stage 是否完成:0.未完成；1.进行中；2.已完成；
     * @apiParam {string} token
     *
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 3,
                "name": "name1",
                "tags": "tag1",
                "summary": "summary1",
                "user_id": 1,
                "execute_user_id": 0,
                "item_id": 0,
                "level": 1,
                "type": 1,
                "sub_count": 0,
                "sub_finfished_count": 0,
                "love_count": 0,
                "collection_count": 0,
                "stage": 0,
                "status": 1,
                "start_time": null,
                "over_time": null,
                "created_at": 1521447655
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function update(Request $request , $id)
    {        // 验证规则
        $rules = [
            'name' => 'required|max:100',
            'tags' => 'max:500',
            'summary' => 'max:1000',
        ];
        $messages = [
            'name.required' => '名称不能为空',
            'name.max' => '名称最多50字符',
            'tags.max' => '标签最多500字符',
            'summary.max' => '备注最多1000字符',
        ];


        $type = $request->input('type') ? (int)$request->input('type') : 1;
        $item_id = $request->input('item_id') ? (int)$request->input('item_id') : 0;
        $level = $request->input('level') ? (int)$request->input('level') : 1;
        $stage = $request->input('stage') ? (int)$request->input('stage') : 0;
        $start_time = $request->input('start_time') ? (int)$request->input('start_time') : null;
        $over_time = $request->input('start_time') ? (int)$request->input('over_time') : null;

        $params = array(
            'name' => $request->input('name'),
            'tags' => $request->input('tags'),
            'summary' => $request->input('summary'),
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'item_id' => $item_id,
            'level' => $level,
            'stage' => $stage,
            'start_time' => $start_time,
            'over_time' => $over_time,
            'status' => 1,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        //检验是否存在任务
        $tasks = Task::find($id);
        if (!$tasks) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的案例
        if ($tasks->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $tasks->update($params);
        if (!$tasks) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($tasks, new TaskTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {delete} /tasks/{id} 删除
     * @apiVersion 1.0.0
     * @apiName tasks delete
     * @apiGroup tasks
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
     */
    public function delete($id)
    {
        $tasks = Task::find($id);
        //检验是否是当前用户创建的作品
        if ($tasks->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否存在
        if (!$tasks) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        $ok = $tasks->delete();
        if (!$ok) {
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }
}
