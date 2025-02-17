<?php
/**
 * Created by PhpStorm.
 * User: cailiguang
 * Date: 2018/10/15
 * Time: 下午4:40
 */

namespace App\Http\Controllers\Api\Wx;


use App\Models\DesignCompanyModel;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use Illuminate\Http\Request;
use App\Http\Transformer\DesignCaseListsTransformer;
use App\Models\DesignCaseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        if (empty($item_name)) {
            return $this->response->array($this->apiError('项目名称不能为空', 412));
        }
        //这边要给内存，不然会炸
        ini_set('memory_limit', '1024M');

        Jieba::init();
        Finalseg::init();
        //把标题分词
        $seg_lists = Jieba::cut($item_name);
        //过滤掉无用的词
        $new_seg_lists = array_diff($seg_lists , ["设计" , "案例" , "系列" , "升级"]);
        $design_cases_array = [];

        foreach ($new_seg_lists as $seg_list){
            //　过滤掉一个字和标点符号
            if (strlen($seg_list) < 6) {
                continue;
            }
            //模糊查询有的话，走上面，没有的话走下面
            $design_cases = DesignCaseModel::where('label' , 'like', '%' . $seg_list . '%')->orWhere('title' , 'like', '%' . $seg_list . '%')->get();
            if($design_cases->isEmpty()){
                continue;
            }
            //遍历到最小单位
            foreach ($design_cases as $design_case) {
                $design_cases_array[] = $design_case;
            }
        }
        //分词搜索为空的话，随机返回10个
        if ($design_cases_array == null) {
            $mend_design_cases = DesignCaseModel::
            orderBy(DB::raw('RAND()'))
                ->take(10)
                ->get();
            return $this->response->collection($mend_design_cases, new DesignCaseListsTransformer())->setMeta($this->apiMeta());
        } else {
            //检测分词搜索出来的数量
            $count = count($design_cases_array);
            //合并新的，看看是否够10条数据，不够的话补全10条，够的话直接返回
            if ($count < 10) {
                $mend_count = 10 - $count;
                $mend_design_cases = DesignCaseModel::
                orderBy(DB::raw('RAND()'))
                    ->take($mend_count)
                    ->get();
                $new_merge_cases = (collect([$design_cases_array, $mend_design_cases]))->collapse();
                return $this->response->collection($new_merge_cases, new DesignCaseListsTransformer())->setMeta($this->apiMeta());
            }
            return $this->response->collection(collect($design_cases_array), new DesignCaseListsTransformer())->setMeta($this->apiMeta());
        }
    }
}