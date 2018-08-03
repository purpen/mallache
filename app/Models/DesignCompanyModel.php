<?php

namespace App\Models;

use App\Helper\Tools;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DesignCompanyModel extends BaseModel
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
        'status',
        'company_abbreviation',
        'is_recommend',
        'verify_status',
        'unique_id',
        'logo',
        'legal_person',
        'document_type',
        'document_number',
        'open',
        'company_english',
        'revenue',
        'weixin_id',
        'high_tech_enterprises',
        'industrial_design_center',
        'investment_product',
        'own_brand'
    ];

    /**
     * 返回
     */
    protected $appends = [
        'company_type_val',
        'company_size_val',
        'logo_image',
        'design_type_value',

    ];

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * 一对多关联推荐关联表
     */
    public function itemRecommend()
    {
        return $this->hasMany('App\Models\ItemRecommend', 'design_company_id');
    }


    /**
     * 一对多关联合同表
     */
    public function contract()
    {
        return $this->hasMany('App\Models\Contract', 'design_company_id');
    }

    /*
     * 一对多 关联项目表
     */
    public function item()
    {
        return $this->hasMany('App\Models\Item', 'design_company_id');
    }

    // 一对一关联 佣金免费次数表
    public function commissionCount()
    {
        return $this->hasOne('App\Models\CommissionCount', 'design_company_id');
    }

    /**
     * 一对多关联报价表
     */
    public function quotation()
    {
        return $this->hasMany('App\Models\QuotationModel', 'design_company_id');
    }

    /**
     * 更改设计公司审核状态
     */
    static public function verifyStatus($id, $verify_status = 0, $verify_summary = ' ')
    {
        $design_company = self::findOrFail($id);
        $design_company->verify_status = $verify_status;
        if ($verify_summary) {
            $design_company->verify_summary = $verify_summary;
        }

        return $design_company->save();
    }

    /**
     * 更改设计公司状态
     */
    static public function unStatus($id, $status = 1)
    {
        $design_company = self::findOrFail($id);
        $design_company->status = $status;
        return $design_company->save();
    }

    //企业类型
    public function getCompanyTypeValAttribute()
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

    //企业人数规模
    public function getCompanySizeValAttribute()
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

    //服务项目
    public function getItemQuantityValAttribute()
    {
        switch ($this->attributes['item_quantity']) {
            case 1:
                $item_quantity_val = '10以下';
                break;
            case 2:
                $item_quantity_val = '10-50';
                break;
            case 3:
                $item_quantity_val = '50-100';
                break;
            case 4:
                $item_quantity_val = '100-200';
                break;
            case 5:
                $item_quantity_val = '200以上';
                break;
            default:
                $item_quantity_val = '';
        }
        return $item_quantity_val;
    }


    /**
     * 获取图片logo
     *
     * @return array
     */
    public function getLogoImageAttribute()
    {
        return AssetModel::getOneImage($this->logo);
    }


    /**
     * 获取图片license
     *
     * @return array
     */
    public function getLicenseImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 3, 1, 5);
    }

    /**
     * 一对多关联案例表
     */
    public function designCase()
    {
        return $this->hasMany('App\Models\DesignCaseModel', 'design_company_id');
    }


    /**
     * 省份访问修改器
     * @return mixed|string
     */
    public function getCompanyProvinceValueAttribute()
    {
        return Tools::cityName($this->province);
    }

    /**
     * 城市访问修改器
     * @return mixed|string
     */
    public function getCompanyCityValueAttribute()
    {
        return Tools::cityName($this->city);
    }

    /**
     * 区县访问修改器
     * @return mixed|string
     */
    public function getCompanyAreaValueAttribute()
    {
        return Tools::cityName($this->area);
    }


    /**
     * 验证有无设计公司访问权限
     *
     * @param int $user_id 访问用户user_iD
     * @param int $design_company_id 公司ID
     * @return bool
     */
    public function isRead($user_id, $design_company_id)
    {
        $design = DesignCompanyModel::find($design_company_id);
        $item_s = Item::where('user_id', $user_id)->get();

        //检查是否推荐了该公司
        $is_recommend = false;
        foreach ($item_s as $item) {
            if (in_array($design_company_id, explode(',', $item->recommend))) {
                $is_recommend = true;
                break;
            }
        }

        // 是否是管理员
        $is_admin = User::isAdmin($user_id);

        //公司是否开放、是否自己访问自己、是否推荐了该公司
        if ($design->open != 1 && $design->user_id != $user_id && $is_recommend != true && $is_admin != true) {
            return false;
        }

        return true;
    }

    public function getCityArrAttribute()
    {
        $array = [110000, 120000, 310000, 500000];
        if (in_array($this->province, $array)) {
            return [
                $this->company_province_value,
                $this->company_area_value,
            ];
        } else {
            return [
                $this->company_province_value,
                $this->company_city_value,
            ];
        }
    }


    /**
     * 获取图片document
     *
     * @return array
     */
    public function getDocumentImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 10, 1, 5);
    }


    //证件类型
    public function getDocumentTypeValAttribute()
    {
        $key = $this->attributes['document_type'];
        if (array_key_exists($key, config('constant.document_type'))) {
            $document_type_val = config('constant.document_type')[$key];
            return $document_type_val;

        }
        return '';
    }

    //设计公司接单类型
    public function getDesignTypeValueAttribute()
    {
        $item_type = config('constant.item_type');
        $arr = [];
        $design_item = $this->user ? $this->user->designItem : '';
        if (!$design_item->isEmpty()) {
            foreach ($design_item as $case) {
                try {
                    $arr[] = $item_type[$case->type][$case->design_type];
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return $arr;
    }

    // 擅长领域访问修改器
    public function getGoodFieldAttribute($key)
    {
        if (empty($key)) {
            return [];
        } else {
            $arr = explode(',', $key);
            $arr = array_map(function ($v) {
                return intval($v);
            }, $arr);

            return $arr;
        }
    }

    // 擅长领域访问修改器--文字
    public function getGoodFieldValueAttribute()
    {
        $data = $this->good_field;

        if (empty($data)) {
            return [];
        } else {
            $field_array = config('constant.field');
            $good_field_array = [];
            foreach ($data as $v) {
                $good_field_array[] = $field_array[$v];
            }

            return $good_field_array;
        }
    }

    //创建设计公司
    static public function createDesign($user_id)
    {
        $all['company_abbreviation'] = '';
        $all['legal_person'] = '';
        $all['document_type'] = 0;
        $all['document_number'] = '';
        $all['open'] = 0;
        $all['user_id'] = $user_id;
        $user = User::where('id', $user_id)->first();
        $design = DesignCompanyModel::create($all);

        if ($design) {
            $user->design_company_id = $design->id;
            $user->save();
            return $design;
        } else {
            return false;
        }
    }

    //公司营收
    public function getRevenueValueAttribute()
    {
        $revenue = $this->revenue;
        $revenue_value = config('constant.revenue');
        if (!array_key_exists($revenue, $revenue_value)) {
            return null;
        } else {
            return $revenue_value[$revenue];
        }
    }

}
