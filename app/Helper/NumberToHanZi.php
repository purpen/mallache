<?php

namespace App\Helper;

class NumberToHanZi
{
    // 获取数字的中文大写
    public static function numberToH($number)
    {
        $number_v = [
            0 => '零',
            1 => '壹',
            2 => '贰',
            3 => '叁',
            4 => '肆',
            5 => '伍',
            6 => '陆',
            7 => '柒',
            8 => '捌',
            9 => '玖',
        ];

        $jinzhi = [
            0 => '分',
            1 => '角',
            2 => '圆',
            3 => '拾',
            4 => '佰',
            5 => '仟',
            6 => '万',
            7 => '拾',
            8 => '佰',
            9 => '仟',
            10 => '亿',
            11 => '拾',
            12 => '佰',
            13 => '仟',
        ];

        $number = $number * 100;
        $number_arr = str_split($number);
        $count = count($number_arr) - 1;

        $str = '';

        $n = 0;
        $old = null;
        for (; $count >= 0; $count--) {
            $int = $number_arr[$count];

            $a1 = $number_v[$int];

            $a2 = $jinzhi[$n];

            if ($a1 === '零' && $a2 !== '万' && $a2 !== '亿' && $a2 !== '圆' && $a2 !== '角' && $a2 !== '分') {
                if ($old !== '零') {
                    $str = $a1 . $str;
                }
            } elseif ($a1 === '零' && ($a2 === '万' || $a2 === '亿' || $a2 === '圆')) {
                $str = $a2 . $str;
            } else {
                $str = $a1 . $a2 . $str;
            }
            $old = $a1;
            $n++;
        }

        return $str;
    }

}