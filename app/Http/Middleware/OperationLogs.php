<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

        foreach ($this->selfConfig() as $route => $func) {
            if ($this->isPath($route, $match, $path)) {
                $this->$func();
            }
        }
    }

    public function task(Request $request, Response $response)
    {
        Log::info('我是一条记录');
    }

    protected function selfConfig()
    {
        // ['路由:请求方法' => 对应执行的方法]
        return [
            'path:match' => 'task',
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
        $pattern = str_replace('/*', '.*', $pattern);

        return (bool)preg_match('#^' . $pattern . '\n#u', $value);
    }
}
