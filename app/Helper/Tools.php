<?php
/**
 * 工具类
 * @Date 2017-3-27
 * @User llh
 */

namespace App\Helper;

use App\Models\Message;
use App\Models\User;
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
            $user = User::find($message->user_id);
            if ($user) {
                $user->increment('message_count');
                return true;
            }
        }
        return false;
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
     * 减少未读消息数量 ---已不用
     * @param int $user_id
     * @param int $type
     */
    public function reduceMessageQuantity(int $user_id)
    {
        //有序列表key
        $key = 'mallache:user:message';
        //member
        $member = 'user:' . $user_id;
        $quantity = Redis::zscore($key, $member);
        if($quantity < 0){
            Redis::zadd($key, 0, $member);
        }else{
            Redis::zincrby($key, -1, $member);
        }
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
//        //有序列表key
//        $key = 'mallache:user:message';
//        //member
//        $member = 'user:' . $user_id;
//
//        //ZSCORE key member
//        $quantity = Redis::zscore($key, $member);
//        if ($quantity == 'nil') {
//            return 0;
//        }

        $data = array(
            'message' => 0,
            'notice' => 0,
            'quantity' => 0,
        );

        if (!$user_id) {
            return $data;
        }

        $user = User::find($user_id);
        if (!$user) {
            return $data;
        }

        if (isset($user->message_count)) $data['message'] = (int)$user->message_count;
        if (isset($user->notice_count)) $data['notice'] = (int)$user->notice_count;
        $data['quantity'] = $data['message'] + $data['notice'];

        // $data = Message::where(['status' => 0, 'user_id' => $user_id])->count();
        return $data;
    }

    /**
     * 消息已读
     * @param int $id
     */
    public function haveRead(int $id)
    {
        if ($message = Message::find($id)) {
            $message->status = 1;
            if($message->save()){
                $user = User::find($message->user_id);
                if ($user && $user->message_count > 0) {
                    $user->decrement('message_count');
                    return true;
                }
            }
        }
        return false;
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

    /**
     * curl http请求
     *
     * @param $url  String
     * @param $params Array
     * @param $options Array
     * @return string
     */
    public static function request($url, $data, $options=array())
    {
        if (empty($url)) {
            return false;
        }
        
        $o = "";
        if(!empty($data)){
            if(is_array($data)){
                foreach ( $data as $k => $v ) 
                { 
                    $o.= "$k=" . urlencode( $v ). "&" ;
                }
                $data = substr($o,0,-1);           
            }
        }

        $postUrl = $url;
        $curlPost = $data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $result = curl_exec($ch);//运行curl
        curl_close($ch);
        
        return $result;
    }
    

}
