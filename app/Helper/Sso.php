<?php
/**
 * 单点登录方法集
 * @Date 2018-09-18
 * @User Tianshuai
 */

namespace App\Helper;

use App\Helper\Tools;
use Mockery\Exception;

class Sso
{
    /**
     * 请求sso系统
     * @param int evt 1.登录；2.注册；3.快捷登录；4.更新；5.修改密码；6.查看；7.--；
     * @param array params 参数集合
     * @return array 返回成功或失败
     *
     */
    public static function request($evt, $params = array())
    {
        $result = array(
            'success' => false,
            'message' => '',
        );
        $path = '';
        switch ($evt) {
            case 1:
                $path = 'auth/signin';
                break;
            case 2:
                $path = 'auth/signup';
                break;
            case 3:
                $path = 'auth/quick_sign';
                break;
            case 4:
                $path = 'auth/update';
                break;
            case 5:
                $path = 'auth/update_pwd';
                break;
            case 6:
                $path = 'auth/view';
                break;
        }
        if (!$path) {
            $result['message'] = '请选择操作行为!';
            return $result;
        }
        try {
            $new_sso_params = self::apiParamEncrypt($params);
            $sso_url = config('sso.url') . $path;

            $sso_result = Tools::request($sso_url, $new_sso_params, 'POST');
            $sso_result = Tools::objectToArray(json_decode($sso_result));

            if (!isset($sso_result['code'])) {
                $result['message'] = '请求用户系统登录失败';
                return $result;
            }

            if ($sso_result['code'] != 200) {
                $result['message'] = $sso_result['message'];
                return $result;
            }
            $result['success'] = true;
            return $result;
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * 请求sso 系统加密接口
     * @param array 应用参数
     * @return 格式化后的参数
     */
    public static function apiParamEncrypt($app_param)
    {
        $sys_param = array(
            'app_id' => config('sso.app_id'),
            'from_to' => 2,
            'timestamp' => time(),
        );
        $params = array_merge($sys_param, $app_param);
        ksort($params);
        $paramstring = '';
        foreach ($params as $key => $value) {
            if (strlen($paramstring) == 0) {
                $paramstring .= $key . "=" . $value;
            } else {
                $paramstring .= "&" . $key . "=" . $value;
            }
        }
        $secret_key = config('sso.secret_key');
        $sign = md5($paramstring . ':' . $secret_key);
        $params['sign'] = $sign;
        return $params;
    }

}
