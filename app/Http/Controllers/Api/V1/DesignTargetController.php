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
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        $turnover = $request->input('turnover');
        $count = $request->input('count');
        $year = date('Y');
        $design_target = DesignTarget::where('design_company_id' , $design_company_id)->whereYear('created_at', date('Y'))
            ->first();

        //检查是否是管理员以上级别
        if (!$this->auth_user->isDesignAdmin()) {
            return $this->response->array($this->apiError('没有权限创建', 403));
        }
        //获取当年的完成项目数量
        $item_counts =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->whereYear('created_at', date('Y'))
            ->count();
        //有的话更新，没有创建
        if ($design_target){
            if (!empty($turnover)){
                $design_target->turnover = $turnover;
                $design_target->year = $year;
                $design_target->save();
            }
            if ($count != 0){
                $design_target->count = $count;
                $design_target->year = $year;
                $design_target->save();
            }
            return $this->response->item($design_target, new DesignTargetTransformer())->setMeta($this->apiMeta());
        } else {
            $new_design_target = new DesignTarget();
            $new_design_target->turnover = $turnover ?? 0;
            $new_design_target->design_company_id = $design_company_id;
            $new_design_target->year = $year;
            $new_design_target->count = $count ?? 0;
            $new_design_target->save();

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
    "ok_turnover": 2, //已收入的营业额
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

        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        $design_target = DesignTarget::where('design_company_id' , $design_company_id)->whereYear('created_at', date('Y'))->first();

        if ($design_target){
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
                if ($design_target->turnover != 0){
                    $ok_turnover_percentage = round(($turnover / $design_target->turnover) * 100, 0);
                } else {
                    $ok_turnover_percentage = 0;
                }
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
            //已收入的金额
            $design_target['ok_turnover'] = $turnover;
            //月均收入
            $design_target['m_money'] = $m_money;
            //月环比
            $design_target['month_on_month'] = $month_on_month;
            //月均项目
            $design_target['m_item'] = $m_item;
            //季度环比
            $design_target['quarter_on_quarter'] = $quarter_on_quarter;

            return $this->response->item($design_target, new DesignTargetTransformer())->setMeta($this->apiMeta());
        } else {
            $design_target['count'] = 0;
            $design_target['design_company_id'] = 0;
            $design_target['turnover'] = 0;
            //项目总数
            $design_target['total_item_counts'] = 0;
            //当年完成的项目数
            $design_target['item_counts'] = 0;
            //当年没有完成的项目数
            $design_target['no_item_counts'] = 0;
            //完成的项目百分比
            $design_target['ok_count_percentage'] = 0;
            //没完成的项目百分比
            $design_target['no_count_percentage'] = 0;
            //营业额百分比
            $design_target['ok_turnover_percentage'] = 0;
            //已收入的金额
            $design_target['ok_turnover'] = 0;
            //月均收入
            $design_target['m_money'] = 0;
            //月环比
            $design_target['month_on_month'] = 0;
            //月均项目
            $design_target['m_item'] = 0;
            //季度环比
            $design_target['quarter_on_quarter'] = 0;
            return $this->response->array($this->apiSuccess('没有创建项目目标，营业额', 200 , $design_target));

        }


    }

    /**
     * @api {get} /designTarget/incomeMonth 收入月报表
     * @apiVersion 1.0.0
     * @apiName designTarget incomeMonth
     * @apiGroup designTarget
     *
     * @apiParam {string} token
     */
    public function incomeMonth()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        //当月完成的项目
        $incomeMonths = DB::select("select sum(cost) as sum_day_cost , count(id) as item_day_count , date_format(created_at , '%Y%m%d') as month_day from design_project where design_company_id = $design_company_id and pigeonhole = 1 and DATE_FORMAT(created_at, '%Y%m' ) = DATE_FORMAT(CURDATE(),'%Y%m') group by month_day");
        //总价
        $total_money = 0;

        $total_ok_item_count = 0;
        //获取当年的项目总数量
        $total_month_item_count =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->whereMonth('created_at', date('m'))
            ->count();

        $month = [];
        foreach ($incomeMonths as $incomeMonth){
            $v = [];
            $v['sum_day_cost'] = $incomeMonth->sum_day_cost;
            $v['item_day_count'] = $incomeMonth->item_day_count;
            $v['month_day'] = $incomeMonth->month_day;
            $month[] = $v;
            $total_money += $incomeMonth->sum_day_cost;
            $total_ok_item_count += $incomeMonth->item_day_count;
        }
        $data['incomeMonths'] = $month;

        //平均单价
        if ($total_ok_item_count == 0){
            $average = 0;
        } else {
            $average = round(($total_money / $total_ok_item_count),1);
        }
        $data['total_month_ok_count'] = $total_ok_item_count;
        $data['total_money'] = $total_money;
        $data['average'] = $average;
        //月总项目数量
        $data['total_month_item_count'] = $total_month_item_count;
        //没完成的月项目数量
        $data['total_no_count'] = $total_month_item_count - $total_ok_item_count;
        //完成的百分比
        if ($total_month_item_count == 0){
            $data['ok_count_percentage'] = 0;
        } else {
            $data['ok_count_percentage'] = round(($total_ok_item_count / $total_month_item_count) * 100 , 0);
        }
        //没完成的百分比
        $data['no_count_percentage'] = 100 - $data['ok_count_percentage'];
        return $this->response->array($this->apiSuccess('获取成功', 200 , $data));

    }

    /**
     * @api {get} /designTarget/incomeQuarter 收入季度报表
     * @apiVersion 1.0.0
     * @apiName designTarget incomeQuarter
     * @apiGroup designTarget
     *
     * @apiParam {string} token
     */
    public function incomeQuarter()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);

        //返回季度月份，总价
        $incomeQuarters = DB::select("select sum(cost) as sum_month_cost , count(id) as item_count , date_format(created_at , '%Y%m') as quarter_month from design_project where design_company_id = $design_company_id and pigeonhole = 1 and quarter(created_at)=quarter(now()) group by quarter_month");

        //获取当季度的项目总数量
        $total_quarter_item_counts = DB::select("select count(id) as item_quarter_count from design_project where design_company_id = $design_company_id and quarter(created_at)=quarter(now())");

        //季度总数量默认0
        $total_quarter_item_count = 0;
        foreach ($total_quarter_item_counts as $total_quarter){
            $total_quarter_item_count = $total_quarter->item_quarter_count;
        }
        //总价
        $total_money = 0;
        //数量
        $total_ok_item_count = 0;
        $quarter = [];
        foreach ($incomeQuarters as $incomeQuarter){
            $v = [];
            $v['cost'] = $incomeQuarter->sum_month_cost;
            $v['item_count'] = $incomeQuarter->item_count;
            $v['quarter_month'] = $incomeQuarter->quarter_month;
            $quarter[] = $v;
            $total_money += $incomeQuarter->sum_month_cost;
            $total_ok_item_count += $incomeQuarter->item_count;
        }

        $data['incomeQuarters'] = $quarter;

        //平均单价
        if ($total_ok_item_count == 0){
            $average = 0;
        } else {
            $average = round(($total_money / $total_ok_item_count),1);
        }
        $data['total_quarter_ok_count'] = $total_ok_item_count;
        $data['total_money'] = $total_money;
        $data['average'] = $average;
        //季度总项目数量
        $data['total_quarter_item_count'] = $total_quarter_item_count;
        //没完成的季度项目数量
        $data['total_no_count'] = $total_quarter_item_count - $total_ok_item_count;
        //完成的百分比
        if ($total_quarter_item_count == 0){
            $data['ok_count_percentage'] = 0;
        } else {
            $data['ok_count_percentage'] = round(($total_ok_item_count / $total_quarter_item_count) * 100 , 0);
        }
        //没完成的百分比
        $data['no_count_percentage'] = 100 - $data['ok_count_percentage'];

        return $this->response->array($this->apiSuccess('获取成功', 200 , $data));

    }

    /**
     * @api {get} /designTarget/incomeYear 收入年报表
     * @apiVersion 1.0.0
     * @apiName designTarget incomeYear
     * @apiGroup designTarget
     *
     * @apiParam {string} token
     */
    public function incomeYear()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        //获取当年的完成项目数量
        //返回季度月份，总价
        $incomeYears = DB::select("select sum(cost) as sum_month_cost , count(id) as item_count , date_format(created_at , '%Y%m') as year_m from design_project where design_company_id = $design_company_id and pigeonhole = 1 and YEAR(created_at)=YEAR(NOW()) group by year_m");

        //获取当年的项目总数量
        $total_year_item_count =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->whereYear('created_at', date('Y'))
            ->count();
        //总价
        $total_money = 0;
        //数量
        $total_ok_item_count = 0;
        $quarter = [];
        foreach ($incomeYears as $incomeYear){
            $v = [];
            $v['cost'] = $incomeYear->sum_month_cost;
            $v['item_count'] = $incomeYear->item_count;
            $v['year_m'] = $incomeYear->year_m;
            $quarter[] = $v;
            $total_money += $incomeYear->sum_month_cost;
            $total_ok_item_count += $incomeYear->item_count;
        }

        $data['incomeYears'] = $quarter;

        //平均单价
        if ($total_ok_item_count == 0){
            $average = 0;
        } else {
            $average = round(($total_money / $total_ok_item_count),1);
        }
        $data['total_ok_count'] = $total_ok_item_count;
        $data['total_money'] = $total_money;
        $data['average'] = $average;
        //年总项目数量
        $data['total_year_item_count'] = $total_year_item_count;
        //没完成的年项目数量
        $data['total_no_count'] = $total_year_item_count - $total_ok_item_count;
        //完成的百分比
        if ($total_year_item_count == 0){
            $data['ok_count_percentage'] = 0;
        } else {
            $data['ok_count_percentage'] = round(($total_ok_item_count / $total_year_item_count) * 100 , 0);
        }
        //没完成的百分比
        $data['no_count_percentage'] = 100 - $data['ok_count_percentage'];

        return $this->response->array($this->apiSuccess('获取成功', 200 , $data));
    }

    /**
     * @api {get} /designTarget/incomeRanked 项目收入排名
     * @apiVersion 1.0.0
     * @apiName designTarget incomeRanked
     * @apiGroup designTarget
     *
     * @apiParam {string} token
     */
    public function incomeRanked()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        //获取当年的项目
        $total_year_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->whereYear('created_at', date('Y'))
            ->get();
        //总价钱
        $total_year_item_money = 0;
        foreach ($total_year_items as $total_year_item){
            $total_year_item_money +=  $total_year_item->cost;
        }
        $year_20_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->whereYear('created_at', date('Y'))
            ->orderBy('cost' , 'desc')
            ->limit(20)
            ->get();
        $income = [];
        //前20收入百分比
        $total_cost_percentage = 0;
        //前20的收入
        $cost_money = 0;
        foreach ($year_20_items as $year_20_item){
            $v = [];
            $v['name'] = $year_20_item->name;
            $v['cost'] = $year_20_item->cost;
            if ($total_year_item_money == 0){
                $v['cost_percentage'] = 0;
            } else {
                $v['cost_percentage'] = round(($year_20_item->cost / $total_year_item_money ) * 100 , 1) ;
            }
            $total_cost_percentage += $v['cost_percentage'];
            $cost_money += $v['cost'];
            $income[] = $v;
        }
        $data['income20'] = $income;
        $data['incomeOther'] = 100 - (int)$total_cost_percentage;
        $data['incomeOtherMoney'] = $total_year_item_money - (int)$cost_money;

        return $this->response->array($this->apiSuccess('获取成功', 200 , $data));

    }

    /**
     * @api {get} /designTarget/incomeType 设计类别
     * @apiVersion 1.0.0
     * @apiName designTarget incomeType
     * @apiGroup designTarget
     *
     * @apiParam {string} token
     */
    public function incomeType()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        //获取当年的产品类型项目
        $year_p_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('type', 1)
            ->whereYear('created_at', date('Y'))
            ->get();
        //产品价钱，数量
        $year_p_money = 0;
        $year_p_count = 0;
        foreach ($year_p_items as $year_p_item){
            $year_p_money += $year_p_item->cost;
            $year_p_count += 1;
        }

        //获取当年的ui类型项目
        $year_u_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('type', 2)
            ->whereYear('created_at', date('Y'))
            ->get();
        //ui价钱，数量
        $year_u_money = 0;
        $year_u_count = 0;
        foreach ($year_u_items as $year_u_item){
            $year_u_money += $year_u_item->cost;
            $year_u_count += 1;
        }

        $data['year_p_count'] = $year_p_count;
        $data['year_p_money'] = $year_p_money;
        if ($year_p_money + $year_u_money == 0){
            $data['year_p_percentage'] = 0;
        } else {
            $data['year_p_percentage'] = round(($year_p_money / ($year_p_money + $year_u_money)) * 100 , 0);
        }
        $data['year_u_count'] = $year_u_count;
        $data['year_u_money'] = $year_u_money;
        if ($year_p_money + $year_u_money == 0){
            $data['year_u_percentage'] = 0;
        } else {
            $data['year_u_percentage'] = round(($year_u_money / ($year_p_money + $year_u_money)) * 100 , 0);
        }
        $data['total_year_count'] = $year_p_count + $year_u_count;
        $data['total_year_money'] = $year_p_money + $year_u_money;

        return $this->response->array($this->apiSuccess('获取成功', 200 , $data));

    }

    /**
     * @api {get} /designTarget/incomeDesignTypes 设计详细类别
     * @apiVersion 1.0.0
     * @apiName designTarget incomeDesignTypes
     * @apiGroup designTarget
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "meta": {
    "message": "获取成功",
    "status_code": 200
    },
    "data": {
    "year_p_s_count": 0, //获取当年的产品策略
    "year_p_s_money": 0,
    "year_p_p_count": 0, //产品设计
    "year_p_p_money": 0,
    "year_p_c_count": 0, //结构设计
    "year_p_c_money": 0,
    "year_u_a_count": 0, //ui类型app
    "year_u_a_money": 0,
    "year_u_w_count": 0, //ui类型,网页
    "year_u_w_money": 0,
    "year_u_i_count": 0,  //ui类型,界面
    "year_u_i_money": 0,
    "year_u_s_count": 0, //ui类型,服务
    "year_u_s_money": 0,
    "year_u_u_count": 0, //ui类型,用户体验
    "year_u_u_money": 0,
    "year_p_s_percentage": 0,
    "year_p_p_percentage": 0,
    "year_p_c_percentage": 0,
    "year_u_a_percentage": 0,
    "year_u_w_percentage": 0,
    "year_u_i_percentage": 0,
    "year_u_s_percentage": 0,
    "year_u_u_percentage": 0,
    "total_year_count": 0,
    "total_year_money": 0
    }
    }
     */
    public function incomeDesignTypes()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        //获取当年的产品策略
        $year_p_s_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('type', 1)
            ->where('design_types', 1)
            ->whereYear('created_at', date('Y'))
            ->get();
        //产品策略价钱，数量
        $year_p_s_money = 0;
        $year_p_s_count = 0;
        foreach ($year_p_s_items as $year_p_s_item){
            $year_p_s_money += $year_p_s_item->cost;
            $year_p_s_count += 1;
        }
        $data['year_p_s_count'] = $year_p_s_count;
        $data['year_p_s_money'] = $year_p_s_money;
        //产品设计
        $year_p_p_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('type', 1)
            ->where('design_types', 2)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_p_p_money = 0;
        $year_p_p_count = 0;
        foreach ($year_p_p_items as $year_p_p_item){
            $year_p_p_money += $year_p_p_item->cost;
            $year_p_p_count += 1;
        }
        $data['year_p_p_count'] = $year_p_p_count;
        $data['year_p_p_money'] = $year_p_p_money;
        //结构设计
        $year_p_c_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('type', 1)
            ->where('design_types', 3)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_p_c_money = 0;
        $year_p_c_count = 0;
        foreach ($year_p_c_items as $year_p_c_item){
            $year_p_c_money += $year_p_c_item->cost;
            $year_p_c_count += 1;
        }
        $data['year_p_c_count'] = $year_p_c_count;
        $data['year_p_c_money'] = $year_p_c_money;
        //获取当年的ui类型app
        $year_u_a_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('type', 2)
            ->where('design_types', 1)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_u_a_money = 0;
        $year_u_a_count = 0;
        foreach ($year_u_a_items as $year_u_a_item){
            $year_u_a_money += $year_u_a_item->cost;
            $year_u_a_count += 1;
        }
        $data['year_u_a_count'] = $year_u_a_count;
        $data['year_u_a_money'] = $year_u_a_money;
        //ui类型,网页
        $year_u_w_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('type', 2)
            ->where('design_types', 2)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_u_w_money = 0;
        $year_u_w_count = 0;
        foreach ($year_u_w_items as $year_u_w_item){
            $year_u_w_money += $year_u_w_item->cost;
            $year_u_w_count += 1;
        }
        $data['year_u_w_count'] = $year_u_w_count;
        $data['year_u_w_money'] = $year_u_w_money;
        //ui类型,界面
        $year_u_i_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('type', 2)
            ->where('design_types', 3)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_u_i_money = 0;
        $year_u_i_count = 0;
        foreach ($year_u_i_items as $year_u_i_item){
            $year_u_i_money += $year_u_i_item->cost;
            $year_u_i_count += 1;
        }
        $data['year_u_i_count'] = $year_u_i_count;
        $data['year_u_i_money'] = $year_u_i_money;
        //ui类型,服务
        $year_u_s_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('type', 2)
            ->where('design_types', 4)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_u_s_money = 0;
        $year_u_s_count = 0;
        foreach ($year_u_s_items as $year_u_s_item){
            $year_u_s_money += $year_u_s_item->cost;
            $year_u_s_count += 1;
        }
        $data['year_u_s_count'] = $year_u_s_count;
        $data['year_u_s_money'] = $year_u_s_money;
        //ui类型,用户体验
        $year_u_u_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('type', 2)
            ->where('design_types', 5)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_u_u_money = 0;
        $year_u_u_count = 0;
        foreach ($year_u_u_items as $year_u_u_item){
            $year_u_u_money += $year_u_u_item->cost;
            $year_u_u_count += 1;
        }
        $data['year_u_u_count'] = $year_u_u_count;
        $data['year_u_u_money'] = $year_u_u_money;

        if ($year_p_s_money + $year_p_p_money + $year_p_c_money + $year_u_a_money + $year_u_w_money + $year_u_i_money + $year_u_s_money + $year_u_u_money == 0){
            $data['year_p_s_percentage'] = 0;
            $data['year_p_p_percentage'] = 0;
            $data['year_p_c_percentage'] = 0;
            $data['year_u_a_percentage'] = 0;
            $data['year_u_w_percentage'] = 0;
            $data['year_u_i_percentage'] = 0;
            $data['year_u_s_percentage'] = 0;
            $data['year_u_u_percentage'] = 0;
        } else {
            if($year_p_s_money == 0){
                $data['year_p_s_percentage'] = 0;
            }else{
                $data['year_p_s_percentage'] = round(($year_p_s_money / ($year_p_s_money + $year_p_p_money + $year_p_c_money + $year_u_a_money + $year_u_w_money + $year_u_i_money + $year_u_s_money + $year_u_u_money)) * 100 , 0);
            }

            if($year_p_p_money == 0){
                $data['year_p_p_percentage'] = 0;
            }else{
                $data['year_p_p_percentage'] = round(($year_p_p_money / ($year_p_s_money + $year_p_p_money + $year_p_c_money + $year_u_a_money + $year_u_w_money + $year_u_i_money + $year_u_s_money + $year_u_u_money)) * 100 , 0);
            }
            if($year_p_c_money == 0){
                $data['year_p_c_percentage'] = 0;
            }else{
                $data['year_p_c_percentage'] = round(($year_p_c_money / ($year_p_s_money + $year_p_p_money + $year_p_c_money + $year_u_a_money + $year_u_w_money + $year_u_i_money + $year_u_s_money + $year_u_u_money)) * 100 , 0);
            }
            if($year_u_a_money == 0){
                $data['year_u_a_percentage'] = 0;
            }else{
                $data['year_u_a_percentage'] = round(($year_u_a_money / ($year_p_s_money + $year_p_p_money + $year_p_c_money + $year_u_a_money + $year_u_w_money + $year_u_i_money + $year_u_s_money + $year_u_u_money)) * 100 , 0);
            }
            if($year_u_w_money == 0){
                $data['year_u_w_percentage'] = 0;
            }else{
                $data['year_u_w_percentage'] = round(($year_u_w_money / ($year_p_s_money + $year_p_p_money + $year_p_c_money + $year_u_a_money + $year_u_w_money + $year_u_i_money + $year_u_s_money + $year_u_u_money)) * 100 , 0);
            }
            if($year_u_i_money == 0){
                $data['year_u_i_percentage'] = 0;
            }else{
                $data['year_u_i_percentage'] = round(($year_u_i_money / ($year_p_s_money + $year_p_p_money + $year_p_c_money + $year_u_a_money + $year_u_w_money + $year_u_i_money + $year_u_s_money + $year_u_u_money)) * 100 , 0);
            }
            if($year_u_s_money == 0){
                $data['year_u_s_percentage'] = 0;
            }else{
                $data['year_u_s_percentage'] = round(($year_u_s_money / ($year_p_s_money + $year_p_p_money + $year_p_c_money + $year_u_a_money + $year_u_w_money + $year_u_i_money + $year_u_s_money + $year_u_u_money)) * 100 , 0);
            }
            if($year_u_u_money == 0){
                $data['year_u_u_percentage'] = 0;
            }else{
                $data['year_u_u_percentage'] = 100 - ($year_p_s_money + $year_p_p_money + $year_p_c_money + $year_u_a_money + $year_u_w_money + $year_u_i_money + $year_u_s_money);
            }
        }

        $data['total_year_count'] = $year_p_s_count + $year_p_p_count + $year_p_c_count + $year_u_a_count + $year_u_w_count + $year_u_i_count + $year_u_s_count + $year_u_u_count;
        $data['total_year_money'] = $year_p_s_money + $year_p_p_money + $year_p_c_money + $year_u_a_money + $year_u_w_money + $year_u_i_money + $year_u_s_money + $year_u_u_money;

        return $this->response->array($this->apiSuccess('获取成功', 200 , $data));
    }

    /**
     * @api {get} /designTarget/incomeIndustry 项目行业
     * @apiVersion 1.0.0
     * @apiName designTarget incomeIndustry
     * @apiGroup designTarget
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "meta": {
    "message": "获取成功",
    "status_code": 200
    },
    "data": {
    "year_industry_m_money": 0, //制造业
    "year_industry_m_count": 0,
    "year_industry_c_r_money": 0, //消费零售
    "year_industry_c_r_count": 0,
    "year_industry_m_t_money": 205, //信息技术
    "year_industry_m_t_count": 2,
    "year_industry_e_money": 0, //能源
    "year_industry_e_count": 0,
    "year_industry_f_r_money": 0, //金融地产
    "year_industry_f_r_count": 0,
    "year_industry_s_money": 0, //服务业
    "year_industry_s_count": 0,
    "year_industry_m_h_money": 0, //医疗保健
    "year_industry_m_h_count": 0,
    "year_industry_r_money": 0, //原材料
    "year_industry_r_count": 0,
    "year_industry_i_p_money": 0, //工业制品
    "year_industry_i_p_count": 0,
    "year_industry_w_i_money": 0, //军工
    "year_industry_w_i_count": 0,
    "year_industry_p_c_money": 0, //公用事业
    "year_industry_p_c_count": 0,
    "year_industry_m_percentage": 0,
    "year_industry_c_r_percentage": 0,
    "year_industry_m_t_percentage": 100,
    "year_industry_e_percentage": 0,
    "year_industry_f_r_percentage": 0,
    "year_industry_s_percentage": 0,
    "year_industry_m_h_percentage": 0,
    "year_industry_r_percentage": 0,
    "year_industry_i_p_percentage": 0,
    "year_industry_w_i_percentage": 0,
    "year_industry_p_c_percentage": 0,
    "total_year_count": 2,
    "total_year_money": 205
    }
    }
     */
    public function incomeIndustry()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        //获取当年的行业-制造业
        $year_industry_m_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('industry', 1)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_industry_m_money = 0;
        $year_industry_m_count = 0;
        foreach ($year_industry_m_items as $year_industry_m_item){
            $year_industry_m_money += $year_industry_m_item->cost;
            $year_industry_m_count += 1;
        }
        $data['year_industry_m_money'] = $year_industry_m_money;
        $data['year_industry_m_count'] = $year_industry_m_count;
        //获取当年的行业-消费零售
        $year_industry_c_r_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('industry', 2)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_industry_c_r_money = 0;
        $year_industry_c_r_count = 0;
        foreach ($year_industry_c_r_items as $year_industry_c_r_item){
            $year_industry_c_r_money += $year_industry_c_r_item->cost;
            $year_industry_c_r_count += 1;
        }
        $data['year_industry_c_r_money'] = $year_industry_c_r_money;
        $data['year_industry_c_r_count'] = $year_industry_c_r_count;
        //获取当年的行业-信息技术
        $year_industry_m_t_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('industry', 3)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_industry_m_t_money = 0;
        $year_industry_m_t_count = 0;
        foreach ($year_industry_m_t_items as $year_industry_m_t_item){
            $year_industry_m_t_money += $year_industry_m_t_item->cost;
            $year_industry_m_t_count += 1;
        }
        $data['year_industry_m_t_money'] = $year_industry_m_t_money;
        $data['year_industry_m_t_count'] = $year_industry_m_t_count;
        //获取当年的行业-能源
        $year_industry_e_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('industry', 4)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_industry_e_money = 0;
        $year_industry_e_count = 0;
        foreach ($year_industry_e_items as $year_industry_e_item){
            $year_industry_e_money += $year_industry_e_item->cost;
            $year_industry_e_count += 1;
        }
        $data['year_industry_e_money'] = $year_industry_e_money;
        $data['year_industry_e_count'] = $year_industry_e_count;
        //获取当年的行业-金融地产
        $year_industry_f_r_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('industry', 5)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_industry_f_r_money = 0;
        $year_industry_f_r_count = 0;
        foreach ($year_industry_f_r_items as $year_industry_f_r_item){
            $year_industry_f_r_money += $year_industry_f_r_item->cost;
            $year_industry_f_r_count += 1;
        }
        $data['year_industry_f_r_money'] = $year_industry_f_r_money;
        $data['year_industry_f_r_count'] = $year_industry_f_r_count;
        //获取当年的行业-服务业
        $year_industry_s_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('industry', 6)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_industry_s_money = 0;
        $year_industry_s_count = 0;
        foreach ($year_industry_s_items as $year_industry_s_item){
            $year_industry_s_money += $year_industry_s_item->cost;
            $year_industry_s_count += 1;
        }
        $data['year_industry_s_money'] = $year_industry_s_money;
        $data['year_industry_s_count'] = $year_industry_s_count;
        //获取当年的行业-医疗保健
        $year_industry_m_h_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('industry', 7)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_industry_m_h_money = 0;
        $year_industry_m_h_count = 0;
        foreach ($year_industry_m_h_items as $year_industry_m_h_item){
            $year_industry_m_h_money += $year_industry_m_h_item->cost;
            $year_industry_m_h_count += 1;
        }
        $data['year_industry_m_h_money'] = $year_industry_m_h_money;
        $data['year_industry_m_h_count'] = $year_industry_m_h_count;
        //获取当年的行业-原材料
        $year_industry_r_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('industry', 8)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_industry_r_money = 0;
        $year_industry_r_count = 0;
        foreach ($year_industry_r_items as $year_industry_r_item){
            $year_industry_r_money += $year_industry_r_item->cost;
            $year_industry_r_count += 1;
        }
        $data['year_industry_r_money'] = $year_industry_r_money;
        $data['year_industry_r_count'] = $year_industry_r_count;
        //获取当年的行业-工业制品
        $year_industry_i_p_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('industry', 9)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_industry_i_p_money = 0;
        $year_industry_i_p_count = 0;
        foreach ($year_industry_i_p_items as $year_industry_i_p_item){
            $year_industry_i_p_money += $year_industry_i_p_item->cost;
            $year_industry_i_p_count += 1;
        }
        $data['year_industry_i_p_money'] = $year_industry_i_p_money;
        $data['year_industry_i_p_count'] = $year_industry_i_p_count;
        //获取当年的行业-军工
        $year_industry_w_i_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('industry', 10)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_industry_w_i_money = 0;
        $year_industry_w_i_count = 0;
        foreach ($year_industry_w_i_items as $year_industry_w_i_item){
            $year_industry_w_i_money += $year_industry_w_i_item->cost;
            $year_industry_w_i_count += 1;
        }
        $data['year_industry_w_i_money'] = $year_industry_w_i_money;
        $data['year_industry_w_i_count'] = $year_industry_w_i_count;
        //获取当年的行业-公用事业
        $year_industry_p_c_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('industry', 11)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_industry_p_c_money = 0;
        $year_industry_p_c_count = 0;
        foreach ($year_industry_p_c_items as $year_industry_p_c_item){
            $year_industry_p_c_money += $year_industry_p_c_item->cost;
            $year_industry_p_c_count += 1;
        }
        $data['year_industry_p_c_money'] = $year_industry_p_c_money;
        $data['year_industry_p_c_count'] = $year_industry_p_c_count;

        if ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money == 0){
            $data['year_industry_m_percentage'] = 0;
            $data['year_industry_c_r_percentage'] = 0;
            $data['year_industry_m_t_percentage'] = 0;
            $data['year_industry_e_percentage'] = 0;
            $data['year_industry_f_r_percentage'] = 0;
            $data['year_industry_s_percentage'] = 0;
            $data['year_industry_m_h_percentage'] = 0;
            $data['year_industry_r_percentage'] = 0;
            $data['year_industry_i_p_percentage'] = 0;
            $data['year_industry_w_i_percentage'] = 0;
            $data['year_industry_p_c_percentage'] = 0;
        } else {
            if($year_industry_m_money == 0){
                $data['year_industry_m_percentage'] = 0;
            }else{
                $data['year_industry_m_percentage'] = round(($year_industry_m_money / ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money)) * 100 , 0);
            }

            if($year_industry_c_r_money == 0){
                $data['year_industry_c_r_percentage'] = 0;
            }else{
                $data['year_industry_c_r_percentage'] = round(($year_industry_c_r_money / ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money)) * 100 , 0);
            }
            if($year_industry_m_t_money == 0){
                $data['year_industry_m_t_percentage'] = 0;
            }else{
                $data['year_industry_m_t_percentage'] = round(($year_industry_m_t_money / ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money)) * 100 , 0);
            }
            if($year_industry_e_money == 0){
                $data['year_industry_e_percentage'] = 0;
            }else{
                $data['year_industry_e_percentage'] = round(($year_industry_e_money / ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money)) * 100 , 0);
            }
            if($year_industry_f_r_money == 0){
                $data['year_industry_f_r_percentage'] = 0;
            }else{
                $data['year_industry_f_r_percentage'] = round(($year_industry_f_r_money / ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money)) * 100 , 0);
            }
            if($year_industry_s_money == 0){
                $data['year_industry_s_percentage'] = 0;
            }else{
                $data['year_industry_s_percentage'] = round(($year_industry_s_money / ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money)) * 100 , 0);
            }
            if($year_industry_m_h_money == 0){
                $data['year_industry_m_h_percentage'] = 0;
            }else{
                $data['year_industry_m_h_percentage'] = round(($year_industry_m_h_money / ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money)) * 100 , 0);
            }
            if($year_industry_r_money == 0){
                $data['year_industry_r_percentage'] = 0;
            }else{
                $data['year_industry_r_percentage'] = round(($year_industry_r_money / ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money)) * 100 , 0);
            }

            if($year_industry_i_p_money == 0){
                $data['year_industry_i_p_percentage'] = 0;
            }else{
                $data['year_industry_i_p_percentage'] = round(($year_industry_i_p_money / ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money)) * 100 , 0);
            }

            if($year_industry_w_i_money == 0){
                $data['year_industry_w_i_percentage'] = 0;
            }else{
                $data['year_industry_w_i_percentage'] = round(($year_industry_w_i_money / ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money)) * 100 , 0);
            }

            if($year_industry_p_c_money == 0){
                $data['year_industry_p_c_percentage'] = 0;
            }else{
                $data['year_industry_p_c_percentage'] = 100 - ($year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money);
            }
        }

        $data['total_year_count'] = $year_industry_m_count + $year_industry_c_r_count + $year_industry_m_t_count + $year_industry_e_count + $year_industry_f_r_count + $year_industry_s_count + $year_industry_m_h_count + $year_industry_r_count + $year_industry_i_p_count + $year_industry_w_i_count + $year_industry_p_c_count;
        $data['total_year_money'] = $year_industry_m_money + $year_industry_c_r_money + $year_industry_m_t_money + $year_industry_e_money + $year_industry_f_r_money + $year_industry_s_money + $year_industry_m_h_money + $year_industry_r_money + $year_industry_i_p_money + $year_industry_w_i_money + $year_industry_p_c_money;

        return $this->response->array($this->apiSuccess('获取成功', 200 , $data));

    }


    /**
     * @api {get} /designTarget/incomeStage 收入阶段
     * @apiVersion 1.0.0
     * @apiName designTarget incomeStage
     * @apiGroup designTarget
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "meta": {
    "message": "获取成功",
    "status_code": 200
    },
    "data": {
    "year_stage1_money": 226, //0-5w
    "year_stage1_count": 8,
    "year_stage2_money": 0, //5-10w
    "year_stage2_count": 0,
    "year_stage3_money": 0, //10-20w
    "year_stage3_count": 0,
    "year_stage4_money": 0, //20-30w
    "year_stage4_count": 0,
    "year_stage5_money": 0, //30-50w
    "year_stage5_count": 0,
    "year_stage6_money": 0, //50w以上
    "year_stage6_count": 0,
    "year_stage1_percentage": 100,
    "year_stage2_percentage": 0,
    "year_stage3_percentage": 0,
    "year_stage4_percentage": 0,
    "year_stage5_percentage": 0,
    "year_stage6_percentage": 0,
    "total_year_count": 8,
    "total_year_money": 226
    }
    }
     */
    public function incomeStage()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        //获取当年的0-5w
        $year_stage1_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->whereBetween('cost', [0 , 50000])
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_stage1_money = 0;
        $year_stage1_count = 0;
        foreach ($year_stage1_items as $year_stage1_item){
            $year_stage1_money += $year_stage1_item->cost;
            $year_stage1_count += 1;
        }
        $data['year_stage1_money'] = $year_stage1_money;
        $data['year_stage1_count'] = $year_stage1_count;
        //获取当年的5-10w
        $year_stage2_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('cost', '>' ,50000)
            ->where('cost', '<=',100000)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_stage2_money = 0;
        $year_stage2_count = 0;
        foreach ($year_stage2_items as $year_stage2_item){
            $year_stage2_money += $year_stage2_item->cost;
            $year_stage2_count += 1;
        }
        $data['year_stage2_money'] = $year_stage2_money;
        $data['year_stage2_count'] = $year_stage2_count;
        //获取当年的10-20w
        $year_stage3_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('cost', '>',100000)
            ->where('cost', '<=',200000)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_stage3_money = 0;
        $year_stage3_count = 0;
        foreach ($year_stage3_items as $year_stage3_item){
            $year_stage3_money += $year_stage3_item->cost;
            $year_stage3_count += 1;
        }
        $data['year_stage3_money'] = $year_stage3_money;
        $data['year_stage3_count'] = $year_stage3_count;
        //获取当年的20-30w
        $year_stage4_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('cost', '>',200000)
            ->where('cost', '<=',300000)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_stage4_money = 0;
        $year_stage4_count = 0;
        foreach ($year_stage4_items as $year_stage4_item){
            $year_stage4_money += $year_stage4_item->cost;
            $year_stage4_count += 1;
        }
        $data['year_stage4_money'] = $year_stage4_money;
        $data['year_stage4_count'] = $year_stage4_count;
        //获取当年的30-50w
        $year_stage5_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('cost', '>',300001)
            ->where('cost', '<=',500000)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_stage5_money = 0;
        $year_stage5_count = 0;
        foreach ($year_stage5_items as $year_stage5_item){
            $year_stage5_money += $year_stage5_item->cost;
            $year_stage5_count += 1;
        }
        $data['year_stage5_money'] = $year_stage5_money;
        $data['year_stage5_count'] = $year_stage5_count;
        //获取当年的50w以上
        $year_stage6_items =  DesignProject
            ::where('design_company_id', $design_company_id)
            ->where('pigeonhole', 1)
            ->where('cost', '>' , 50000)
            ->whereYear('created_at', date('Y'))
            ->get();
        $year_stage6_money = 0;
        $year_stage6_count = 0;
        foreach ($year_stage6_items as $year_stage6_item){
            $year_stage6_money += $year_stage6_item->cost;
            $year_stage6_count += 1;
        }
        $data['year_stage6_money'] = $year_stage6_money;
        $data['year_stage6_count'] = $year_stage6_count;


        if ($year_stage1_money + $year_stage2_money + $year_stage3_money + $year_stage4_money + $year_stage5_money + $year_stage6_money == 0){
            $data['year_stage1_percentage'] = 0;
            $data['year_stage2_percentage'] = 0;
            $data['year_stage3_percentage'] = 0;
            $data['year_stage4_percentage'] = 0;
            $data['year_stage5_percentage'] = 0;
            $data['year_stage6_percentage'] = 0;
        } else {
            if($year_stage1_money == 0){
                $data['year_stage1_percentage'] = 0;
            }else{
                $data['year_stage1_percentage'] = round(($year_stage1_money / ($year_stage1_money + $year_stage2_money + $year_stage3_money + $year_stage4_money + $year_stage5_money + $year_stage6_money)) * 100 , 0);
            }

            if($year_stage2_money == 0){
                $data['year_stage2_percentage'] = 0;
            }else{
                $data['year_stage2_percentage'] = round(($year_stage2_money / ($year_stage1_money + $year_stage2_money + $year_stage3_money + $year_stage4_money + $year_stage5_money + $year_stage6_money)) * 100 , 0);
            }
            if($year_stage3_money == 0){
                $data['year_stage3_percentage'] = 0;
            }else{
                $data['year_stage3_percentage'] = round(($year_stage3_money / ($year_stage1_money + $year_stage2_money + $year_stage3_money + $year_stage4_money + $year_stage5_money + $year_stage6_money)) * 100 , 0);
            }
            if($year_stage4_money == 0){
                $data['year_stage4_percentage'] = 0;
            }else{
                $data['year_stage4_percentage'] = round(($year_stage4_money / ($year_stage1_money + $year_stage2_money + $year_stage3_money + $year_stage4_money + $year_stage5_money + $year_stage6_money)) * 100 , 0);
            }
            if($year_stage5_money == 0){
                $data['year_stage5_percentage'] = 0;
            }else{
                $data['year_stage5_percentage'] = round(($year_stage5_money / ($year_stage1_money + $year_stage2_money + $year_stage3_money + $year_stage4_money + $year_stage5_money + $year_stage6_money)) * 100 , 0);
            }
            if($year_stage6_money == 0){
                $data['year_stage6_percentage'] = 0;
            }else{
                $data['year_stage6_percentage'] = 100 - ($year_stage1_money + $year_stage2_money + $year_stage3_money + $year_stage4_money + $year_stage5_money);
            }
        }

        $data['total_year_count'] = $year_stage1_count + $year_stage2_count + $year_stage3_count + $year_stage4_count + $year_stage5_count + $year_stage6_count;
        $data['total_year_money'] = $year_stage1_money + $year_stage2_money + $year_stage3_money + $year_stage4_money + $year_stage5_money + $year_stage6_money;

        return $this->response->array($this->apiSuccess('获取成功', 200 , $data));
    }

    /**
     * @api {get} /design/userPercentage 成员占比
     * @apiVersion 1.0.0
     * @apiName designTarget userPercentage
     * @apiGroup designTarget
     *
     * @apiParam {string} token
     */
    public function userPercentage()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        //用户成员
        $users =  User
            ::where('design_company_id', $design_company_id)
            ->where('company_role', 0)
            ->count();
        //管理员
        $admin_users =  User
            ::where('design_company_id', $design_company_id)
            ->where('company_role', 10)
            ->count();
        //超级管理员
        $super_admin_users =  User
            ::where('design_company_id', $design_company_id)
            ->where('company_role', 20)
            ->count();
        //所有成员
        $total_users = $users + $admin_users + $super_admin_users;

        if ($total_users == 0){
            $data['users_percentage'] = 0;
            $data['admin_users_percentage'] = 0;
            $data['super_admin_users_percentage'] = 0;
            $data['total_users'] = 0;
        } else {
            //成员
            if ($users == 0){
                $data['users'] = 0;
                $data['users_percentage'] = 0;
            } else {
                $data['users'] = $users;
                $data['users_percentage'] = round(($users / $total_users) * 100 , 0);
            }
            //管理员
            if ($admin_users == 0){
                $data['admin_users'] = 0;
                $data['admin_users_percentage'] = 0;
            } else {
                $data['admin_users'] = $admin_users;
                $data['admin_users_percentage'] = round(($admin_users / $total_users) * 100 , 0);
            }
            //超级管理员
            if ($super_admin_users == 0){
                $data['super_admin_users'] = 0;
                $data['super_admin_users_percentage'] = 0;
            } else {
                $data['super_admin_users'] = $super_admin_users;
                $data['super_admin_users_percentage'] = 100 - $data['users_percentage'] - $data['admin_users_percentage'];
            }
            $data['total_users'] = $total_users;

        }

        return $this->response->array($this->apiSuccess('获取成功', 200 , $data));

    }

    /**
     * @api {get} /design/positionPercentage 职位占比
     * @apiVersion 1.0.0
     * @apiName designTarget positionPercentage
     * @apiGroup designTarget
     *
     * @apiParam {string} token
     */
    public function positionPercentage()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        //用户总成员人数
        $total_user_count =  User::where('design_company_id', $design_company_id)->count();
        //按职位分组
        $users = DB::select("select count(id) as user_count , position as user_position from users where design_company_id = $design_company_id group by user_position");

        $positions = [];
        foreach ($users as $user){
            $v = [];
            $position = $user->user_position;
            $user_count = $user->user_count;
            $v['position'] = $position;
            $v['user_count'] = $user_count;
            if ($total_user_count == 0){
                $v['position_percentage'] = 0;
            } else {
                $v['position_percentage'] = round(($user_count / $total_user_count) * 100 ,0);
            }
            $positions[] = $v;
        }
        $data['positions'] = $positions;
        $data['total_user_count'] = $total_user_count;

        return $this->response->array($this->apiSuccess('获取成功', 200 , $data));

    }

}