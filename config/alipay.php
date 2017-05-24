<?php
/**
 * 支付宝配置
 */
return [
    // 合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
    'partner' => env('ALIPAY_PARTNER', '2088511301099072'),

    // 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
    'notify_url' => env('ALIPAY_NOTIFY_URL', 'http://sa.taihuoniao.com/pay/aliPayNotify'),

    // 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
    'return_url' => env('ALIPAY_RETURN_URL', 'http://mc.taihuoniao.com/alipay/callback'),

    // 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    'transport' => env('ALIPAY_TRANSPORT', 'http'),
];