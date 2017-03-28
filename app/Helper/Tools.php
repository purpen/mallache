<?php
/**
 * 工具类
 * @Date 2017-3-27
 * @User llh
 */
namespace App\Helper;

class Tools
{
    /**
     * 随机生成6位数字验证码
     *
     * @return int
     */
    static public function randNumber()
    {
        return mt_rand(100000,999999);
    }
}