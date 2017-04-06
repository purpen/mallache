<?php

namespace App\Http\Transformer;

use App\Models\DesignCaseModel;
use League\Fractal\TransformerAbstract;

class DesignCaseTransformer extends TransformerAbstract
{
    /*
        id	            int(10)	        否		ID
        user_id	        int(10)	        否		用户ID
        title	        varchar(50)	    否		标题
        prize	        tinyint(4)	    是		奖项
        prize_time	    date	        是		获奖时间
        mass_production	tinyint(4)	    否		是否量产
        sales_volume	decimal(10,2)	是		销售金额
        customer	    varchar(50)	    否		服务客户
        field	        tinyint(4)	    否		所属领域 class_id
        profile	        varchar(500)	否		项目描述
        status	        tinyint(4)	    否		状态
    */

    public function transform(DesignCaseModel $designCase)
    {
        return [
            'id' => intval($designCase->id),
            'prize' => intval($designCase->prize),
            'title' => strval($designCase->title),
            'prize_time' => strval($designCase->prize_time),
            'sales_volume' => strval($designCase->sales_volume),
            'customer' => strval($designCase->customer),
            'field' => intval($designCase->field),
            'profile' => strval($designCase->profile),
            'status' => intval($designCase->status),


        ];
    }
}