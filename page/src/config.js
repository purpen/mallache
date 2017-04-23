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
        name: '产品设计'
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

module.exports = {
  COMPANY_TYPE,
  DESIGN_CASE_PRICE_OPTIONS,
  DESIGN_CASE_SALE_OPTIONS
}
