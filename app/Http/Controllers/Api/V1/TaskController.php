<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Transformer\StatisticalTransformer;
use App\Http\Transformer\TaskChildTransformer;
use App\Http\Transformer\TaskTransformer;
use App\Models\DesignProject;
use App\Models\ItemUser;
use App\Models\ProductDesign;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskUser;
use App\Models\User;
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
     * @apiParam {string} name 名称
     * @apiParam {string} summary 备注
     * @apiParam {array} tags 标签
     * @apiParam {array} selected_user_id 选择的用户id
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} over_time 完成时间
     * @apiParam {integer} item_id 所属项目ID
     * @apiParam {integer} stage_id 阶段ID
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
                "stage_id": 1,
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

        $tier = $request->input('tier') ? (int)$request->input('tier') : 0;
        $pid = $request->input('pid') ? (int)$request->input('pid') : 0;
        $type = $request->input('type') ? (int)$request->input('type') : 1;
        $item_id = $request->input('item_id') ? (int)$request->input('item_id') : 0;
        if($item_id == 0){
            return $this->response->array($this->apiError('项目id不能为0', 412));
        }
        $level = $request->input('level') ? (int)$request->input('level') : 1;
        $stage = $request->input('stage') ? (int)$request->input('stage') : 0;
        $start_time = $request->input('start_time') ? $request->input('start_time') : null;
        $over_time = $request->input('over_time') ? $request->input('over_time') : null;
        $tags = $request->input('tags') ? $request->input('tags') : [];
        $selected_user_id_arr = $request->input('selected_user_id') ? $request->input('selected_user_id') : [];
        $summary = $request->input('summary') ? $request->input('summary') : '';
        $name= $request->input('name') ? $request->input('name') : '';
        $stage_id= $request->input('stage_id') ? $request->input('stage_id') : 0;
        $params = array(
            'name' => $name,
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
            'execute_user_id' => $this->auth_user_id,
            'stage_id' => $stage_id,
            'tier' => $tier,
        );

        try {
            DB::beginTransaction();
            //如果是主任务，任务数量为0。如果是子任务，任务数量+1
            if($tier == 0){
                if($pid !== 0 ){
//                    DB::rollBack();
                    return $this->response->array($this->apiError('主任务父id必须为0', 412));
                }
                $tasks = Task::create($params);
                if($tasks){
                    $task_user = new TaskUser();
                    $task_user->user_id = $this->auth_user_id;
                    $task_user->task_id = $tasks->id;
                    $task_user->selected_user_id =  $this->auth_user_id;
                    $task_user->type = 1;
                    $task_user->status = 1;
                    $task_user->save();
                }
                //如果选中的用户不为空，把用户更新到用户成员里
                if(!empty($selected_user_id_arr)){
                    foreach ($selected_user_id_arr as $selected_user_id){
                        //检查又没有创建过任务成员，创建过返回，没有创建过创建
                        $find_task_user = TaskUser::where('task_id' , $tasks->id)->where('selected_user_id' , $selected_user_id)->first();
                        if($find_task_user){
                            continue;
                        }else{
                            $task_user = new TaskUser();
                            $task_user->user_id = $this->auth_user_id;
                            $task_user->task_id = $tasks->id;
                            $task_user->selected_user_id = $selected_user_id;
                            $task_user->type = 1;
                            $task_user->status = 1;
                            $task_user->save();
                        }
                    }
                }
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
                            if($tasks){
                                $task_user = new TaskUser();
                                $task_user->user_id = $this->auth_user_id;
                                $task_user->task_id = $tasks->id;
                                $task_user->selected_user_id =  $this->auth_user_id;
                                $task_user->type = 1;
                                $task_user->status = 1;
                                $task_user->save();
                            }
                            //如果选中的用户不为空，把用户更新到用户成员里
                            if(!empty($selected_user_id_arr)){
                                foreach ($selected_user_id_arr as $selected_user_id){
                                    //检查又没有创建过任务成员，创建过返回，没有创建过创建
                                    $find_task_user = TaskUser::where('task_id' , $tasks->id)->where('selected_user_id' , $selected_user_id)->first();
                                    if($find_task_user){
                                        continue;
                                    }else{
                                        $task_user = new TaskUser();
                                        $task_user->user_id = $this->auth_user_id;
                                        $task_user->task_id = $tasks->id;
                                        $task_user->selected_user_id = $selected_user_id;
                                        $task_user->type = 1;
                                        $task_user->status = 1;
                                        $task_user->save();
                                    }
                                }
                            }
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
     * @apiParam {integer} stage 默认10；0.全部 2.已完成 -1.未完成  默认0
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
        $item_id = $request->input('item_id') ? intval($request->input('item_id')) : 0;
        if($item_id == 0){
            return $this->response->array($this->apiError('项目id不能为0', 412));
        }
        $per_page = $request->input('per_page') ? intval($request->input('per_page')) : $this->big_per_page;
        $stage = $request->input('stage') ? intval($request->input('stage')) : 0;
        if($request->input('sort') == 0 && $request->input('sort') !== null)
        {
            $sort = 'asc';
        }
        else
        {
            $sort = 'desc';
        }
        $user_id = intval($this->auth_user_id);
        //检查是否有查看的权限
        $itemUser = ItemUser::checkUser($item_id , $user_id);
        if($itemUser == false){
            return $this->response->array($this->apiError('没有权限查看', 403));
        }

        if($stage !== 0){
            if($stage == -1){
                $stage = 0;
            }
            $tasks= Task::where(['item_id' => (int)$item_id , 'status' => 1 , 'stage' => $stage ])->orderBy('id', $sort)->paginate($per_page);
        }else{
            $tasks= Task::where(['item_id' => (int)$item_id , 'status' => 1])->orderBy('id', $sort)->paginate($per_page);
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
        $task = Task::find($id);
        if (!$task) {
            return $this->response->array($this->apiError('not found', 404));
        }
        //查看子任务
        $taskChild = Task::where('pid' , $id)->get();
        $task['childTask'] = $taskChild;
        $tagsAll = $task->tags;
        if(!empty($tagsAll)){
            $tagsAll = explode(',' , $tagsAll);
            $task['tagsAll'] = Tag::whereIn('id' , $tagsAll)->get();
        }else{
            $task['tagsAll'] = [];
        }
        if ($task->status == 0) {
            return $this->response->array($this->apiError('该任务已禁用！', 403));
        }

        return $this->response->item($task, new TaskChildTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /tasks/{id} 任务修改
     * @apiVersion 1.0.0
     * @apiName  tasks update
     * @apiGroup tasks
     *
     * @apiParam {integer} execute_user_id 执行人id 默认0
     * @apiParam {string} name 名称
     * @apiParam {string} summary 备注
     * @apiParam {array} tags 标签
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} over_time 完成时间
     * @apiParam {integer} stage_id 阶段ID
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
                "stage_id": 0,
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
    {
        $user_id = $this->auth_user_id;

        //检验是否存在任务
        $tasks = Task::find($id);
        if (!$tasks) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检查是否有查看的权限
        $itemUser = ItemUser::checkUser($tasks->item_id , $user_id);
        if($itemUser == false){
            return $this->response->array($this->apiError('没有权限查看该项目', 403));
        }
        $all = $request->except(['token']);
        //不为空标签时，合并数组
        if(!empty($all['tags'])){
            $all['tags'] = implode(',' , $all['tags']);
        }
        //移除没有更新的值，只要更新的
        $new_all = array_diff($all , array(null));
        $tasks->update($new_all);
        //更改完任务，查看标签是否为空，不为空分割成数组
        if(!empty($tasks->tags)){
            $new_tags = explode(',' , $tasks->tags);
            $tasks['tagsAll'] = Tag::whereIn('id' , $new_tags)->get();

        }
        if (!$tasks) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($tasks, new TaskChildTransformer())->setMeta($this->apiMeta());

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
        //检验是否是当前用户创建的作品
        if ($tasks->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('没有权限删除!', 403));
        }
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
     * @api {put} /isStage/tasks 更改主／子任务完成与未完成
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
                //检测执行者与登录id是否是一个人
                if($task->execute_user_id !== $this->auth_user_id){
                    return $this->response->array($this->apiError('当前用户不是执行者，不能点击完成!', 403));
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
     * @apiParam {integer} item_id 项目id
     * @apiParam {integer} execute_user_id 执行者id
     * @apiParam {integer} task_id 任务id
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
        //项目id不能为0
        $item_id = $request->input('item_id') ? intval($request->input('item_id')) : 0;
        $task_id = $request->input('task_id') ? intval($request->input('task_id')) : 0;
        $execute_user_id = $request->input('execute_user_id') ? intval($request->input('execute_user_id')) : 0;

        if($item_id == 0){
            return $this->response->array($this->apiError('项目id不能为0', 412));
        }
        $task = Task::find($task_id);
        if(!$task){
            return $this->response->array($this->apiError('没有找到该任务!', 404));
        }
        $user_id = $this->auth_user_id;
        //检查是否有查看的权限
        $itemUser = ItemUser::checkUser($item_id , $user_id);
        if($itemUser == false){
            return $this->response->array($this->apiError('没有权限查看该项目', 403));
        }
        $task->execute_user_id = $execute_user_id;
        if($task->save()){
            return $this->response->array($this->apiSuccess());
        }
    }

    /**
     * @api {get} /statistical/tasks  任务统计
     * @apiVersion 1.0.0
     * @apiName tasks statistical
     * @apiGroup tasks
     *
     * @apiParam {integer} item_id 项目id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "meta": {
    "message": "获取成功",
    "status_code": 200
    },
    "data": {
    "no_get": 1,       //未领取
    "no_stage": 2,    //未完成
    "ok_stage": 1,    //已完成
    "overdue": 0,   //已预期
    "total_count": 3,   //任务总数
    "no_get_percentage": "33",  //未领取百分比
    "no_stage_percentage": "66", //未完成百分比
    "ok_stage_percentage": "33", //已完成百分比
    "overdue_percentage": "0"  //已预期百分比
    }
    }
     */
    public function statistical(Request $request)
    {
        $item_id = $request->input('item_id');
        $tasks = Task::where('item_id' , $item_id)->get();
        if(!empty($tasks)){
            //总数量
            $total_count = $tasks->count();
            //未领取
            $no_get = 0;
            //未完成
            $no_stage = 0;
            //已完成
            $ok_stage = 0;
            //已预期
            $overdue = 0;
            //当前时间
            $current_time = date('Y-m-d H:i:s');
            foreach ($tasks as $task){
                //未领取
                if($task->execute_user_id == 0){
                    $no_get += 1;
                }
                //未完成
                if($task->stage == 0){
                    $no_stage += 1;
                }
                //已完成
                if($task->stage == 2){
                    $ok_stage += 1;
                }
                //已预期
                $over_time = $task->over_time;
                if($over_time > $current_time && $task->stage == 0){
                    $overdue += 1;
                }
            }
            //未领取百分比
            $no_get_percentage = round(($no_get / $total_count) * 100 , 0);
            //未完成百分比
            $no_stage_percentage = round(($no_stage / $total_count) * 100 , 0);
            //已完成百分比
            $ok_stage_percentage = round(($ok_stage / $total_count) * 100 , 0);
            //已预期百分比
            $overdue_percentage = round(($overdue / $total_count) * 100 , 0);

            $statistical = [];
            $statistical['no_get'] = $no_get;
            $statistical['no_stage'] = $no_stage;
            $statistical['ok_stage'] = $ok_stage;
            $statistical['overdue'] = $overdue;
            $statistical['total_count'] = $total_count;
            $statistical['no_get_percentage'] = $no_get_percentage;
            $statistical['no_stage_percentage'] = $no_stage_percentage;
            $statistical['ok_stage_percentage'] = $ok_stage_percentage;
            $statistical['overdue_percentage'] = $overdue_percentage;

            return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistical));

        }
        return $this->response->array($this->apiError('该项目下没有任务', 404));

    }

    /**
     * @api {get} /statistical/userTasks  个人任务统计
     * @apiVersion 1.0.0
     * @apiName tasks userStatistical
     * @apiGroup tasks
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "meta": {
    "message": "获取成功",
    "status_code": 200
    },
    "data": {
    "no_get": 1,       //未领取
    "no_stage": 2,    //未完成
    "ok_stage": 1,    //已完成
    "overdue": 0,   //已预期
    "total_count": 3,   //任务总数
    "no_get_percentage": "33",  //未领取百分比
    "no_stage_percentage": "66", //未完成百分比
    "ok_stage_percentage": "33", //已完成百分比
    "overdue_percentage": "0"  //已预期百分比
    }
    }
     */
    public function userStatistical(Request $request)
    {
        $user_id = $this->auth_user_id;
        $taskUsers = TaskUser::where('selected_user_id' , $user_id)->get();
        if(!empty($taskUsers)){
            //任务数组
            $task_id_array = [];
            foreach ($taskUsers as $taskUser){
                $task_id_array[] = $taskUser->task_id;
            }
            $tasks = Task::whereIn('id' , $task_id_array)->get();
            //总数量
            $total_count = $tasks->count();
            if(!empty($tasks)){
                //未领取
                $no_get = 0;
                //未完成
                $no_stage = 0;
                //已完成
                $ok_stage = 0;
                //已预期
                $overdue = 0;
                //当前时间
                $current_time = date('Y-m-d H:i:s');
                foreach ($tasks as $task){
                    //未领取
                    if($task->execute_user_id == 0 && $task->stage == 0){
                        $no_get += 1;
                    }
                    //未完成
                    if($task->stage == 0){
                        $no_stage += 1;
                    }
                    //已完成
                    if($task->stage == 2){
                        $ok_stage += 1;
                    }
                    //已预期
                    $over_time = $task->over_time;
                    if($over_time > $current_time && $task->stage == 0){
                        $overdue += 1;
                    }
                }
                //未领取百分比
                if($total_count != 0){
                    $no_get_percentage = round(($no_get / $total_count) * 100 , 0);
                }else{
                    $no_get_percentage = 0;
                }
                //未完成百分比
                if($total_count != 0){
                    $no_stage_percentage = round(($no_stage / $total_count) * 100 , 0);
                }else{
                    $no_stage_percentage = 0;
                }
                //已完成百分比
                if($total_count != 0){
                    $ok_stage_percentage = round(($ok_stage / $total_count) * 100 , 0);
                }else {
                    $ok_stage_percentage = 0;
                }
                //已预期百分比
                if($total_count != 0){
                    $overdue_percentage = round(($overdue / $total_count) * 100 , 0);
                }else{
                    $overdue_percentage = 0;
                }

                $statistical = [];
                $statistical['no_get'] = $no_get;
                $statistical['no_stage'] = $no_stage;
                $statistical['ok_stage'] = $ok_stage;
                $statistical['overdue'] = $overdue;
                $statistical['total_count'] = $total_count;
                $statistical['no_get_percentage'] = $no_get_percentage;
                $statistical['no_stage_percentage'] = $no_stage_percentage;
                $statistical['ok_stage_percentage'] = $ok_stage_percentage;
                $statistical['overdue_percentage'] = $overdue_percentage;

                return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistical));

            }
            return $this->response->array($this->apiError('没有参加任何任务', 404));

        }else{
            return $this->response->array($this->apiError('没有参加任何任务', 404));
        }
    }

}
