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
    protected $table = 'design_companys';


    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = ['user_id', 'company_type', 'company_name', 'registration_number', 'province', 'city', 'area', 'address', 'contact_name', 'position' , 'phone', 'email' , 'company_size' , 'branch_office' , 'item_quantity' , 'company_profile' , 'good_field' , 'web' , 'design_type' , 'establishment_time' , 'professional_advantage' , 'awards'];


}
