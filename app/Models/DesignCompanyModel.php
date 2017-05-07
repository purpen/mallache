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
        'status',
        'company_abbreviation',
        'is_recommend',
        'verify_status',
        'unique_id'
    ];

    /**
     * 返回
     */
    protected $appends = [
        'company_type_val',
        'company_size_val',
//        'item_quantity_val',

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
     * 一对多关联推荐关联表
     */
    public function contract()
    {
        return $this->hasMany('App\Models\Contract', 'design_company_id');
    }

    /**
     * 更改设计公司审核状态
     */
    static public function verifyStatus($id, $verify_status=1)
    {
        $design_company = self::findOrFail($id);
        $design_company->verify_status = $verify_status;
        return $design_company->save();
    }

    /**
     * 更改设计公司状态
     */
    static public function unStatus($id, $status=-1)
    {
        $design_company = self::findOrFail($id);
        $design_company->status = $status;
        return $design_company->save();
    }

    //企业类型
    public function getCompanyTypeValAttribute()
    {
        return $this->attributes['company_type'] == 1 ? '普通' : '多证合一';
    }

    //企业人数规模
    public function getCompanySizeValAttribute()
    {
        switch ($this->attributes['company_size']){
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
        return AssetModel::getImageUrl($this->id, 6, 1);
    }


    /**
     * 获取图片license
     *
     * @return array
     */
    public function getLicenseImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 3, 1 , 5);
    }

    /**
     * 一对多关联案例表
     */
    public function designCase()
    {
        return $this->hasMany('App\Models\DesignCaseModel', 'design_company_id');
    }

    /**
     * 验证有无设计公司访问权限
     *
     * @param int $user_id  访问用户user_iD
     * @param int $design_company_id 公司ID
     * @return bool
     */
    public function isRead($user_id, $design_company_id)
    {
        $design = DesignCompanyModel::find($design_company_id);
        $item_s = Item::where('user_id', $user_id)->get();

        //检查是否推荐了该公司
        $is_recommend = false;
        foreach($item_s as $item){
            if(in_array($design_company_id, explode(',', $item->recommend))){
                $is_recommend = true;
                break;
            }
        }

        //公司是否开放、是否自己访问自己、是否推荐了该公司
        if($design->open != 1 && $design->user != $user_id && $is_recommend != true){
            return false;
        }

        return true;
    }
}
