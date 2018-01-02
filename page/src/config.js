// 设计类型
const COMPANY_TYPE = [
  {
    id: 1,
    name: '产品设计',
    designType: [
      {
        id: 1,
        key: 'item_1_1',
        name: '产品策略'
      },
      {
        id: 2,
        key: 'item_1_2',
        name: '产品外观设计'
      },
      {
        id: 3,
        key: 'item_1_3',
        name: '结构设计'
      }
    ],
    field: [
      {
        id: 1,
        key: 'item_1_1',
        name: '智能硬件'
      },
      {
        id: 2,
        key: 'item_1_2',
        name: '消费电子'
      },
      {
        id: 3,
        key: 'item_1_3',
        name: '交通工具'
      },
      {
        id: 4,
        key: 'item_1_4',
        name: '工业设备'
      },
      {
        id: 5,
        key: 'item_1_5',
        name: '厨电厨具'
      },
      {
        id: 6,
        key: 'item_1_6',
        name: '医疗设备'
      },
      {
        id: 7,
        key: 'item_1_7',
        name: '家具用品'
      },
      {
        id: 8,
        key: 'item_1_8',
        name: '办公用品'
      },
      {
        id: 9,
        key: 'item_1_9',
        name: '大家电'
      },
      {
        id: 10,
        key: 'item_1_10',
        name: '小家电'
      },
      {
        id: 11,
        key: 'item_1_11',
        name: '卫浴'
      },
      {
        id: 12,
        key: 'item_1_12',
        name: '玩具'
      },
      {
        id: 13,
        key: 'item_1_13',
        name: '体育用品'
      },
      {
        id: 14,
        key: 'item_1_14',
        name: '军工设备'
      },
      {
        id: 15,
        key: 'item_1_15',
        name: '户外用品'
      }
    ],
    industry: [
      {
        id: 1,
        key: 'item_1_1',
        name: '制造业'
      },
      {
        id: 2,
        key: 'item_1_2',
        name: '消费零售'
      },
      {
        id: 3,
        key: 'item_1_3',
        name: '信息技术'
      },
      {
        id: 4,
        key: 'item_1_4',
        name: '能源'
      },
      {
        id: 5,
        key: 'item_1_5',
        name: '金融地产'
      },
      {
        id: 6,
        key: 'item_1_6',
        name: '服务业'
      },
      {
        id: 7,
        key: 'item_1_7',
        name: '医疗保健'
      },
      {
        id: 8,
        key: 'item_1_8',
        name: '原材料'
      },
      {
        id: 9,
        key: 'item_1_9',
        name: '工业制造'
      },
      {
        id: 10,
        key: 'item_1_10',
        name: '军工'
      },
      {
        id: 11,
        key: 'item_1_11',
        name: '公用事业'
      }
    ]
  },
  {
    id: 2,
    name: 'UI/UX设计',
    designType: [
      {
        id: 1,
        key: 'item_2_1',
        name: 'App设计'
      },
      {
        id: 2,
        key: 'item_2_2',
        name: '网页设计'
      },
      {
        id: 3,
        key: 'item_2_3',
        name: '界面设计'
      },
      {
        id: 4,
        key: 'item_2_4',
        name: '服务设计'
      },
      {
        id: 5,
        key: 'item_2_5',
        name: '用户体验咨询'
      }
    ],
    field: [
    ],
    industry: [
    ]
  }
]

// 作品案例奖项选项
const DESIGN_CASE_PRICE_OPTIONS = [
  {
    id: 1,
    name: '德国红点设计奖'
  },
  {
    id: 2,
    name: '德国IF设计奖'
  },
  {
    id: 3,
    name: 'IDEA工业设计奖'
  },
  {
    id: 4,
    name: '中国红星奖'
  },
  {
    id: 5,
    name: '中国红棉奖'
  },
  {
    id: 6,
    name: '台湾金点奖'
  },
  {
    id: 7,
    name: '香港DFA设计奖 '
  },
  {
    id: 8,
    name: '日本G-Mark设计奖'
  },
  {
    id: 9,
    name: '韩国好设计奖'
  },
  {
    id: 10,
    name: '香港DFA设计奖 '
  },
  {
    id: 11,
    name: '新加坡设计奖'
  },
  {
    id: 12,
    name: '意大利—Compasso d`Oro设计奖'
  },
  {
    id: 13,
    name: '英国设计奖'
  }

]

