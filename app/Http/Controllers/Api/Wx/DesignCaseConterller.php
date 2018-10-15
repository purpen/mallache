<?php
/**
 * Created by PhpStorm.
 * User: cailiguang
 * Date: 2018/10/15
 * Time: 下午3:51
 */
namespace App\Http\Controllers\Api\Wx;

use App\Http\Transformer\DesignCaseListsTransformer;
use App\Models\DesignCaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesignCaseController extends BaseController
{
    /**
     * @api {post} /wechat/designCase 设计案例搜索项目名称
     * @apiVersion 1.0.0
     * @apiName wechatDemand create
     * @apiGroup wechatDemandType
     *
     * @apiParam {string} item_name
     */
    public function lists(Request $request)
    {
        $item_name = $request->input('item_name');
        //模糊查询有的话，走上面，没有的话走下面
        $designCases = DesignCaseModel::where('item_name' , 'like', '%' . $item_name . '%')->limit(10)->get();
        if($designCases){
            return $this->response->collection($designCases, new DesignCaseListsTransformer())->setMeta($this->apiMeta());
        }
        $design_cases = DesignCaseModel::
            orderBy(DB::raw('RAND()'))
            ->take(10)
            ->get();
        return $this->response->collection($design_cases, new DesignCaseListsTransformer())->setMeta($this->apiMeta());

    }
}