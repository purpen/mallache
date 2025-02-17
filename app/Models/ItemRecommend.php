<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRecommend extends BaseModel
{
    protected $table = 'item_recommend';

    protected $fillable = [
        'item_id',
        'design_company_id',
        'summary',
        'refuse_types',
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

    public function getItemStatusValueAttribute()
    {
        switch ($this->item_status){
            case -1:
                $item_status_value = '项目需求方拒绝';
                break;
            case 0:
                $item_status_value = '等待设计公司接单';
                break;
            case 1:
                $item_status_value = '选定设计公司';
                break;
            default:
                $item_status_value = '';
        }
        return $item_status_value;
    }

    public function getDesignCompanyStatusValueAttribute()
    {
        switch ($this->design_company_status){
            case -1:
                $design_company_status_value = '设计公司拒绝';
                break;
            case 0:
                $design_company_status_value = '等待接单';
                break;
            case 1:
                $design_company_status_value = '设计公司一键成交';
                break;
            case 2:
                $design_company_status_value = '已接单';
                break;
            default:
                $design_company_status_value = '';
        }
        return $design_company_status_value;
    }

    /**
     * 项目意向接单数量
     *
     * @param $item_id
     * @return int
     */
    public static function purposeCount($item_id)
    {
        return  (int)self::where(['item_id' => $item_id, 'design_company_status' => 2])
            ->where('item_status', '!=', -1)
            ->count();
    }

    //项目推荐报价信息列表
    public function quotationList($item_id)
    {
        return self::where(['item_id' => $item_id, 'design_company_status' => 2])->get();
    }

    //项目推荐状态转换
    public function itemStatus()
    {
        $item_status  = $this->item_status;
        $design_company_status = $this->design_company_status;

        if($item_status == -1){
            $status = 1;
            $status_value = '有机会再合作吧';
            $design_status_value = '报价未被采纳';
        }elseif ($item_status == 0 && $design_company_status == -1){
            $status = 2;
            $status_value = '设计服务供应商对此项目不感兴趣';
            $design_status_value = '不感兴趣';
        }elseif($item_status == 0 && $design_company_status == 0){
            $status = 3;
            $status_value = '等待设计服务供应商提交报价单';
            $design_status_value = '请提交报价单';
        }elseif($item_status == 0 && $design_company_status == 2){
            $status = 4;
            $status_value = '请确认设计服务供应商报价单内容';
            $design_status_value = '报价单已发送给项目需求方，等待对方确认';
        }elseif ($item_status == 1){
            $status = 5;
            $status_value = '确认合作';
            $design_status_value = '确认合作';
        }

        return compact('status', 'status_value', 'design_status_value');
    }

}