// 擅长领域
const FIELD = [
  {
    id: 1,
    key: 'item_1_1',
    name: '智能硬件'
  },
  {
    id: 2,
    key: 'item_1_2',
    name: '消费电子'
  },
  {
    id: 3,
    key: 'item_1_3',
    name: '交通工具'
  },
  {
    id: 4,
    key: 'item_1_4',
    name: '工业设备'
  },
  {
    id: 5,
    key: 'item_1_5',
    name: '厨电厨具'
  },
  {
    id: 6,
    key: 'item_1_6',
    name: '医疗设备'
  },
  {
    id: 7,
    key: 'item_1_7',
    name: '家具用品'
  },
  {
    id: 8,
    key: 'item_1_8',
    name: '办公用品'
  },
  {
    id: 9,
    key: 'item_1_9',
    name: '大家电'
  },
  {
    id: 10,
    key: 'item_1_10',
    name: '小家电'
  },
  {
    id: 11,
    key: 'item_1_11',
    name: '卫浴'
  },
  {
    id: 12,
    key: 'item_1_12',
    name: '玩具'
  },
  {
    id: 13,
    key: 'item_1_13',
    name: '体育用品'
  },
  {
    id: 14,
    key: 'item_1_14',
    name: '军工设备'
  },
  {
    id: 15,
    key: 'item_1_15',
    name: '户外用品'
  }
]

// 行业
const INDUSTRY = [
  {
    id: 1,
    key: 'item_1_1',
    name: '制造业'
  },
  {
    id: 2,
    key: 'item_1_2',
    name: '消费零售'
  },
  {
    id: 3,
    key: 'item_1_3',
    name: '信息技术'
  },
  {
    id: 4,
    key: 'item_1_4',
    name: '能源'
  },
  {
    id: 5,
    key: 'item_1_5',
    name: '金融地产'
  },
  {
    id: 6,
    key: 'item_1_6',
    name: '服务业'
  },
  {
    id: 7,
    key: 'item_1_7',
    name: '医疗保健'
  },
  {
    id: 8,
    key: 'item_1_8',
    name: '原材料'
  },
  {
    id: 9,
    key: 'item_1_9',
    name: '工业制造'
  },
  {
    id: 10,
    key: 'item_1_10',
    name: '军工'
  },
  {
    id: 11,
    key: 'item_1_11',
    name: '公用事业'
  }
]

// 作品案例销售额
const DESIGN_CASE_SALE_OPTIONS = [
  {
    id: 1,
    name: '100-500w'
  },
  {
    id: 2,
    name: '500-1000w'
  },
  {
    id: 3,
    name: '1000-5000w'
  },
  {
    id: 4,
    name: '5000-10000w'
  },
  {
    id: 5,
    name: '10000w以上'
  }

]

// 项目周期
const CYCLE_OPTIONS = [
  {
    id: 1,
    name: '1个月内'
  },
  {
    id: 2,
    name: '1-2个月'
  },
  {
    id: 3,
    name: '2-3个月'
  },
  {
    id: 4,
    name: '3-4个月'
  },
  {
    id: 5,
    name: '4个月以上'
  }

]

// 证件类型
const DOCUMENT_TYPE = [
  {
    id: 1,
    name: '身份证'
  },
  {
    id: 2,
    name: '港澳通行证'
  },
  {
    id: 3,
    name: '台胞证'
  },
  {
    id: 4,
    name: '护照'
  }

]

// 需求方公司类型
const COMPANY_PROPERTY_TYPE = [
  {
    id: 1,
    name: '初创企业'
  },
  {
    id: 2,
    name: '私企'
  },
  {
    id: 3,
    name: '国有企业'
  },
  {
    id: 4,
    name: '事业单位'
  },
  {
    id: 5,
    name: '外资'
  },
  {
    id: 6,
    name: '合资'
  },
  {
    id: 7,
    name: '上市公司'
  }

]

// 设计费用预算
const DESIGN_COST_OPTIONS = [
  {
    id: 1,
    name: '5万以内'
  },
  {
    id: 2,
    name: '5-10万'
  },
  {
    id: 3,
    name: '10-20万'
  },
  {
    id: 4,
    name: '20-30万'
  },
  {
    id: 5,
    name: '30-50万'
  },
  {
    id: 6,
    name: '50万以上'
  }

]

// 公司规模
const COMPANY_SIZE = [
  {
    id: 1,
    name: '20人以下'
  },
  {
    id: 2,
    name: '20-50人'
  },
  {
    id: 3,
    name: '50-100人'
  },
  {
    id: 4,
    name: '100-300人'
  },
  {
    id: 5,
    name: '300人以上'
  }
]

