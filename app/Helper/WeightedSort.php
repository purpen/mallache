<?php

/**
 * 设计公司加权值计算
 */
namespace App\Helper;

use Illuminate\Support\Facades\DB;

class WeightedSort
{
    //加权分数
    protected $score = 0;

    //用户ID
    protected $user_id;

    // 设计公司ID
    protected $design_company_id;

    /*配置项
      '表名' => [
       // sql语句where条件
        'where' => 'user_id',
        //bool类型选项
        'bool' =>[
            '字段' => '加权值',
            '字段' => '加权值',
        ],
        //枚举类型选项
        'enum' =>[
            '字段' =>[
            '选项' => '加权值',
            '选项' => '加权值',
            ],
        ],
        // 累加类型选项
        'accumulation' => [
            '字段' => '需要乘以的基数 如：3，1等',
        ],
    ],*/
    public $weighted_config = [
        // 设计公司表
        'design_company' => [
            // sql语句where条件
            'where' => 'user_id',
            //bool类型选项
            'bool' =>[
                'branch_office' => '10', //是否有分公司
            ],
            //枚举类型选项
            'enum' =>[
                'company_size' =>[       // 公司规模
                    '1' => '10',
                    '2' => '20',
                    '3' => '30',
                    '4' => '40',
                    '5' => '50',
                ],
            ],
        ],

        //设计公司案例表
        'design_case' => [
            // sql语句where条件
            'where' => 'user_id',
            //bool类型选项
            'bool' => [
                'mass_production' => '100',  // 案例是否量产
            ],
            //枚举类型选项
            'enum' =>[
                // 获得奖项
                'prize' => [
                    1 => 50,        // '德国红点设计奖',
                    2 => 50,        // '德国IF设计奖',
                    3 => 50,        // 'IDEA工业设计奖',
                    4 => 50,        // 中国红星奖;
                    5 => 50,        // .中国红棉奖;
                    6 => 50,        // .台湾金点奖;
                    7 => 50,        // .香港DFA设计奖 ;
                    8 => 50,        //.日本G-Mark设计奖;
                    9 => 50,        //.韩国好设计奖;
                    10 => 50,       //.新加坡设计奖;
                    11 => 50,       //.意大利—Compasso d`Oro设计奖;
                    12 => 50,       //.英国设计奖;
                ],
                // 案例销售金额
                'sales_volume' => [
                    1 => 10,        // .100-500w;
                    2 => 20,        // .500-1000w;
                    3 => 30,        // .1000-5000w;
                    4 => 40,        // .5000-10000w;
                    5 => 50,        // .10000w以上
                ],
            ],
        ],

        // 评价表
        'evaluate' => [
            'where' => 'design_company_id',
            // 累加类型选项
            'accumulation' => [
                'score' => 10,    // 项目评价分
            ],
        ],

    ];

    public function __construct(int $user_id, int $design_company_id)
    {
        $this->user_id = $user_id;
        $this->design_company_id = $design_company_id;
        $this->weightNumber();
    }

    /**
     * 获取给定用户加权分数
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    //计算给定用户的加权分
    protected function weightNumber()
    {
        foreach ($this->weighted_config as $key => $value) {
            $this->weightedCalculation($key, $value);
        }

        return intval($this->score);
    }

    /**
     * 计算给定数据表中的加权分
     */
    protected function weightedCalculation($table, array $data)
    {
        $query = DB::table($table)->query();

        switch ($data['where']){
            case 'user_id':
                $query->where('user_id', $this->user_id);
                break;
            case 'design_company_id':
                $query->where('design_company_id', $this->design_company_id);
                break;
        }
        $table_data = $query->get();
        if($table_data->isEmpty()){
            return;
        }
        foreach ($table_data as $value) {

            $bool_config = $data['bool'];
            $this->boolCalculation($value, $bool_config);

            $enum_config = $data['enum'];
            $this->enumCalculation($value, $enum_config);

            $accumulation_config = $data['accumulation'];
            $this->accumulationCalculation($value, $accumulation_config);
        }
    }

    /**
     * 计算bool类型的字段加权分
     */
    protected function boolCalculation($line_data, $bool_config)
    {
        foreach ($bool_config as $k =>$v) {
            if($line_data->$k > 0){
                $this->score += intval($v);
                break;
            }
        }
    }

    /**
     * 计算枚举类型的字段加权分
     */
    protected function enumCalculation($line_data, $enum_config)
    {
        foreach ($enum_config as $key1 => $value1) {
            //遍历选项配置，增加对应加权分
            foreach ($value1 as $key2 => $value2) {
                if($line_data->$key1 == $key2){
                    $this->score += intval($value2);
                    break;
                }
            }
        }
    }

    /**
     * 计算累加类型的字段分值
     */
    protected function accumulationCalculation($line_data, $accumulation_config)
    {
        foreach ($accumulation_config as $key => $value) {
                if($line_data->$key > 0){
                    $this->score += ($line_data->$key * $value);
                }
        }
    }

}

