<?php
namespace App\Http\Controllers\Api\Admin;

use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use App\Models\User;

class SummaryActionController extends BaseController
{
    //后台汇总信息
    public function summary()
    {

    }

    // 设计公司数量
    protected function designCompanyQuantity()
    {
        return (int)DesignCompanyModel::query()->count();
    }

    // 需求公司数量
    protected function demandCompanyQuantity()
    {
        return (int)DemandCompany::query()->count();
    }

    // 项目数量
    protected function itemQuantity()
    {
        return (int)Item::query()->count();
    }

    //用户数量总数量
    protected function userQuantity()
    {
        return (int)User::query()->count();
    }
}