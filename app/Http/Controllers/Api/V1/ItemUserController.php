<?php

/**
 * 项目用户
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ItemUserTransformer;
use App\Http\Transformer\UserTaskUserTransformer;
use App\Http\Transformer\UserTransformer;
use App\Models\DesignProject;
use App\Models\ItemUser;
use App\Models\Task;
use App\Models\TaskUser;
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


        $params = array(
            'user_id' => $user_id,
            'item_id' => $item_id,
            'type' => 1,
            'status' => 1,
        );

        //判断权限
        $designProject = DesignProject::where('id' , $item_id)->first();
        if(!$designProject){
            return $this->response->array($this->apiError('没有找到该项目!', 404));
        }
        if($designProject->isPower($this->auth_user_id) != true){
            return $this->response->array($this->apiError('没有权限添加!', 403));
        }

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
        $users = User::whereIn('id',$new_user_id)->orderBy('id', $sort)->get();
        return $this->response->collection($users, new UserTaskUserTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {get} /itemUsers/{user_id}  根据选择用户id查看项目成员详情
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
    public function show($user_id)
    {
        $itemUser = ItemUser::where('user_id' , $user_id)->first();
        if (!$itemUser) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($itemUser, new ItemUserTransformer())->setMeta($this->apiMeta());


    }

    /**
     * @api {delete} /itemUsers/delete 项目成员删除
     * @apiVersion 1.0.0
     * @apiName itemUsers delete
     * @apiGroup itemUsers
     *
     * @apiParam {integer} item_id
     * @apiParam {integer} selected_user_id
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
    public function destroy(Request $request)
    {
        $item_id = $request->input('item_id');
        $user_id = $request->input('selected_user_id');
        //检验是否存在
        $itemUser = ItemUser::where('item_id' , $item_id)->where('user_id' , $user_id)->first();
        if (!$itemUser) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用
        if ($itemUser->user_id == $this->auth_user_id) {
            return $this->response->array($this->apiError('自己不能删除自己!', 403));
        }
        //判断权限
        $designProject = DesignProject::where('id' , $item_id)->first();
        if(!$designProject){
            return $this->response->array($this->apiError('没有找到该项目!', 404));
        }
        if($designProject->isPower($this->auth_user_id) != true){
            return $this->response->array($this->apiError('没有权限删除!', 403));

        }
        //判断是商务经理还是项目经理
        if($itemUser->level == 3){
            $designProject->removeLeader($user_id);
        }elseif($itemUser->level == 5){
            $designProject->removeBusinessManager($user_id);
        }
        $ok = $itemUser->delete();
        if ($ok) {
            //查询任务成员
            $taskUsers = TaskUser::where('selected_user_id' , $user_id)->get();
            foreach ($taskUsers as $taskUser){
                //查询任务成员所参加的任务
                $task = Task::find($taskUser->task_id);
                if($task){
                    //移除任务执行人
                    $task->removeExecuteUser($user_id);
                }
                //移除任务成员
                $taskUser->delete();
            }
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->array($this->apiError());

    }
}