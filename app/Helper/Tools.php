<?php
/**
 * 工具类
 * @Date 2017-3-27
 * @User llh
 */

namespace App\Helper;

use App\Models\Message;
use App\Models\User;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Cache;
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


    protected static $data_arr = null;

    /**
     * 获取城市名称
     * @param $code
     */
    static public function cityName($code)
    {
        $code = (int)$code;
        if (self::$data_arr == null) {
            $data = config('city.data');
            $data = json_decode($data, true);

            $data_arr = [];
            foreach ($data as $v) {
                $data_arr = $data_arr + $v;
            }
            self::$data_arr = $data_arr;
        } else {
            $data_arr = self::$data_arr;
        }

        if (array_key_exists($code, $data_arr)) {
            $name = $data_arr[$code];
        } else {
            $name = '';
        }

        unset($data_arr);
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
        if ($quantity < 0) {
            Redis::zadd($key, 0, $member);
        } else {
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
            if ($message->save()) {
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
    public static function request($url, $data, $options = array())
    {
        if (empty($url)) {
            return false;
        }

        $o = "";
        if (!empty($data)) {
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    $o .= "$k=" . urlencode($v) . "&";
                }
                $data = substr($o, 0, -1);
            }
        }

        $postUrl = $url;
        $curlPost = $data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $result = curl_exec($ch);//运行curl
        curl_close($ch);

        return $result;
    }

    /**
     * 生成验证码图片
     *
     * @param $str string 随机字符串
     */
    public static function captchaCreate($str)
    {
        $str = trim($str);
        if ($phrase = Cache::get($str)) {
            $builder = new CaptchaBuilder($phrase);
        } else {
            $builder = new CaptchaBuilder();
            $phrase = $builder->getPhrase();

            // 设置缓存十分钟过期
            Cache::put($str, $phrase, 10);
        }


        //可以设置图片宽高及字体
        $builder->build(102, 34);

        // 启用失真
        $builder->setDistortion(true);

        header('Content-type: image/jpeg');
        $builder->output();
    }


    /**
     * 验证验证码
     * @param $str string 随机字符串
     * @param $captcha string 验证码
     */
    public static function checkCaptcha(string $str, string $captcha)
    {
        $str = trim($str);
        $captcha = trim($captcha);
        $result = Cache::get($str);

        if ($result === null) {
            return false;
        }
        if (strtolower($result) == strtolower($captcha)) {
            Cache::forget($str);
            return true;
        }

        return false;
    }

    /**
     * 生成微秒级 唯一字符串
     *
     * @return string
     */
    public static function microsecondUniqueStr()
    {
        $str = uniqid('mallache', true);
        return md5($str);
    }

    /**
     * 生成随机字符串
     *
     * @return string
     */
    public static function createStr($num)
    {
        $digit_msp = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'w', 'z', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'W', 'Z'];
        return implode('', array_random($digit_msp, $num));
    }

    /**
     * 短网址、推广码
     *
     */

    public static function url_short($url)
    {
        if (!is_string($url)) return false;
        $result = sprintf("%u", crc32($url));
        $shortUrl = '';
        $digitMsp = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'w', 'z', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'W', 'Z');
        while ($result > 0) {
            $s = $result % 62;
            $result = floor($result / 62);
            if ($s > 9 && $s < 36)
                $s += 10;
            $shortUrl .= $digitMsp[$s];
        }

        return $shortUrl;

    }
}
