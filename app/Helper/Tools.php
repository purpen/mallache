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

    /**
     * 获取城市名称
     * @param $code
     */
    static public function cityName($code)
    {
        $code = (int)$code;
        $data = config('city.data');
        $data_arr = [];
        foreach(json_decode($data, true) as $v){
            $data_arr = $data_arr + $v;
        }
        if(array_key_exists($code, $data_arr)){
            $name = $data_arr[$code];
        }else{
            $name = '';
        }

        return $name;
    }

}