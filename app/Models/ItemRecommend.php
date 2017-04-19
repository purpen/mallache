<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRecommend extends Model
{
    protected $table = 'item_recommend';

    protected $fillable = [
        'item_id',
        'design_company_id',
    ];


    /**
     * 相对关联需求项目表
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id');
    }

    /**
     * 相对关联设计公司表
     */
    public function designCompany()
    {
        return $this->belongsTo('App\Models\DesignCompanyModel', 'design_company_id');
    }

    /**
     *相对关联报价单表
     */
    public function quotation()
    {
        return $this->belongsTo('App\Models\QuotationModel', 'quotation_id');
    }

    /**
     * 项目意向接单数量
     *
     * @param $item_id
     * @return int
     */
    public static function purposeCount($item_id)
    {
        return  (int)self::where(['item_id' => $item_id, 'design_company_status' => 2])->count();
    }

    //项目推荐报价信息列表
    public function quotationList($item_id)
    {
        return self::where(['item_id' => $item_id, 'design_company_status' => 2])->get();
    }

}
