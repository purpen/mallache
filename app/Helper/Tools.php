<?php
/**
 * 工具类
 * @Date 2017-3-27
 * @User llh
 */
namespace App\Helper;

use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
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
    public function message(int $user_id, string $message, int $type = 1)
    {
        $message = Message::create([
            'user_id' => $user_id,
            'content' => $message,
            'type' => $type,
        ]);

        if($message){
            //新消息数量加1
            $this->addMessageQuantity($user_id, $type);
            return true;
        }else{
            return false;
        }
    }

    /**
     * 添加新消息数量
     * @param int $user_id
     * @param int $type
     */
    public function addMessageQuantity(int $user_id, int $type = 1)
    {
        //有序列表key
        $key = 'mallache:user:message:' . $type;
        //member
        $member = 'user:' . $user_id;
        Redis::zincrby($key, 1, $member);
    }

    /**
     * 读取用户新消息数量
     *
     * @param int $user_id 用户Id
     * @param int $type 消息类型：1.系统消息
     * @return int 消息数量
     */
    public function getMessageQuantity(int $user_id, int $type = 1)
    {
        //有序列表key
        $key = 'mallache:user:message:' . $type;
        //member
        $member = 'user:' . $user_id;

        //ZSCORE key member
        $quantity = Redis::zscore($key, $member);
        if($quantity == 'nil'){
            return 0;
        }

        return (int)$quantity;
    }

    /**
     * 清空新消息数量
     */
    public function emptyMessageQuantity(int $user_id, int $type = 1)
    {
        //有序列表key
        $key = 'mallache:user:message:' . $type;
        //member
        $member = 'user:' . $user_id;
        //ZADD key score member
        Redis::zadd($key, 0, $member);
    }

    /**
     * 生成支付单号  日期 + 8位用户ID + 6位随机数
     *
     * @param $user_id
     * @return string
     */
    public static function orderId($user_id)
    {
        return  date("YmdHis") . sprintf("%08d", $user_id) . Tools::randNumber();
    }

}