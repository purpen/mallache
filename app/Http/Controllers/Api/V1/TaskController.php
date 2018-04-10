<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Transformer\TaskTransformer;
use App\Models\ItemUser;
use App\Models\Task;
use App\Models\TaskUser;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskController extends BaseController
{
    /**
     * @api {post} /tasks 任务创建
     * @apiVersion 1.0.0
     * @apiName  tasks store
     * @apiGroup tasks
     *
     * @apiParam {integer} tier 层级 默认0
     * @apiParam {integer} pid 父id 默认0
     * @apiParam {integer} execute_user_id 执行人id 默认0
     * @apiParam {string} name 名称
     * @apiParam {string} summary 备注
     * @apiParam {array} tags 标签
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} over_time 完成时间
     * @apiParam {integer} item_id 所属项目ID
     * @apiParam {integer} level 优先级：1.普通；5.紧级；8.非常紧级；
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


        $tier = $request->input('tier') ? (int)$request->input('tier') : 0;
        $pid = $request->input('pid') ? (int)$request->input('pid') : 0;
        $execute_user_id = $request->input('execute_user_id') ? (int)$request->input('execute_user_id') : 0;
        $type = $request->input('type') ? (int)$request->input('type') : 1;
        $item_id = $request->input('item_id') ? (int)$request->input('item_id') : 0;
        $level = $request->input('level') ? (int)$request->input('level') : 1;
        $stage = $request->input('stage') ? (int)$request->input('stage') : 0;
        $start_time = $request->input('start_time') ? (int)$request->input('start_time') : null;
        $over_time = $request->input('start_time') ? (int)$request->input('over_time') : null;
        $tags = $request->input('tags') ? $request->input('tags') : [];
        $summary = $request->input('summary') ? (int)$request->input('summary') : '';
        $params = array(
            'name' => $request->input('name'),
            'tags' => implode(',' , $tags),
            'summary' => $summary,
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'item_id' => $item_id,
            'level' => $level,
            'stage' => $stage,
            'start_time' => $start_time,
            'over_time' => $over_time,
            'status' => 1,
            'execute_user_id' => $execute_user_id,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try {
            DB::beginTransaction();
            //如果是主任务，任务数量为0。如果是子任务，任务数量+1
            if($tier == 0){
                if($pid !== 0 ){
                    DB::rollBack();
                    return $this->response->array($this->apiError('主任务父id必须为0', 412));
                }
                $tasks = Task::create($params);
            }else{
                //如果父id为0的话，就返回，子任务必须有父id
                if($pid == 0){
                    DB::rollBack();
                    return $this->response->array($this->apiError('父id不能为0', 412));
                }else{
                    //查看父id的任务，添加父id里面的子任务数量
                    $task_pid = Task::find($pid);
                    if(!$task_pid){
                        DB::rollBack();
                        return $this->response->array($this->apiError('没有找到父id为'.$pid.'的任务', 404));
                    }else{
                        $sub_count = $task_pid->sub_count;
                        $sub_count += 1 ;
                        $task_pid->sub_count = $sub_count;
                        if($task_pid->save()){
                            $params['pid'] = $pid;
                            $tasks = Task::create($params);
                        }
                    }


                }

            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }

        return $this->response->item($tasks, new TaskTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {get} /tasks  任务列表
     * @apiVersion 1.0.0
     * @apiName tasks index
     * @apiGroup tasks
     *
     * @apiParam {integer} stage 默认10；0.未完成 2.已完成 10.全部
     * @apiParam {integer} item_id 项目id
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0:升序；1.降序(默认)
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
    public function index(Request $request)
    {
        $item_id = $request->input('item_id') ? (int)$request->input('item_id') : 0;
        $per_page = $request->input('per_page') ?? $this->per_page;
        $stage = $request->input('stage') ? $request->input('stage') : 0;
        if($request->input('sort') == 0 && $request->input('sort') !== null)
        {
            $sort = 'asc';
        }
        else
        {
            $sort = 'desc';
        }
        $user_id = intval($this->auth_user_id);
        $itemUsers = ItemUser::where('item_id' , $item_id)->get();
        $item_user_id = [];
        foreach ($itemUsers as $itemUser){
            $item_user_id[] = $itemUser->user_id;
        }
        $new_user_id = $item_user_id;
        if(in_array($user_id , $new_user_id)){
            if(in_array($stage,[0,2])){
                $tasks= Task::where(['item_id' => (int)$item_id , 'status' => 1 , 'stage' => $stage ])->orderBy('id', $sort)->paginate($per_page);
            }else{
                $tasks= Task::where(['item_id' => (int)$item_id , 'status' => 1])->orderBy('id', $sort)->paginate($per_page);
            }
        }else{
            return $this->response->array($this->apiError('没有权限查看', 403));
        }


        return $this->response->paginator($tasks, new TaskTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {get} /tasks/{id}  任务详情
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
            return $this->response->array($this->apiError('该任务已禁用！', 403));
        }

        return $this->response->item($tasks, new TaskTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /tasks/{id} 任务修改
     * @apiVersion 1.0.0
     * @apiName  tasks update
     * @apiGroup tasks
     *
     * @apiParam {integer} tier 层级 默认0
     * @apiParam {integer} pid 父id 默认0
     * @apiParam {integer} execute_user_id 执行人id 默认0
     * @apiParam {string} name 名称
     * @apiParam {string} summary 备注
     * @apiParam {array} tags 标签
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} over_time 完成时间
     * @apiParam {integer} item_id 所属项目ID
     * @apiParam {integer} level 优先级：1.普通；5.紧级；8.非常紧级；
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
        $execute_user_id = $request->input('execute_user_id') ? (int)$request->input('execute_user_id') : 0;
        $tags = $request->input('tags') ? $request->input('tags') : [];
        $summary = $request->input('summary') ? (int)$request->input('summary') : '';

        $params = array(
            'name' => $request->input('name'),
            'tags' => implode(',' , $tags),
            'summary' => $summary,
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'item_id' => $item_id,
            'level' => $level,
            'stage' => $stage,
            'start_time' => $start_time,
            'over_time' => $over_time,
            'status' => 1,
            'execute_user_id' => $execute_user_id,
        );
        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        //检验是否存在任务
        $tasks = Task::find($id);
        if (!$tasks) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $tasks->update($params);
        if (!$tasks) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($tasks, new TaskTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {delete} /tasks/{id} 任务删除
     * @apiVersion 1.0.0
     * @apiName tasks destroy
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
    public function destroy($id)
    {
        $tasks = Task::find($id);
        //检验是否存在
        if (!$tasks) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $child_tasks = Task::where('pid' , $id)->get();
        foreach ($child_tasks as $child_task){
            $child_task->delete();
        }

        $ok = $tasks->delete();
        if (!$ok) {
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /tasks/is/stage 更改主／子任务完成与未完成
     * @apiVersion 1.0.0
     * @apiName tasks stage
     * @apiGroup tasks
     *
     * @apiParam {integer} tier 0.主任务；1.子任务
     * @apiParam {integer} task_id 任务id
     * @apiParam {integer} stage 0.未完成；2.已完成
     * @apiParam {token} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     */
    public function stage(Request $request)
    {
        $task_id = $request->input('task_id');
        $stage = $request->input('stage');
        $tier = $request->input('tier');
        $task = Task::find($task_id);
        if(!$task){
            return $this->response->array($this->apiError('not found task!', 404));
        }
        try {
            DB::beginTransaction();
            //主任务走上面，子任务走下面
            if($tier == 0){
                //根据pid查询子任务
                $pid_tasks = Task::where('pid' , $task_id)->get();
                foreach ($pid_tasks as $pid_task){
                    if($pid_task->stage == 0){
                        DB::rollBack();
                        return $this->response->array($this->apiError('子任务'.$pid_task->name.'没有完成!', 412));
                    }
                }
                $ok = $task::isStage($task_id , $stage);
            }else{
                // 查找任务，任务id为子任务pid的任务
                $p_task = Task::where('id' , $task->pid)->first();
                if(!$p_task){
                    DB::rollBack();
                    return $this->response->array($this->apiError('没有找到该子任务的主任务', 404));
                }
                //子任务设置成未完成的话，完成数量-1。完成，完成数量+1
                if($stage == 0){
                    if($p_task->sub_finfished_count == 0){
                        $p_task->sub_finfished_count = 0;
                        $p_task->save();
                    }else{
                        $p_task->sub_finfished_count -= 1;
                        $p_task->save();
                    }
                }else{
                    if($p_task->sub_count == $p_task->sub_finfished_count){
                        $p_task->sub_finfished_count = $p_task->sub_count;
                        $p_task->save();
                    }else{
                        $p_task->sub_finfished_count += 1;
                        $p_task->save();

                    }

                }
                $ok = $task::isStage($task_id , $stage);
            }
            DB::commit();

            if(!$ok){
                return $this->response->array($this->apiError());
            }
            return $this->response->array($this->apiSuccess());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }

    }


    /**
     * @api {post} /tasks/executeUser 领取任务
     * @apiVersion 1.0.0
     * @apiName tasks executeUser
     * @apiGroup tasks
     *
     * @apiParam {integer} task_id 任务id
     * @apiParam {integer} execute_user_id 设定执行任务的用户id
     * @apiParam {token} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     */
    public function executeUser(Request $request)
    {
        $task_id = $request->input('task_id');
        $execute_user_id = $request->input('execute_user_id') ? $request->input('execute_user_id') : 0;
        $task = Task::find($task_id);
        if(!$task){
            return $this->response->array($this->apiError('没有找到该任务!', 404));
        }
        $taskUsers = TaskUser::where('task_id' , $task_id)->get();
        $user_id = [];
        foreach ($taskUsers as $taskUser){
            $user_id[] = $taskUser->user_id;
        }
        $new_user_id = $user_id;
        if(!in_array($execute_user_id , $new_user_id)){
            return $this->response->array($this->apiError('请先添加该用户到任务用户列表!', 403));
        }
        $task->execute_user_id = $execute_user_id;
        if($task->save()){
            return $this->response->array($this->apiSuccess());
        }
    }
}
