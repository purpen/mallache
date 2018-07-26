<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Transformer\DesignTargetTransformer;
use App\Models\DesignTarget;
use App\Models\User;
use Illuminate\Http\Request;

class DesignTargetController extends BaseController
{

    /**
     * @api {post} /designTarget/store 计划年营业额，年度项目数量添加更新
     * @apiVersion 1.0.0
     * @apiName designTarget store
     * @apiGroup designTarget
     *
     * @apiParam {string} turnover 项目营业额
     * @apiParam {integer} count 项目数量
     * @apiParam {string} token
     */
    public function store(Request $request)
    {
        //获取当年是那一年
        $year = strtotime(date('Y'));
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        $turnover = $request->input('turnover');
        $count = $request->input('count');
        $design_target = DesignTarget::where('year' , $year)->where('design_company_id' , $design_company_id)->first();
        //检查是否是管理员以上级别
        if (!$this->auth_user->isDesignAdmin()) {
            return $this->response->array($this->apiError('没有权限创建', 403));
        }
        //有的话更新，没有创建
        if ($design_target){
            if (!empty($turnover)){
                $design_target->turnover = $turnover;
                $design_target->save();
            }
            if ($count != 0){
                $design_target->count = $count;
                $design_target->save();
            }
            return $this->response->item($design_target, new DesignTargetTransformer())->setMeta($this->apiMeta());
        } else {
            $new_design_target = new DesignTarget();
            //营销额不是空的话，保存
            if (!empty($turnover)){
                $new_design_target->turnover = $turnover;
                $new_design_target->year = $year;
                $new_design_target->design_company_id = $design_company_id;
                $new_design_target->save();
            }
            if ($count != 0){
                $design_target->count = $count;
                $new_design_target->year = $year;
                $new_design_target->design_company_id = $design_company_id;
                $design_target->save();
            }
            return $this->response->item($new_design_target, new DesignTargetTransformer())->setMeta($this->apiMeta());

        }
    }

    /**
     * @api {get} /designTarget/show 详情
     * @apiVersion 1.0.0
     * @apiName designTarget show
     * @apiGroup designTarget
     *
     * @apiParam {string} token
     */
    public function show()
    {
        //获取当年是那一年
        $year = strtotime(date('Y'));
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        $design_target = DesignTarget::where('year' , $year)->where('design_company_id' , $design_company_id)->first();
    }
}