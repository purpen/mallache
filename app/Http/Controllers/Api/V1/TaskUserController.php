<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Transformer\TaskUserTransformer;
use App\Http\Transformer\UserTaskUserTransformer;
use App\Http\Transformer\UserTransformer;
use App\Models\TaskUser;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskUserController extends BaseController
{

    /**
     * @api {post} /taskUsers 任务成员创建(暂时不用)
     * @apiVersion 1.0.0
     * @apiName  taskUsers store
     * @apiGroup taskUsers
     *
     * @apiParam {integer} task_id 任务id
     * @apiParam {array} user_id_arr 项目成员用户列表id(可以多选)
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 1,
                "task_id": 11,
                "user_id": 3,
                "type": 1,
                "status": 1,
                "created_at": 1522155497
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'task_id' => 'required|integer',
            'user_id_arr' => 'required|array',
        ]);
        $task_id = $request->input('task_id');
        $user_id_arr = $request->input('user_id_arr');

        //遍历用户id，添加
        foreach ($user_id_arr as $user_id){
            $params = array(
                'task_id' => intval($task_id),
                'type' => 1,
                'status' => 1,
                'user_id' => $user_id,
            );
            //查看是否有没有创建过任务用户，有的话，跳出
            $taskUsers = TaskUser::where('task_id' , $task_id)->where('user_id' , $user_id)->first();
            if($taskUsers){
                continue;
            }
            $taskUsers = TaskUser::create($params);

        }
        return $this->response->item($taskUsers, new TaskUserTransformer())->setMeta($this->apiMeta());



    }

    /**
     * @api {get} /taskUsers/{id} 任务成员详情
     * @apiVersion 1.0.0
     * @apiName  taskUsers show
     * @apiGroup taskUsers
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "data": {
    "id": 1,
    "task_id": 11,
    "user_id": 3,
    "type": 1,
    "status": 1,
    "created_at": 1522155497
    },
    "meta": {
    "message": "Success",
    "status_code": 200
    }
    }
     */
    public function show($id)
    {
        $taskUsers = TaskUser::find($id);

        if (!$taskUsers) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($taskUsers->status == 0) {
            return $this->response->array($this->apiError('该任务已禁用！', 403));
        }

        return $this->response->item($taskUsers, new TaskUserTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /taskUsers 任务成员列表
     * @apiVersion 1.0.0
     * @apiName  taskUsers index
     * @apiGroup taskUsers
     *
     * @apiParam {integer} task_id 任务id
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
                    "logo_image": null,
                    "design_company_id": 52,
                    "role_id": 0,
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
                    "logo_image": null,
                    "design_company_id": 49,
                    "role_id": 20,
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
        if($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $task_id = $request->input('task_id');
        $taskUsers = TaskUser::where('task_id' , $task_id)->get();
        $user_id = [];
        foreach ($taskUsers as $taskUser){
            $user_id[] = $taskUser->user_id;
        }
        $new_user_id = $user_id;
        $users = User::whereIn('id',$new_user_id)->orderBy('id', $sort)->get();
        return $this->response->collection($users, new UserTaskUserTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /taskUsers/{id} 任务成员删除
     * @apiVersion 1.0.0
     * @apiName taskUsers delete
     * @apiGroup taskUsers
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
        $taskUsers = TaskUser::find($id);
        //检验是否存在
        if (!$taskUsers) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        $ok = $taskUsers->delete();
        if (!$ok) {
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {post} /taskUsers/newStore 新任务成员创建
     * @apiVersion 1.0.0
     * @apiName  taskUsers newStore
     * @apiGroup taskUsers
     *
     * @apiParam {integer} task_id 任务id
     * @apiParam {integer} selected_user_id 选择的用户
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "data": {
    "id": 1,
    "task_id": 11,
    "user_id": 3,
    "type": 1,
    "status": 1,
    "created_at": 1522155497
    },
    "meta": {
    "message": "Success",
    "status_code": 200
    }
    }
     */
    public function newStore(Request $request)
    {
        $this->validate($request, [
            'task_id' => 'required|integer',
            'selected_user_id' => 'required|integer',
        ]);
        $task_id = $request->input('task_id');
        $selected_user_id = $request->input('selected_user_id');

        $params = array(
            'task_id' => intval($task_id),
            'type' => 1,
            'status' => 1,
            'selected_user_id' => $selected_user_id,
            'user_id' => $this->auth_user_id,
        );
        //查看是否有没有创建过任务用户，有的话，跳出
        $taskUsers = TaskUser::where('task_id' , $task_id)->where('selected_user_id' , $selected_user_id)->first();
        if($taskUsers){
            return $this->response->array($this->apiError('已存在该任务成员', 412));
        }
        $taskUsers = TaskUser::create($params);

        return $this->response->item($taskUsers, new TaskUserTransformer())->setMeta($this->apiMeta());
    }
}
