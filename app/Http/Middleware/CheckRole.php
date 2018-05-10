<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * 角色验证中间件
 * Class CheckRole
 * @package App\Http\Middleware
 */
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            throw new JWTException('用户不存在');
        }

        //0.用户; 5.编辑； 10.管理员 admin； 15:管理员 admin_plus  20：超级管理员 root
        switch ($user->role_id){
            //普通用户
            case 0:
                throw new HttpException(403, '无访问权限');
                break;
            //编辑 editor
            case 5:
                if(!$this->check($request, 'editor')){
                    throw new HttpException(403, '无访问权限');
                }
                break;
            //管理员 admin
            case 10:
                if(!$this->check($request, 'admin')){
                    throw new HttpException(403, '无访问权限');
                }
                break;
            //管理员 admin_plus
            case 15:
                if(!$this->check($request, 'admin_plus')){
                    throw new HttpException(403, '无访问权限');
                }
                break;
            //超级管理员 root
            case 20:
                if(!$this->check($request, 'root')){
                    throw new HttpException(403, '无访问权限');
                }
                break;
            default:
                throw new HttpException(403, '无访问权限');
        }

        return $next($request);
    }

    /**
     * 验证有无访问路由的权限
     *
     * @param Request $request
     * @param $role
     * @return bool
     */
    protected function check(Request $request, $role)
    {
        $pathInfo = $request->getPathInfo();

        $path_arr = config('role.' . $role);

        return (bool)in_array($pathInfo, $path_arr);
    }
}
