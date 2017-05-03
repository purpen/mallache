<?php
/**
 * 工具类
 * @Date 2017-3-27
 * @User llh
 */
namespace App\Helper;

use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

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

    /**
     * 添加系统通知
     *
     * @param int $user_id 用户ID
     * @param string $message 消息内容
     * @param int $type 消息类型：1.系统通知；
     * @return bool 返回值
     */
    static public function message(int $user_id, string $message, int $type = 1)
    {
            $message = Message::create([
                'user_id' => $user_id,
                'message' => $message,
                'type' => $type,
            ]);

        if($message){
            return true;
        }else{
            return false;
        }
    }

}