<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /*user_id	int(10)	否		用户ID
    design_type	tinyint(4)	是		设计类别：1.产品策略；2.产品设计；3.结构设计；4.app设计；5.网页设计；
    field	int(10)	是		所属领域： class_id
    status	tinyint(4)	是		状态：1.准备；2.发布；3失败；4成功；5.取消；*/

    protected $table = 'item';

    /**
     * 允许批量赋值属性
     */

    protected $fillable = ['user_id', 'design_type', 'field', 'status'];
}
