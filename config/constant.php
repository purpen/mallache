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

    //项目推荐失败后，是否人工后台介入处理
    'item_recommend_lose' => true,     // true:人工介入； false：项目匹配失败

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
        20 => '其他',
    ],


    // 项目状态(需求公司)
    'demand_item_status' => [
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
    ],

    //接单设置
    'item_type' => [
        1 => [1 => '产品策略', 2 => '产品设计', 3 =>'结构设计'],
        2 => [1 => 'app设计', 2 => '网页设计', 3 => '界面设计', 4 => '服务设计', 5 => '用户体验咨询'],
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

];