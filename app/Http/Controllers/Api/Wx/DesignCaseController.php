<?php
/**
 * Created by PhpStorm.
 * User: cailiguang
 * Date: 2018/10/15
 * Time: 下午4:40
 */
namespace App\Http\Controllers\Api\Wx;


use Illuminate\Http\Request;
use App\Http\Transformer\DesignCaseListsTransformer;
use App\Models\DesignCaseModel;
use Illuminate\Support\Facades\DB;

class DesignCaseController extends BaseController
{
    /**
     * @api {get} /wechat/designCase 设计案例搜索项目名称
     * @apiVersion 1.0.0
     * @apiName designCase lists
     * @apiGroup wechatDemandType
     *
     * @apiParam {string} item_name
     */
    public function lists(Request $request)
    {
        $item_name = $request->input('item_name');
        //模糊查询有的话，走上面，没有的话走下面
        $designCases = DesignCaseModel::where('title' , 'like', '%' . $item_name . '%')->limit(10)->get();
]        $designCaseCount = $designCases->count();
        //等于10的话走上面，下面不够10的话补全
        if($designCaseCount == 10){
            return $this->response->collection($designCases, new DesignCaseListsTransformer())->setMeta($this->apiMeta());
        } else {
            $mendCount = 10 - $designCaseCount;
            $mend_design_cases = DesignCaseModel::
            orderBy(DB::raw('RAND()'))
                ->take($mendCount)
                ->get();

            $merge_case = collect(array_merge((array)$designCases , (array)$mend_design_cases));
            return $this->response->collection($merge_case, new DesignCaseListsTransformer())->setMeta($this->apiMeta());
        }
    }
}