// 银行卡信息
const BANK_OPTIONS = [
  {
    id: 1,
    name: '中国建设银行',
    mark: 'js'
  },
  {
    id: 2,
    name: '中国银行',
    mark: 'zh'
  },
  {
    id: 3,
    name: '中国农业银行',
    mark: 'ny'
  },
  {
    id: 4,
    name: '中国工商银行',
    mark: 'gs'
  },
  {
    id: 6,
    name: '民生银行',
    mark: 'ms'
  },
  {
    id: 7,
    name: '招商银行',
    mark: 'zs'
  },
  {
    id: 8,
    name: '兴业银行',
    mark: 'xy'
  },
  {
    id: 9,
    name: '国家开发银行',
    mark: 'gjkf'
  },
  {
    id: 10,
    name: '汇丰银行',
    mark: 'hf'
  },
  {
    id: 11,
    name: '中国人民银行',
    mark: 'rm'
  },
  {
    id: 12,
    name: '中国光大银行',
    mark: 'gd'
  },
  {
    id: 13,
    name: '中信银行',
    mark: 'zx'
  },
  {
    id: 14,
    name: '交通银行',
    mark: 'jt'
  },
  {
    id: 15,
    name: '华夏银行',
    mark: 'hx'
  },
  {
    id: 16,
    name: '深圳发展银行',
    mark: 'szfz'
  },
  {
    id: 17,
    name: '浦发银行',
    mark: 'pf'
  },
  {
    id: 18,
    name: '中国邮政储蓄银行',
    mark: 'yzcx'
  }
]

// 公司证件类型certificate
const COMPANY_CERTIFICATE_TYPE = [
  {
    id: 1,
    name: '普通营业执照（存在独立的组织机构代码证）'
  },
  {
    id: 2,
    name: '多证合一营业执照（不含统一社会信用代码）'
  },
  {
    id: 3,
    name: '多证合一营业执照（含统一社会信用代码）'
  }
]

// 栏目内容分类
const COLUMN_TYPE = [
  {
    id: 1,
    name: '灵感'
  },
  {
    id: 2,
    name: '其它'
  }
]

// 分类类别
const CATEGORY_TYPE = [
  {
    id: 1,
    name: '文章'
  },
  {
    id: 2,
    name: '未定义'
  }
]

// 文章类别
const ARTICLE_TYPE = [
  {
    id: 1,
    name: '文章'
  },
  {
    id: 2,
    name: '专题'
  }
]

// 用户属性
const USER_KIND = [
  {
    id: 1,
    name: '默认'
  },
  {
    id: 2,
    name: '鸟人'
  }
]

// 日历类别
const AWARDS_TYPE = [
  {
    id: 1,
    name: '设计大赛'
  },
  {
    id: 2,
    name: '节日'
  },
  {
    id: 3,
    name: '展会'
  },
  {
    id: 4,
    name: '事件'
  }
]

// 日历属性
const AWARDS_EVT = [
  {
    id: 1,
    name: '颜色1'
  },
  {
    id: 2,
    name: '颜色2'
  },
  {
    id: 3,
    name: '颜色3'
  },
  {
    id: 4,
    name: '颜色4'
  }
]

// 常用风站类型
const COMMONLY_SITE_TYPE = [
  {
    id: 1,
    name: '设计资讯'
  },
  {
    id: 2,
    name: '创意灵感'
  },
  {
    id: 3,
    name: '众筹'
  },
  {
    id: 4,
    name: '商业咨询'
  },
  {
    id: 5,
    name: '设计奖项'
  },
  {
    id: 99,
    name: '其它'
  }
]

// 奖项案例分类
const AWARD_CASE_CATEGORY = [
  {
    id: 1,
    name: 'IDEA设计奖',
    img: require('assets/images/awards_logo/4.png')
  },
  {
    id: 2,
    name: 'G-Mark设计奖',
    img: require('assets/images/awards_logo/3.png')
  },
  {
    id: 3,
    name: 'IF设计奖',
    img: require('assets/images/awards_logo/1.png')
  },
  {
    id: 4,
    name: '红点设计奖',
    img: require('assets/images/awards_logo/2.png')
  },
  {
    id: 99,
    name: '其它',
    img: require('assets/images/awards_logo/0.png')
  }
]

// 通知目标人群
const NOTICE_EVT = [
  {
    id: 0,
    name: '全部'
  },
  {
    id: 1,
    name: '需求方'
  },
  {
    id: 2,
    name: '设计公司'
  }
]

// test
const TEST = {}

module.exports = {
  COMPANY_TYPE,
  FIELD,
  INDUSTRY,
  DESIGN_CASE_PRICE_OPTIONS,
  DESIGN_CASE_SALE_OPTIONS,
  CYCLE_OPTIONS,
  DESIGN_COST_OPTIONS,
  COMPANY_SIZE,
  BANK_OPTIONS,
  DOCUMENT_TYPE,
  COMPANY_PROPERTY_TYPE,
  COMPANY_CERTIFICATE_TYPE,
  COLUMN_TYPE,
  CATEGORY_TYPE,
  ARTICLE_TYPE,
  USER_KIND,
  AWARDS_TYPE,
  AWARDS_EVT,
  COMMONLY_SITE_TYPE,
  AWARD_CASE_CATEGORY,
  NOTICE_EVT,

  TEST
}
