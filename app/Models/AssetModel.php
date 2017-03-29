<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetModel extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * 关联模型到数据表
     * @var string
     */
    protected $table = 'assets';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = ['user_id','name','random','size','width','height','mime','domain','path','target_id','type'];

}
