<?php

namespace App\Models;

use App\Helper\Tools;
use Illuminate\Database\Eloquent\Model;

class DemandCompany extends Model
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'demand_company';


    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'company_abbreviation',
        'company_size',
        'company_web',
        'province',
        'city',
        'area',
        'address',
        'contact_name',
        'phone',
        'email',
        'logo',
        'verify_status',
        'position',
    ];

    //公司规模
    public function getCompanySizeValueAttribute()
    {
        switch ($this->company_size){
            case 1:
                $company_size_val = '10人以下';
                break;
            case 2:
                $company_size_val = '10-50人之间';
                break;
            case 3:
                $company_size_val = '50-100人之间';
                break;
            case 4:
                $company_size_val = '100人以上';
                break;
            case 5:
                $company_size_val = '初创公司';
                break;
            default:
                $company_size_val = '';
        }
        return $company_size_val;
    }

    /**
     * 获取图片url
     *
     * @return array
     */
    public function getImageAttribute()
    {
        return AssetModel::getOneImage($this->logo);
    }

    /**
     * 省份访问修改器
     * @return mixed|string
     */
    public function getProvinceValueAttribute()
    {
        return Tools::cityName($this->province);
    }

    /**
     * 城市访问修改器
     * @return mixed|string
     */
    public function getCityValueAttribute()
    {
        return Tools::cityName($this->city);
    }

    /**
     * 区县访问修改器
     * @return mixed|string
     */
    public function getAreaValueAttribute()
    {
        return Tools::cityName($this->area);
    }

    /**
     * 更改需求公司审核状态
     */
    static public function verifyStatus($id, $verify_status=1)
    {
        $demand_company = self::findOrFail($id);
        $demand_company->verify_status = $verify_status;
        return $demand_company->save();
    }

    /**
     * 获取图片附件
     *
     * @return array
     */
    public function getAnnexImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 9, 1 , 5);
    }

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
