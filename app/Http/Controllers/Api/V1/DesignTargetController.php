<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Transformer\DesignTargetTransformer;
use App\Models\DesignProject;
use App\Models\DesignTarget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesignTargetController extends BaseController
{

    /**
     * @api {post} /designTarget/create 计划年营业额，年度项目数量添加更新
     * @apiVersion 1.0.0
     * @apiName designTarget store
     * @apiGroup designTarget
     *
     * @apiParam {string} turnover 项目营业额
     * @apiParam {integer} count 项目数量
     * @apiParam {string} token
     */
    public function create(Request $request)
    {
        //获取当年是那一年
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        $turnover = $request->input('turnover');
        $count = $request->input('count');
        $design_target = DesignTarget::where('design_company_id' , $design_company_id)->whereYear('created_at', date('Y'))
            ->first();
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
                $new_design_target->design_company_id = $design_company_id;
                $new_design_target->save();
            }
            if ($count != 0){
                $design_target->count = $count;
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
     *
     * @apiSuccessExample 成功响应:
    {
    "data": {
    "id": 1,
    "count": 2,
    "design_company_id": 49,
    "turnover": 100,
    "year": 2018,
    "total_item_counts": 2, //项目总数
    "item_counts": 2, //当年完成的项目数
    "no_item_counts": 0,         //当年没有完成的项目数
    "ok_count_percentage": 100, //完成的项目百分比
    "no_count_percentage": 0, //没完成的项目百分比
    "ok_turnover_percentage": 2, //营业额百分比
    "m_money": 0.29, //月均收入
    "month_on_month": 0, //月环比
    "quarter_on_quarter": 0, //季度环比
    "m_item": 0.3 //月均项目
    },
    "meta": {
    "message": "Success",
    "status_code": 200
    }
    }
     */
    public function show()
    {

        //获取当年是那一年
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        $design_target = DesignTarget::where('design_company_id' , $design_company_id)->whereYear('created_at', date('Y'))->first();

        //获取项目总数
       $total_item_counts = DesignProject
            ::where('design_company_id', $design_company_id)
            ->whereYear('created_at', date('Y'))
            ->count();
        //获取当年的完成项目数量
        $item_counts =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->whereYear('created_at', date('Y'))
            ->get();
        //没有归档的项目数
        $no_item_counts = $total_item_counts - $item_counts->count();
        //获取当年完成的营业额
        $turnover = 0;
        foreach ($item_counts as $item_count){
            $turnover += $item_count->cost;
        }
        if ($design_target){
            //已完成的项目百分比
            if ($design_target->count == 0){
                $ok_count_percentage = 0 ;
            } else {
                $ok_count_percentage = round(($item_counts->count() / $design_target->count) * 100, 0);
            }
        } else {
            $ok_count_percentage = 0;
        }

        //未完成的项目百分比
        $no_count_percentage = 100 - $ok_count_percentage;
        //已收入的百分比
        if ($turnover == 0){
            $ok_turnover_percentage = 0;
        } else {
            $ok_turnover_percentage = round(($turnover / $design_target->turnover) * 100, 0);
        }
        //月均收入
        if ($turnover == 0){
            $m_money = 0;
        } else {
            $m_money =  round(($turnover / date('m')),2);
        }
        //月均项目
        if ($item_counts->count() == 0){
            $m_item = 0;
        } else {
            $m_item =  round(($item_counts->count() / date('m')),1);
        }

        //当月完成的项目
        $month_item_counts = DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->whereMonth('created_at', date('m'))
            ->get();
        //当月项目收入
        $current_m_money = 0;
        foreach ($month_item_counts as $month_item_count){
            $current_m_money += $month_item_count->cost;
        }
        //如果是1月份，就获取去年12月份的数据
        if (date('m') == 1){
            //上个月完成的项目
            $last_month_item_counts = DesignProject
                ::where('design_company_id', $design_company_id)
                ->where('pigeonhole', 1)
                ->whereYear('created_at', date('Y')-1)
                ->whereMonth('created_at', 12)
                ->get();
        } else {
            //上个月完成的项目
            $last_month_item_counts = DesignProject
                ::where('design_company_id', $design_company_id)
                ->where('pigeonhole', 1)
                ->whereMonth('created_at', date('m')-1)
                ->get();
        }
        //上月项目收入
        $last_current_m_money = 0;
        foreach ($last_month_item_counts as $last_month_item_count){
            $last_current_m_money += $last_month_item_count->cost;
        }
        //月环比
        $month_on_month = round((($current_m_money - $last_current_m_money) / 100 ) * 100 , 0);
        //当前季度
        $quarter_item_counts = DB::select("select * from design_project where design_company_id = $design_company_id and pigeonhole = 1 and quarter(created_at)=quarter(now())");
        $current_q_money = 0;
        foreach ($quarter_item_counts as $quarter_item_count){
            $current_q_money += $quarter_item_count->cost;
        }
        //上个季度
        $last_quarter_item_counts = DB::select("select * from design_project where design_company_id = $design_company_id and pigeonhole = 1 and quarter(created_at)=quarter(date_sub(now(),interval 1 quarter))");
        $last_current_m_money = 0;
        foreach ($last_quarter_item_counts as $last_quarter_item_count){
            $last_current_m_money += $last_quarter_item_count->cost;
        }
        //季度环比
        $quarter_on_quarter = round((($current_q_money - $last_current_m_money) / 100 ) * 100 , 0);

        //项目总数
        $design_target['total_item_counts'] = $total_item_counts;
        //当年完成的项目数
        $design_target['item_counts'] = $item_counts->count();
        //当年没有完成的项目数
        $design_target['no_item_counts'] = $no_item_counts;
        //完成的项目百分比
        $design_target['ok_count_percentage'] = $ok_count_percentage;
        //没完成的项目百分比
        $design_target['no_count_percentage'] = $no_count_percentage;
        //营业额百分比
        $design_target['ok_turnover_percentage'] = $ok_turnover_percentage;
        //月均收入
        $design_target['m_money'] = $m_money;
        //月环比
        $design_target['month_on_month'] = $month_on_month;
        //月均项目
        $design_target['m_item'] = $m_item;
        //季度环比
        $design_target['quarter_on_quarter'] = $quarter_on_quarter;


        return $this->response->item($design_target, new DesignTargetTransformer())->setMeta($this->apiMeta());

    }
}