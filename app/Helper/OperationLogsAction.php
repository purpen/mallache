<?php

namespace App\Helper;

use App\Models\OperationLog;
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
     * 创建项目管理类型的操作动态
     *
     * @param int $item_id 项目ID
     * @param int $action_type 动作类型 1.创建任务
     * @param int $target_id 目标ID（与action_type配合使用）
     * @param int|null $other_user_id 被操作人ID
     * @param string|null $content 变更内容
     * @return OperationLog
     */
    private function createItemLog(int $item_id, int $action_type, int $target_id, int $other_user_id = null, string $content = null)
    {
        $company_id = $this->getUserDesignId();
        return OperationLog::createLog($company_id, 1, $item_id, $action_type, $target_id, $this->auth_user->id, $other_user_id, $content);
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

        if ($tier == 0) {  // 父任务
            $this->createItemLog($item_id, 1, $target_id, null, $content);
        } else if ($tier == 1) {  // 子任务
            $this->createItemLog($item_id, 2, $target_id, null, $content);

        }
    }

    public function updateTask()
    {
        $response_content = $this->getResponseContent();
        $all = $this->request->except(['token']);
        $new_all = array_diff($all, array(null));
        Log::info($new_all);
        $target_id = $response_content['data']['id'];

        $item_id = intval($this->request->input('item_id'));
        $content = $response_content['data']['name'];

        $this->createItemLog($item_id, 3, $target_id, null, $content);

    }

}