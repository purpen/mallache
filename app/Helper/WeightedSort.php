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

    protected $user_id = null;

    /*配置项
     * '表名' => [
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
    ],*/
    public $weighted_config = [
        'design_company' => [
            //bool类型选项
            'bool' =>[
                'branch_office' => '10',
            ],
            //枚举类型选项
            'enum' =>[
                'company_size' =>[
                    '1' => '10',
                    '2' => '20',
                    '3' => '30',
                    '4' => '40',
                ],
                'item_quantity' => [
                    '1' => '10',
                    '2' => '20',
                    '3' => '30',
                    '4' => '40',
                    '5' => '50',
                ],
            ],
        ],

    ];

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
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
        $table_data = DB::table($table)->where('user_id', $this->user_id)->get();
        if($table_data->isEmpty()){
            return;
        }
        foreach ($table_data as $value) {

            $bool_config = $data['bool'];
            $this->boolCalculation($value, $bool_config);

            $enum_config = $data['enum'];
            $this->enumCalculation($value, $enum_config);
        }
    }

    /**
     * 计算bool类型的字段加权分
     */
    protected function boolCalculation($line_data, $bool_config)
    {
        foreach ($bool_config as $k =>$v) {
            if($line_data->$k === 1){
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
                if($line_data->$key1 === $key2){
                    $this->score += intval($value2);
                    break;
                }
            }
        }
    }

}

