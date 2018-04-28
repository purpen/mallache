<?php

namespace App\Models;

use App\Helper\Tools;

class DesignClient extends BaseModel
{
    protected $table = 'design_client';

    protected $fillable = [
        'company_name',
        'contact_name',
        'position',
        'phone',
        'province',
        'city',
        'area',
        'address',
        'design_company_id',
        'user_id',
    ];

    /**
     * 省份访问修改器
     * @return mixed|string
     */
    public function getCompanyProvinceValueAttribute()
    {
        return Tools::cityName($this->company_province);
    }

    /**
     * 城市访问修改器
     * @return mixed|string
     */
    public function getCompanyCityValueAttribute()
    {
        return Tools::cityName($this->company_city);
    }

    /**
     * 区县访问修改器
     * @return mixed|string
     */
    public function getCompanyAreaValueAttribute()
    {
        return Tools::cityName($this->company_area);
    }

    public function info()
    {
        return [
            'id' => intval($this->id),
            'company_name' => $this->company_name,
            'contact_name' => $this->contact_name,
            'position' => $this->position,
            'phone' => $this->phone,
            'province' => intval($this->province),
            'province_value' => $this->province_value,
            'city' => intval($this->city),
            'city_value' => $this->city_value,
            'area' => intval($this->area),
            'area_value' => $this->area_value,
            'address' => $this->address,
            'user_id' => intval($this->user_id),
        ];
    }


}
