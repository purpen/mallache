<?php
return [
    //商户开通的商户号
'merchantNum' => 22294531,

// 商户DES密钥
'desKey' => 'ta4E/aspLA3lgFGKmNDNRYU92RkZ4w2t',

// 京东支付服务地址
'serverPayUrl' => 'https://wepay.jd.com/jdpay/saveOrder',

// 京东查询服务地址
'serverQueryUrl' => 'https://paygate.jd.com/service/query',

// 退款服务地址
'refundUrl' => 'https://paygate.jd.com/service/refund',

// callback地址
'callbackUrl' => env('JD_CALL_BACK_URL', 'http://mc.taihuoniao.com/alipay/callback'),

// notify地址
'notifyUrl' => env('JD_NOTIFY_URL', 'http://sa.taihuoniao.com/pay/jdPayNotify'),

// 扫码创建订单
'uniorderUrl' => 'https://paygate.jd.com/service/uniorder',

// 交易号查退款
'queryRefundUrl' => 'https://paygate.jd.com/service/queryRefund',

// 撤销地址
'revokeUrl' => 'https://paygate.jd.com/service/revoke',

// 付款码支付
'fkmPayUrl' => 'https://paygate.jd.com/service/fkmPay'

];