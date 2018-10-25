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

    protected $appends = [
        'company_size_value',
        'province_value',
        'city_value',
        'area_value',
        'license_image',
        'document_type_value',
        'company_property_value',
        'company_type_value',
        'logo_image',
        'document_image',
    ];

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
        'company_type',
        'registration_number',
        'legal_person',
        'document_type',
        'document_number',
        'company_property',
        'account_name',
        'bank_name',
        'account_number',
        'source',
    ];

    //公司规模
    public function getCompanySizeValueAttribute()
    {
        switch ($this->company_size) {
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
    public function getLogoImageAttribute()
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

    //证件类型
    public function getDocumentTypeValueAttribute()
    {
        $key = $this->attributes['document_type'];
        if (array_key_exists($key, config('constant.document_type'))) {
            $document_type_val = config('constant.document_type')[$key];
            return $document_type_val;

        }
        return '';
    }

    //企业性质
    public function getCompanyPropertyValueAttribute()
    {
        if (array_key_exists($this->company_property, config('constant.company_property'))) {
            return config('constant.company_property')[$this->company_property];

        }
        return '';
    }

    //企业类型
    public function getCompanyTypeValueAttribute()
    {
        switch ($this->company_type) {
            case 1:
                $company_type_val = '普通';
                break;
            case 2:
                $company_type_val = '多证合一(不含信用代码)';
                break;
            case 3:
                $company_type_val = '多证合一(含信用代码)';
                break;
            default:
                $company_type_val = '';
        }
        return $company_type_val;
    }

    /**
     * 更改需求公司审核状态
     */
    static public function verifyStatus($id, $verify_status, $verify_summary = ' ')
    {
        $demand_company = self::findOrFail($id);
        $demand_company->verify_status = $verify_status;
        if ($verify_summary) {
            $demand_company->verify_summary = $verify_summary;
        }

        return $demand_company->save();
    }

    /**
     * 获取图片附件
     *
     * @return array
     */
    public function getLicenseImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 9, 1, 5);
    }

    /**
     * 法人证件附件
     */
    public function getDocumentImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 11, 1);
    }

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * 一对多关联顺德设计需求
     */
    public function designDemand()
    {
        return $this->hasOne('App\Models\DesignDemand', 'demand_company_id');
    }
    /**
     * 创建需求公司信息
     *
     * @param int $user_id
     * @return bool
     */
    public static function createCompany(User $user)
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
            'phone' => '',
            'email' => '',
            'position' => '',
            'user_id' => $user->id,
            'document_type' => 0,
            'source' => $user->source,
        ];

        $demand = DemandCompany::create($all);

        if ($demand) {
            $user->demand_company_id = $demand->id;
            $user->save();
            return $demand;
        } else {
            return false;
        }
    }

    /**
     * 判断需求公司是否通过审核
     * @return bool
     */
    public function isVerify()
    {
        if ($this->verify_status == 1) {
            return true;
        }

        return false;
    }
}
