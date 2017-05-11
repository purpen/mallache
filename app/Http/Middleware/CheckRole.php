<?php

namespace App\Http\Middleware;

use Closure;
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
    public function handle($request, Closure $next, $role_id)
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            throw new JWTException('用户不存在');
        }

        if($role_id != $user->role_id){
            throw new HttpException(401, '无访问权限');
        }
        return $next($request);
    }
}
