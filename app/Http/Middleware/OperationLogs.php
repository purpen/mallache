<?php

namespace App\Http\Middleware;

use App\Helper\OperationLogsAction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class OperationLogs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response)
    {
        // 请求的路由
        $path = $request->getPathInfo();
        // 请求方法
        $match = $request->getMethod();

        // 判断请求是否成功：成功下一步动态记录处理 ，失败则返回
        $response_arr = json_decode($response->getContent(), true);
        if (empty($response_arr) || !isset($response_arr['meta']) || $response_arr['meta']['status_code'] != 200) {
            return;
        }

        // 初始化 操作记录方法类
        $operation_logs_action = new OperationLogsAction($request, $response);

        foreach ($this->selfConfig() as $route => $func) {
            if ($this->isPath($route, $match, $path)) {
                $operation_logs_action->$func();
            }
        }
    }


    // 路由对应处理方法
    protected function selfConfig()
    {
        // ['路由:请求方法' => 对应执行的方法]
        return [
            '/recycleBin/*:get' => 'task', // 测试
            '/tasks:post' => 'createTask',  // 创建任务
            '/isStage/tasks:put' => 'isStage',  // 任务完成与未完成
            '/tasks:put' => 'updateTask',  // 更改任务名称，备注，优先级
            '/tags:post' => 'createTag',  // 创建标签
            '/tags:delete' => 'deleteTag',  // 删除标签
            '/itemUsers:post' => 'createItemUser',  //创建项目成员
            '/itemUsers:delete' => 'deleteItemUser',  //删除项目成员
            '/communeSummaries:post' => 'createCommuneSummary',  //创建沟通纪要
            '/communeSummaries:put' => 'updateCommuneSummary',  //更改沟通纪要
            '/communeSummaries:delete' => 'deleteCommuneSummary',  //删除沟通纪要
            '/design/restoreMember:put' => 'userOutItem',  //某某退出了该项目
            '/tasks/executeUser:post' => 'executeUser',  //某某领取了任务，某某指派给了谁，某某移除了谁
        ];
    }

    /**
     * 判断值是否是指定的路由
     *
     * @param $pattern string 指定的路由信息
     * @param $match string 请求方法
     * @param $value string 请求路由
     *
     * @return bool
     */
    public function isPath($pattern, $match, $value)
    {
        $match = strtolower($match);

        $pattern_arr = explode(':', $pattern);
        if (count($pattern_arr) != 2) {
            throw new \Exception('pattern value error');
        }

        if ($pattern_arr[1] != $match) {
            return false;
        }

        if ($pattern_arr[0] == $value && $pattern_arr[1] == $match) {
            return true;
        }

        $pattern = $pattern_arr[0];
        $pattern = preg_quote($pattern, '#');

        // 支持通配符
        $pattern = str_replace('\*', '.*', $pattern);
        return (bool)preg_match('#^' . $pattern . '#u', $value);
    }
}
