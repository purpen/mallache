<?php

namespace App\Models;

use App\Helper\Tools;
use Illuminate\Database\Eloquent\Model;

class DemandCompany extends BaseModel
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
                $company_size_val = '20人以下';
                break;
            case 2:
                $company_size_val = '20-50人之间';
                break;
            case 3:
                $company_size_val = '50-100人之间';
                break;
            case 4:
                $company_size_val = '100-300人之间';
                break;
            case 5:
                $company_size_val = '300人以上';
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
    static public function verifyStatus($id, $verify_status)
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

    /**
     * 创建需求公司信息
     *
     * @param int $user_id
     * @return bool
     */
    public static function createCompany(int $user_id)
    {
        $all = [
            'company_name' => '',
            'company_abbreviation' => '',
            'company_size' => 0,
            'company_web' => '',
            'province' => 0,
            'city' => 0,
            'area' => 0,
            'address' => '',
            'contact_name' => '',
            'phone' => 0,
            'email' => '',
            'position' => '',
            'user_id' => $user_id,
        ];

        $user = User::where('id' , $user_id)->first();
        $demand = DemandCompany::create($all);
        $user->demand_company_id = $demand->id;
        if($demand){
            $user->save();
        }

        return true;
    }
}
