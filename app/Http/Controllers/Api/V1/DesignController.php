<?php
/**
 * 设计公司相关操作控制器
 */

namespace App\Http\Controllers\Api\V1;

use App\Models\DesignCompanyModel;
use App\Models\ItemRecommend;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DesignController extends BaseController
{

    //获取项目信息列表
    public function ItemList()
    {
        if(!$design_company = $this->auth_user->designCompany){
            return $this->response->array($this->apiSuccess());
        }

        ItemRecommend::where('design_company_id', $design_company->id);
    }
}
