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
        return mt_rand(100000, 999999);
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
        foreach (json_decode($data, true) as $v) {
            $data_arr = $data_arr + $v;
        }
        if (array_key_exists($code, $data_arr)) {
            $name = $data_arr[$code];
        } else {
            $name = '';
        }

        return $name;
    }

    /**
     * 添加系统通知
     *
     * @param int $user_id 用户ID
     * @param string $title 标题
     * @param string $message 消息内容
     * @param int $type 消息类型：1.系统通知。2.项目通知。3.资金通知
     * @param int $target_id 目标ID
     * @return bool 返回值
     */
    public function message(int $user_id, string $title, string $message, int $type = 1, int $target_id = null)
    {
        $message = Message::create([
            'user_id' => $user_id,
            'title' => $title,
            'content' => $message,
            'type' => $type,
            'target_id' => $target_id
        ]);

        if ($message) {
            //新消息数量加1
            $this->addMessageQuantity($user_id);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 添加新消息数量
     * @param int $user_id
     */
    public function addMessageQuantity(int $user_id)
    {
        //有序列表key
        $key = 'mallache:user:message';
        //member
        $member = 'user:' . $user_id;
        Redis::zincrby($key, 1, $member);
    }

    /**
     * 减少未读消息数量
     * @param int $user_id
     * @param int $type
     */
    public function reduceMessageQuantity(int $user_id)
    {
        //有序列表key
        $key = 'mallache:user:message';
        //member
        $member = 'user:' . $user_id;
        Redis::zincrby($key, -1, $member);
    }

    /**
     * 读取用户新消息数量
     *
     * @param int $user_id 用户Id
     * @param int $type 消息类型：1.系统消息
     * @return int 消息数量
     */
    public function getMessageQuantity(int $user_id)
    {
        //有序列表key
        $key = 'mallache:user:message';
        //member
        $member = 'user:' . $user_id;

        //ZSCORE key member
        $quantity = Redis::zscore($key, $member);
        if ($quantity == 'nil') {
            return 0;
        }

        return (int)$quantity;
    }

    /**
     * 消息已读
     * @param int $id
     */
    public function haveRead(int $id)
    {
        if ($message = Message::find($id)) {
            $message->status = 1;
            $message->save();
            $this->reduceMessageQuantity($message->user_id);

            return true;
        }else{
            return false;
        }
    }

    /**
     * 清空新消息数量
     */
    public function emptyMessageQuantity(int $user_id, int $type = 1)
    {
        //有序列表key
        $key = 'mallache:user:message' . $type;
        //member
        $member = 'user:' . $user_id;
        //ZADD key score member
        Redis::zadd($key, 0, $member);
    }

    /**
     * 生成单号  日期 + 6位用户ID + 2位随机数
     *
     * @param $user_id
     * @return string
     */
    public static function orderId($user_id)
    {
        return date("mdHis") . sprintf("%06d", $user_id) . mt_rand(00, 99);
    }

}