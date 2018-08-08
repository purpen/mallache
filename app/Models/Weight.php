<?php

namespace App\Models;

class Weight extends BaseModel
{
    /**
     * 关联模型到数据表
     *
     * @var string
     */
    protected $table = 'weight';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = ['score','case','last_time','success_rate','average_price','area'];

    /**
     * 保存权重
     * $params['score']    //评价评分权重值
     * $params['case']    //案例数量权重值
     * $params['last_time']    //最近推荐时间
     * $params['success_rate']    //接单成功率权重值
     * $params['average_price']    //接单单价权重值
     * $params['area']    //地区权重值
     */
    public function saveWeight($params)
    {
        $res = $this::first();
        if(empty($res)){
            $params['created_at'] = date('Y-m-d H:i:s',time());
            return $this::insert($params);
        }else{
            return $this::where('id',$res->id)->update($params);
        }
    }

    /**
     * 权重详情
     */
    public function getWeight()
    {
        return $this::select('score','case','last_time','success_rate','average_price','area')->first();
    }
}
