<?php

namespace App\Models;


class CommissionCount extends BaseModel
{
    protected $table = 'commission_count';

    // 相对一对一关联设计公司表
    public function designCompany()
    {
        return $this->belongsTo('App\Models\DesignCompanyModel', 'design_company_id');
    }

    /**
     * 添加设计公司的优惠数量
     *
     * @param array $design_company_id_arr 设计公司ID数组
     * @param int $count 添加的优惠数量
     */
    public static function addCount(array $design_company_id_arr, $count)
    {
        foreach ($design_company_id_arr as $id) {
            $com = CommissionCount::firstOrCreate(['design_company_id' => $id]);
            $com->increment('count', $count);
        }
    }

    // 设计公司是有是否有优惠

    /**
     * @param int $design_company_id 设计公司ID
     * @return bool|\Illuminate\Database\Eloquent\Model|null|static 有返回优惠的model对象 无但会false
     */
    public static function isPreferential($design_company_id)
    {
        $com = CommissionCount::query()
            ->where('design_company_id', '=', $design_company_id)
            ->first();

        if (!$com || $com->count <= 0) {
            return false;
        }

        return $com;
    }

    // 减少一次优惠次数
    public function removeOne()
    {
        $this->decrement('count', 1);

        if ($this->count <= 0) {
            $this->delete();
        }
    }

    public function info()
    {
        return [
            'id' => $this->id,
            'count' => $this->count,
            'design_company_id' => $this->design_company_id,
            'design_company_name' => $this->designCompany->company_name,
        ];
    }

}
