<?php

namespace App\Helper;

use App\Models\OperationLog;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class OperationLogsAction
{

    // 请求对象
    protected $request = null;

    // 返回对象
    protected $response = null;

    // 用户model
    protected $auth_user = null;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->getAuthUser();
    }

    private function getAuthUser()
    {
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $this->auth_user = $user;
                return;
            }
        } catch (\Exception $e) {
            // skip
        }
    }

    /**
     * 获取用户设计公司Id
     *
     * @return mixed
     */
    private function getUserDesignId()
    {
        return User::designCompanyId($this->auth_user->id);
    }


    /**
     * 创建项目管理--任务的操作动态
     *
     * @param int $item_id 项目ID
     * @param int $action_type 动作类型 1.创建任务
     * @param int $target_id 目标ID（与action_type配合使用）
     * @param int|null $other_user_id 被操作人ID
     * @param string|null $content 变更内容
     * @return OperationLog
     */
    private function createTaskLog(int $item_id, int $action_type, int $target_id, int $other_user_id = null, string $content = null)
    {
        $company_id = $this->getUserDesignId();
        return OperationLog::createLog($company_id, 1, $item_id, $action_type, 1, $target_id, $this->auth_user->id, $other_user_id, $content);
    }

    // 项目管理--标签操作动态
    private function createTagLog(int $item_id, int $action_type, int $target_id, int $other_user_id = null, string $content = null)
    {
        $company_id = $this->getUserDesignId();
        return OperationLog::createLog($company_id, 1, $item_id, $action_type, 2, $target_id, $this->auth_user->id, $other_user_id, $content);
    }

    // 项目管理--项目人员操作动态
    private function createUserLog(int $item_id, int $action_type, int $target_id, int $other_user_id = null, string $content = null)
    {
        $company_id = $this->getUserDesignId();
        return OperationLog::createLog($company_id, 1, $item_id, $action_type, 3, $target_id, $this->auth_user->id, $other_user_id, $content);
    }

    // 项目管理--项目沟通纪要操作动态
    private function createSummariesLog(int $item_id, int $action_type, int $target_id, int $other_user_id = null, string $content = null)
    {
        $company_id = $this->getUserDesignId();
        return OperationLog::createLog($company_id, 1, $item_id, $action_type, 4, $target_id, $this->auth_user->id, $other_user_id, $content);
    }

    // 项目管理--某某退出了项目
    private function createOutItemLog($company_id ,int $item_id, int $action_type, int $target_id, int $user_id,int $other_user_id = null, string $content)
    {
        return OperationLog::createLog($company_id, 1, $item_id, $action_type, 5, $target_id, $user_id, $other_user_id, $content);
    }

    /**
     * 获取返回数据
     *
     * @return array
     */
    private function getResponseContent()
    {
        return json_decode($this->response->getContent(), true);
    }

    // 路由请求对应的log记录方法
    public function task()
    {
//        Log::info($this->response);
//        Log::info($this->auth_user->phone . '我是一条记录');
    }

    //创建任务，子任务
    public function createTask()
    {
        $response_content = $this->getResponseContent();
        $item_id = intval($this->request->input('item_id'));
        $target_id = $response_content['data']['id'];
        $content = $response_content['data']['name'];
        $tier = intval($this->request->input('tier'));
        //父id
        $pid = intval($this->request->input('pid'));

        if ($tier == 0) {  // 父任务
            $this->createTaskLog($item_id, 1, $target_id, null, $content);
        } else if ($tier == 1) {  // 子任务
            $this->createTaskLog($item_id, 2, $pid, null, $content);

        }
    }

    // 更改任务
    public function updateTask()
    {
        $response_content = $this->getResponseContent();
        $all = $this->request->except(['token']);
        array_diff($all, array(null));
        $target_id = $response_content['data']['id'];
        $item_id = $response_content['data']['item_id'];

        $name = $this->request->input('name');
        $summary = $this->request->input('summary');
        $level = $this->request->input('level');
        $over_time = $this->request->input('over_time');
        if (!empty($name)) {
            $this->createTaskLog($item_id, 3, $target_id, null, $name);

        } elseif (!empty($summary)) {
            $this->createTaskLog($item_id, 4, $target_id, null, $summary);

        } elseif (!empty($level)) {
            //优先级存普通，紧急，非常紧急
            if ($level == 1) {
                $level = '普通';
            } else if ($level == 5) {
                $level = '紧级';
            } else if ($level == 8) {
                $level = '非常紧级';
            }
            $this->createTaskLog($item_id, 5, $target_id, null, $level);

        } elseif (!empty($over_time)) {
            $this->createTaskLog($item_id, 10, $target_id, null, $over_time);
        }

    }

    //任务完成重做
    public function isStage()
    {
        $all = $this->request->except(['token']);
        array_diff($all, array(null));
        $target_id = intval($this->request->input('task_id'));
        $tier = intval($this->request->input('tier'));
        $stage = intval($this->request->input('stage'));
        $task = Task::find($target_id);
        $item_id = $task->item_id;
        if ($tier == 0) {  // 父任务
            if ($stage == 0) { //父任务重做
                $this->createTaskLog($item_id, 6, $target_id, null, $task->name);

            } else {  //父任务完成
                $this->createTaskLog($item_id, 7, $target_id, null, $task->name);

            }

        } else if ($tier == 1) {  // 子任务
            if ($stage == 0) { //子任务重做
                $this->createTaskLog($item_id, 8, $task->pid, null, $task->name);

            } else {  //子任务完成
                $this->createTaskLog($item_id, 9, $task->pid, null, $task->name);

            }
        }

    }

    //创建标签
    public function createTag()
    {
        $response_content = $this->getResponseContent();

        $item_id = intval($this->request->input('item_id'));
        $target_id = $response_content['data']['id'];
        $content = $response_content['data']['title'];

        $this->createTagLog($item_id, 11, $target_id, null, $content);

    }

    //删除标签
    public function deleteTag()
    {
        $content = $_POST['tagTitle'];
        $item_id = $_POST['item_id'];
        $target_id = $_POST['id'];

        $this->createTagLog($item_id, 12, $target_id, null, $content);

    }

    //创建项目成员
    public function createItemUser()
    {
        $response_content = $this->getResponseContent();

        $item_id = intval($this->request->input('item_id'));
        $user_id = intval($this->request->input('user_id'));
        $target_id = $response_content['data']['id'];
        $user = User::find($user_id);
        $content = $user->getUserName();

        $this->createUserLog($item_id, 13, $target_id, null, $content);

    }

    //删除项目成员
    public function deleteItemUser()
    {
        $user_id = $_POST['user_id'];
        $target_id = $_POST['id'];
        $item_id = $_POST['item_id'];
        $user = User::find($user_id);
        $content = $user->getUserName();

        $this->createUserLog($item_id, 14, $target_id, null, $content);

    }

    //创建沟通纪要
    public function createCommuneSummary()
    {
        $response_content = $this->getResponseContent();
        $target_id = $response_content['data']['id'];
        $item_id = $response_content['data']['item_id'];
        $content = $response_content['data']['title'];

        $this->createSummariesLog($item_id, 15, $target_id, null, $content);

    }

    //更改沟通纪要
    public function updateCommuneSummary()
    {
        $response_content = $this->getResponseContent();
        $target_id = $response_content['data']['id'];
        $item_id = $response_content['data']['item_id'];
        $content = $response_content['data']['title'];

        $this->createSummariesLog($item_id, 16, $target_id, null, $content);

    }

    //删除沟通纪要
    public function deleteCommuneSummary()
    {
        $content = $_POST['title'];
        $target_id = $_POST['id'];
        $item_id = $_POST['item_id'];
        $this->createSummariesLog($item_id, 17, $target_id, null, $content);

    }

    //某某退出了该项目
    public function userOutItem()
    {
        $company_id = $_POST['company_id'];
        $user_id = $_POST['user_id'];
        $target_id = $_POST['id'];
        $item_id = $_POST['item_id'];
        $user = User::find($user_id);
        $content = $user->getUserName();
        $this->createOutItemLog($company_id , $item_id, 18, $target_id, $user_id , null , $content);

    }


    //认领了 , 指派给了 , 移除了执行者
    public function executeUser()
    {
        $item_id = intval($this->request->input('item_id'));
        $task_id = intval($this->request->input('task_id'));
        $execute_user_id = intval($this->request->input('execute_user_id'));
        $task = Task::find($task_id);
        //移除了执行者
        if ($execute_user_id == 0){
            $this->createTaskLog($item_id, 21, $task_id, null , $task->name);
        } else {
            $user_id = $this->auth_user->id;
            //相等的话领取了任务 ,不等的话指派给了谁
            if ($user_id == $execute_user_id){
                $this->createTaskLog($item_id, 19, $task_id, null , $task->name);
            } else {
                $user = User::find($execute_user_id);
                $content = $user->getUserName();
                $this->createTaskLog($item_id, 20, $task_id, $execute_user_id , $content.$task->name);
            }
        }
    }

}