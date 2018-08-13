<?php

/**
 * 常量配置文件
 */

return [

    //创建项目项目保证金
    'item_price' => env('ITEM_PRICE', 99),

    // 项目首付款支付比例
    'first_payment' => 0.40,
    //项目验收之后支付比例
    'warranty_money' => 0.10,

    // 平台佣金比例
    'commission_rate' => 0.10,

    //合同版本配置 合同版本：0.默认 1.1806版
    'contract_version' => 1,

    //项目推荐失败后，是否人工后台介入处理
    'item_recommend_lose' => env('ITEM_RECOMMEND_LOSE', true),     // true:人工介入； false：项目匹配失败


    //是否开启项目状态短信通知 true | false
    'sms_send' => true,

    // 有新项目发布需要通知的手机号
    'new_item_send_phone' => [
        '耿霆' => '13031154842',
    ],

    // 短信通知标签
    'sms_fix' => '【太火鸟铟果】',

    // 京东艺云短信统计标签
    'jd_sms_fix' => '【京东云艺火】',

    // 义乌短信统计标签
    'yw_sms_fix' => '【义乌设计大脑】',

    // 系统短信通知中的联系手机
    'notice_phone' => '13031154842',

    // 子账户的数量
    'child_count' => 30,

    // 项目下任务总数量限制
    'task_count' => 1000,

    //产品领域配置
    'field' => [
        1 => '智能硬件',
        2 => '消费电子',
        3 => '交通工具',
        4 => '工业设备',
        5 => '厨电厨具',
        6 => '医疗设备',
        7 => '家具用品',
        8 => '办公用品',
        9 => '大家电',
        10 => '小家电',
        11 => '卫浴',
        12 => '玩具',
        13 => '体育用品',
        14 => '军工设备',
        15 => '户外用品',
    ],

    //行业配置
    'industry' => [
        1 => '制造业',
        2 => '消费零售',
        3 => '信息技术',
        4 => '能源',
        5 => '金融地产',
        6 => '服务业',
        7 => '医疗保健',
        8 => '原材料',
        9 => '工业制品',
        10 => '军工',
        11 => '公用事业',
    ],

    //奖项配置
    'prize' => [
        1 => '德国红点设计奖',
        2 => '德国IF设计奖',
        3 => 'IDEA工业设计奖',
        4 => '中国红星奖',
        5 => '中国红棉奖',
        6 => '台湾金点奖',
        7 => '香港DFA设计奖',
        8 => '日本G-Mark设计奖',
        9 => '韩国好设计奖',
        10 => '新加坡设计奖',
        11 => '意大利—Compasso d`Oro设计奖',
        12 => '英国设计奖',
        13 => '中国优秀工业设计奖',
        14 => 'DIA中国设计智造大奖',
        15 => '中国好设计奖',
        16 => '澳大利亚国际设计奖',
        20 => '其他',
    ],


    // 项目状态(需求公司)
    'demand_item_status' => [
        -3 => '项目已强制关闭',
        -2 => '系统没有匹配到符合您要求的设计服务供应商，调整需求信息试试',
        -1 => '撤回当前项目',
        1 => '待完善',
        2 => '发布成功，等待系统匹配',
        3 => '系统为您找到符合条件的设计服务供应商，请从中选择心仪对象发送项目需求',
        4 => '项目需求已发送，等待设计服务供应商报价',
        5 => '接受报价，合同详情商榷中',
        6 => '收到合同，确认或继续沟通',
        7 => '确认合同，支付项目款后设计服务供应商可开始项目',
        8 => '支付并托管项目款',
        9 => '项目款已托管，等待设计服务供应商启动项目',
        11 => '目前项目正在进行中，请时刻与设计服务供应商保持沟通，确保项目按时、顺利进行',
        15 => '项目全部阶段已完成，请及时验收',
        18 => '项目验收成功',
        22 => '已评价'
    ],

    //项目状态(设计公司)
    'design_item_status' => [
        -3 => '项目已强制关闭',
        4 => '新收到项目需求，如有兴趣可报价,若已报价请等待需求公司确认',
        5 => '报价已被项目需求方确认，可以订立合同',
        6 => '合同已发送，等待项目需求方确认',
        7 => '合同已订立，等待项目需求方支付并托管项目款',
        8 => '等待项目需求方托管项目资金',
        9 => '项目需求方已支付项目款，请按照合同约定启动项目',
        11 => '目前项目正在进行中，请时刻与项目需求方保持沟通，确保项目按时、顺利进行',
        15 => '等待项目需求方验收',
        18 => '项目全部阶段已完成，请等待设计需求方验收。验收成功后尾款自动转入“我的钱包”',
        22 => '已评价'
    ],

    //项目周期
    'item_cycle' => [
        1 => '1个月内',
        2 => '1-2个月',
        3 => '2-3个月',
        4 => '3-4个月',
        5 => '4个月以上',
    ],

    //证件类型
    'document_type' => [
        1 => '身份证',
        2 => '港澳通行证',
        3 => '台胞证',
        4 => '护照',
    ],

    //银行
    'bank' => [
        1 => '中国建设银行',
        2 => '中国银行',
        3 => '中国农业银行',
        4 => '中国工商银行',
        5 => '中国建设银行',
        6 => '民生银行',
        7 => '招商银行',
        8 => '兴业银行',
        9 => '国家开发银行',
        10 => '汇丰银行',
        11 => '中国人民银行',
        12 => '中国光大银行',
        13 => '中信银行',
        14 => '交通银行',
        15 => '华夏银行',
        16 => '深圳发展银行',
        17 => '浦发银行',
        18 => '中国邮政储蓄银行',
        -1 => '京东云市场',
    ],

    // 项目大类型
    'type' => [
        1 => '产品设计',
        2 => 'UI/UX',
        3 => '平面设计',
        4 => 'H5',
        5 => '包装设计',
        6 => '插画',
    ],

    //接单设置
    'item_type' => [
        1 => [1 => '产品策略', 2 => '产品设计', 3 => '结构设计'],
        2 => [1 => 'app设计', 2 => '网页设计', 3 => '界面设计', 4 => '服务设计', 5 => '用户体验咨询'],
        3 => [1 => 'logo/VI设计', 2 => '海报/宣传册', 3 => '画册/书装'],
        4 => [1 => 'H5'],
        5 => [1 => '包装设计'],
        6 => [1 => '商业插画', 2 => '书籍插画', 3 => '形象/IP插画'],
    ],

    //企业性质类型
    'company_property' => [
        1 => '初创企业',
        2 => '私企',
        3 => '国有企业',
        4 => '事业单位',
        5 => '外资',
        6 => '合资',
        7 => '上市公司',
    ],

    // 栏目位分类
    'column_type' => [
        1 => '灵感',
    ],

    // 分类
    'classification_type' => [
        1 => '文章',
    ],

    //日期奖项分类
    'dateOfAward_type' => [
        1 => '设计大赛',
        2 => '节日',
        3 => '展会',
        4 => '事件'
    ],

    //常用网站分离
    'commonlyUsedUrl_type' => [
        1 => '设计资讯',
        2 => '创意灵感',
        3 => '众筹',
        4 => '商业咨询',
        5 => '设计奖项',
    ],

    // 奖项案例奖项名称
    'awardCase_category' => [
        1 => 'IDEA设计奖',
        2 => 'G-Mark设计奖',
        3 => 'IF设计奖',
        4 => '红点设计奖',
        99 => '其它',
    ],

    // 系统通知目标人群
    'notice_evt' => [
        0 => '全部',
        1 => '需求方',
        2 => '设计公司',
    ],

    // 设计公司营收
    'revenue' => [
        1 => '100万以下',
        2 => '100-500万',
        3 => '500-1000万',
        4 => '1000-2000万',
        5 => '3000-5000万',
        6 => '5000万以上',
    ],

    //标签颜色
    'tag_type' => [
        1 => '#999999',
        2 => '#FF5A5F',
        3 => '#FC9259',
        4 => '#FFD330',
        5 => '#A4CF30',
        6 => '#E362E3',
        7 => '#AA62E3',
        8 => '#3DA8F5',
        9 => '#129C4F',
        10 => '#37C5AB',
    ],

    //平面设计项目现状
    'graphic_present_situation' => [
        1 => '设计理念与需求明确',
        2 => '设计理念与需求部分明确/待定',
        3 => '无设计理念',
    ],

    //平面设计项目现有内容
    'graphic_existing_content' => [
        1 => '所需设计内容齐全',
        2 => '有核心视觉元素与标识使用手册',
        3 => '只有核心视觉元素',
        4 => '只有标识使用手册',
        5 => '没有任何设计元素',
    ],

    //包装设计项目现状
    'pack_present_situation' => [
        1 => '设计理念与需求明确',
        2 => '设计理念与需求部分明确/待定',
        3 => '无设计理念',
    ],

    //包装设计项目现有内容
    'pack_existing_content' => [
        1 => '所需设计内容齐全',
        2 => '有核心视觉元素与标识使用手册',
        3 => '只有核心视觉元素',
        4 => '只有标识使用手册',
        5 => '没有任何设计元素',
    ],

    //h5项目现状
    'h5_present_situation' => [
        1 => '设计概念清晰',
        2 => '设计概念模糊',
        3 => '无设计概念',
    ],

    //插画项目现状 1.设计概念清晰 2.设计概念模糊 3. 无设计概念
    'illustration_present_situation' => [
        1 => '设计概念清晰',
        2 => '设计概念模糊',
        3 => '无设计概念',
    ],

    // 物流公司
    'logistics' => [
        1 => '圆通快递',
        2 => '申通快递',
        3 => '顺丰快递',
        4 => '天天快递',
        5 => '中通速递',
        6 => '邮政EMS',
        7 => '韵达快递',
        8 => '优速快递',
        9 => '全峰快递',
        10 => '宅急送',
        11 => '百世快递',
        12 => '国通快递',
        13 => '德邦物流',
        14 => '快捷快递',
        15 => '全一快递',
    ],

    //拒单类型
    'refuse_type' => [
        1 => '价格低',
        2 => '不擅长',
        10 => '其他',
    ],

    //通知时间5分钟
    'inform_time' => 172800,

];
