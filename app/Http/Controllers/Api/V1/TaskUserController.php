<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Transformer\TaskUserTransformer;
use App\Http\Transformer\UserTransformer;
use App\Models\TaskUser;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskUserController extends BaseController
{

    /**
     * @api {post} /taskUsers 任务成员创建
     * @apiVersion 1.0.0
     * @apiName  taskUsers store
     * @apiGroup taskUsers
     *
     * @apiParam {integer} task_id 任务id
     * @apiParam {integer} user_id 项目成员用户列表id
     * @apiParam {string} token
     */
    public function store(Request $request)
    {
        $task_id = $request->input('task_id');
        $user_id = $request->input('user_id');

        $params = array(
            'task_id' => $task_id,
            'user_id' => $user_id,
            'type' => 1,
            'status' => 1,
        );

        try {
            $taskUsers = TaskUser::create($params);
        } catch (\Exception $e) {

            throw new HttpException($e->getMessage());
        }

        return $this->response->item($taskUsers, new TaskUserTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /taskUsers/{id} 任务成员更改
     * @apiVersion 1.0.0
     * @apiName  taskUsers update
     * @apiGroup taskUsers
     *
     * @apiParam {integer} task_id 任务id
     * @apiParam {integer} user_id 项目成员用户列表id
     * @apiParam {string} token
     */
    public function update(Request $request , $id)
    {
        $task_id = $request->input('task_id');
        $user_id = $request->input('user_id');

        $params = array(
            'task_id' => $task_id,
            'user_id' => $user_id,
            'type' => 1,
            'status' => 1,
        );

        $taskUsers = TaskUser::find($id);
        if (!$taskUsers) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $taskUsers->update($params);
        if (!$taskUsers) {
            return $this->response->array($this->apiError());
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
        return $this->response->collection($users, new UserTransformer())->setMeta($this->apiMeta());
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
}
