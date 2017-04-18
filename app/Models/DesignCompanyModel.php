<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignCompanyModel extends Model
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'design_company';


    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_type',
        'company_name',
        'registration_number',
        'province',
        'city',
        'area',
        'address',
        'contact_name',
        'position',
        'phone',
        'email',
        'company_size',
        'branch_office',
        'item_quantity',
        'company_profile',
        'good_field',
        'web',
        'design_type',
        'establishment_time',
        'professional_advantage',
        'awards',
        'company_abbreviation'
    ];

    /**
     * 更新设计公司状态
     */
    static public function upStatus($id, $status=1)
    {
        $design_company = self::findOrFail($id);
        $design_company->status = $status;
        return $design_company->save();
    }

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
