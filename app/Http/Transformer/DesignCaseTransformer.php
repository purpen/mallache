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
        sales_volume	tinyint(4)  	是		销售金额
        customer	    varchar(50)	    否		服务客户
        field	        tinyint(4)	    否		所属领域 class_id
        profile	        varchar(500)	否		项目描述
        status	        tinyint(4)	    否		状态
        industry	    tinyint(4)	    否		所属行业
        system	        tinyint(4)	    是		系统：1.ios；2.安卓；
        design_content	tinyint(4)	    是		设计内容：1.视觉设计；2.交互设计；
        type	        tinyint(4)	    否		设计类型：1.产品设计；2.UI UX 设计；
        design_type	    tinyint(4)	    是		设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）
        other_prize	    varchar(30)	    是		其他奖项
    */

    public function transform(DesignCaseModel $designCase)
    {
        return [
            'id' => intval($designCase->id),
            'prize' => intval($designCase->prize),
            'prize_val' => $designCase->prize_val,
            'title' => strval($designCase->title),
            'prize_time' => strval($designCase->prize_time),
            'sales_volume' => intval($designCase->sales_volume),
            'sales_volume_val' => $designCase->sales_volume_val,
            'customer' => strval($designCase->customer),
            'field' => intval($designCase->field),
            'field_val' => $designCase->field_val,
            'profile' => strval($designCase->profile),
            'status' => intval($designCase->status),
            'case_image' => $designCase->case_image,
            'industry' => intval($designCase->industry),
            'industry_val' => $designCase->industry_val,
            'type' => intval($designCase->type),
            'type_val' => $designCase->type_val,
            'design_type' => intval($designCase->design_type),
            'design_type_val' => $designCase->design_type_val,
            'other_prize' => strval($designCase->other_prize),

        ];
    }
}