<?php
namespace App\Service;

use App\Models\Item;
use App\Models\Weight;
use App\Models\Contract;
use App\Models\Evaluate;
use App\Models\DesignCaseModel;
use App\Models\DesignItemModel;
use App\Models\DesignStatistics;
use Illuminate\Support\Facades\DB;
use App\Models\DesignCompanyModel;
use Illuminate\Support\Facades\Log;

/**
 * Class Statistics 设计公司信息统计
 * @package App\Service
 * @author 王松
 */
class Statistics
{
    /**
     * contractAveragePrice    更新所有设计公司平均价格
     *
     * @author 王松
     * @params array      设计公司id    [1,2,3]
     * @return boolean    true|false   1|0
     */
    public function contractAveragePrice($data = [])
    {
        foreach ($data as $key => $val){
            //查询公司所有合同
            $c_data = Contract::select('total')->where(['design_company_id'=>$val])->get();
            if(!empty($c_data)){
                $num = count($c_data);
                $total = 0;
                foreach($c_data as $k => $v){
                    $total += (int)$v['total'];
                }
                if($total <= 0){
                    continue;
                }
                if($num <= 0){
                    continue;
                }
                $price = $total / $num;
                $price = sprintf("%.2f", $price);
                $data = DesignStatistics::select('id','average_price','cooperation_count','design_company_id')->where(['design_company_id'=>$val])->first();
                if(empty($data)){
                    //新增公司记录
                    $res = DesignStatistics::insert(['average_price'=>$price,'cooperation_count'=>$num,'design_company_id'=>$val]);
                    if(empty($res)){
                        return false;
                    }
                }else{
                    $data->average_price = $price;
                    $data->cooperation_count = $num;
                    $res = $data->save();
                    //更新均价
                    if(empty($res)){
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * companyDesignCase    更新设计公司案例数量
     *
     * @author 王松
     * @params array      设计公司id    [1,2,3]
     * @return boolean    true|false   1|0
     */
    public function companyDesignCase($data = [])
    {
        foreach ($data as $key => $val){
            //查询公司案例数量
            $num = DesignCaseModel::where(['design_company_id'=>$val])->count();
            if($num <= 0){
                continue;
            }
            $data = DesignStatistics::select('id','case')->where(['design_company_id'=>$val])->first();
            if(empty($data)){
                //新增公司记录
                $res = DesignStatistics::insert(['case'=>$num,'design_company_id'=>$val]);
                if(empty($res)){
                    return false;
                }
            }else{
                //更新案例数量
                $data->case = $num;
                $res = $data->save();
                if(empty($res)){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * companyContract    更新设计公司推荐次数
     *
     * @author 王松
     * @params array      设计公司id    [1,2,3]
     * @return boolean    true|false   1|0
     */
    public function saveRecommend($data = [])
    {
        foreach ($data as $key => $val){
            //查询公司案例数量
            $res = Item::select('recommend','ord_recommend')->where('recommend','like',"%$val%")->orWhere('ord_recommend','like',"%$val%")->get();
            if(!empty($res)){
                $res = $res->toArray();
                $num = 0;
                foreach ($res as $value) {
                    $recommend = !empty($value['recommend']) ? explode(',',$value['recommend']) : [];
                    $ord_recommend = !empty($value['ord_recommend']) ? explode(',',$value['ord_recommend']) : [];
                    $arr = array_merge($recommend,$ord_recommend);
                    foreach ($arr as $v){
                        if($val == $v){
                            $num++;
                        }
                    }
                }
                if($num <= 0){
                    continue;
                }
                $statistics = DesignStatistics::select('id','recommend_count')->where(['design_company_id'=>$val])->first();
                if(empty($statistics)){
                    //新增公司记录
                    $res = DesignStatistics::insert(['recommend_count'=>$num,'design_company_id'=>$val]);
                    if(empty($res)){
                        return false;
                    }
                }else{
                    //更新推荐次数
                    $statistics->recommend_count = $num;
                    $res = $statistics->save();
                    if(empty($res)){
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * successRate    更新设计公司成功率
     *
     * @author 王松
     * @params array      设计公司id    [1,2,3]
     * @return boolean    true|false   1|0
     */
    public function successRate($data = [])
    {
        foreach ($data as $key => $val){
            $this->saveSuccessRate($val);
        }
        return false;
    }

    /**
     * saveAveragePrice    设计公司平均价格(每来一单更新一次)
     *
     * @author 王松
     * @params $id      设计公司id
     * @params $price   接单价格
     * @return boolean  true|false   1|0
     */
    public function saveAveragePrice($id,$price)
    {
        $data = DesignStatistics::select('id','average_price','cooperation_count')->where(['design_company_id'=>$id])->first();
        if(!empty($data)){
            //总价格
            $prices = $data->average_price * $data->cooperation_count;
            $prices = $prices + $price;
            $cooperation_count = $data->cooperation_count + 1;
            $average_price = $prices / $cooperation_count;
            $average_price = sprintf("%.2f", $average_price);
            $data->average_price = $average_price;
            $data->cooperation_count = $cooperation_count;
            $res = $data->save();
            if(empty($res)){
                return false;
            }
        }
        //更新成功率
        $this->saveSuccessRate($id);
        return true;
    }

    /**
     * saveSingleTime        更新设计公司最近接单时间
     *
     * @author 王松
     * @params $design_company_id    array   设计公司id    [1,2,3]
     * @params $last_time            int     时间戳
     * @return boolean    true|false
     */
    public function saveSingleTime($design_company_id,$last_time)
    {
        foreach($design_company_id as $v){
            //查询公司信息是否存在
            $data = DesignStatistics::select('id','last_time')->where(['design_company_id'=>$v])->first();
            if(empty($data)){
                $designstatistics = new DesignStatistics;
                //新增一条公司信息
                $res = $designstatistics->insert(['last_time'=>$last_time,'design_company_id'=>$v]);
                if(empty($res)){
                    return false;
                }
            }else{
                //更新最近接单时间
                $data->last_time = $last_time;
                $res = $data->save();
                if(empty($res)){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * saveDesignCaseNum        更新设计公司案例数量
     *
     * @author 王松
     * @params array        设计公司id    [1,2,3]
     * @return boolean      true|false   1|0
     */
    public function saveDesignCaseNum($params)
    {
        if(!is_array($params)){
            return false;
        }
        $designstatistics = new DesignStatistics;
        foreach($params as $v){
            //查询设计公司信息是否存在
            $data = DesignStatistics::select('id','case')->where(['design_company_id'=>$v])->first();
            if(empty($data)){
                //新增一条设计公司信息
                $res = $designstatistics->insert(['case'=>1,'design_company_id'=>$v]);
                if(empty($res)){
                    return false;
                }
            }else{
                $data->case = (int)$data->case + 1;
                //更新设计公司案例数量
                $res = $data->save();
                if(empty($res)){
                    return false;
                }
                //更新成功率
                $this->saveSuccessRate($v);
            }
        }
        return true;
    }

    /**
     * saveRecommendedTimes        更新设计公司推荐次数
     *
     * @author 王松
     * @params array        设计公司id    [1,2,3]
     * @return boolean      true|false   1|0
     */
    public function saveRecommendedTimes($params)
    {
        if(!is_array($params)){
            return false;
        }
        $designstatistics = new DesignStatistics;
        foreach($params as $v){
            $data = $designstatistics->saveRecommendedTimes((int)$v);
            if(empty($data)){
                return false;
            }
        }
        return true;
    }

    /**
     * saveSuccessRate       更新成功率
     *
     * @author 王松
     * @params $id    int   设计公司id    [1,2,3]
     * @return boolean      true|false   1|0
     */
    public function saveSuccessRate($id)
    {
        $data = DesignStatistics::select('id','cooperation_count','recommend_count','success_rate')->where(['design_company_id'=>$id])->first();
        if(!empty($data)){
            //接单数量和推荐数量大于0才会更新
            if($data->cooperation_count > 0 && $data->recommend_count > 0){
                //根据接单次数和推荐次数计算接单成功率
                $success_rate = $data->cooperation_count / $data->recommend_count;
                $success_rate = sprintf("%.4f", $success_rate);
                if($success_rate > 0){
                    //更新成功率
                    $data->success_rate = $success_rate;
                    $res = $data->save();
                    if(empty($res)){
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * saveDesignInfo       新增设计公司信息
     *
     * @author 王松
     * @params $id    int   设计公司id    [1,2,3]
     * @return boolean      true|false   1|0
     */
    public function saveDesignInfo($id)
    {
        $statistics = new DesignStatistics;
        return $statistics->saveDesignInfo($id);
    }

    /**
     * recommendTime       更新最近推荐时间
     *
     * @author 王松
     * @params $id    int   设计公司id
     * @return boolean      true|false
     */
    public function recommendTime($id)
    {
        $data = DesignStatistics::select('id','recommend_time')->where(['design_company_id'=>$id])->first();
        if(!empty($data)){
            $data->recommend_time = time();
            return $data->save();
        }else{
            //新增一条设计公司信息
            $res = DesignStatistics::insert(['recommend_time'=>time(),'design_company_id'=>$id]);
            if($res > 0){
                return true;
            }
        }
        return false;
    }

    /**
     * evaluationScore       评价平均分
     *
     * @author 王松
     * @params $data      int    设计公司id    [1,2,3]
     * @return boolean    true|false    1|0
     */
    public function evaluationScore($data)
    {
        foreach ($data as $key => $id){
            $score_data = Evaluate::where('design_company_id',$id)->get();
            $average = 0;
            if(!empty($score_data)){
                $num = 0;
                $score = 0;
                foreach ($score_data as $val){
                    $num++;
                    $score += (int)$val->score;
                }
                if($score == 0 || $num == 0){
                    $average = 0;
                }else{
                    $average = $score / $num;
                }
            }
            if($average > 0){
                $data = DesignStatistics::where(['design_company_id'=>$id])->first();
                if(!empty($data)){
                    $data->score = $average;
                    $res = $data->save();
                    if(empty($res)){
                        return false;
                    }
                }else{
                    //新增一条设计公司信息
                    $res = DesignStatistics::insert(['score'=>$average,'design_company_id'=>$id]);
                    if(empty($res)){
                        return false;
                    }
                }
            }
        }
        return true;
    }
    /**
     * 测试设计公司匹配
     *
     * @param $type 设计类型
     * @param $design_types 设计类别
     * @return array
     */
    public function testMatching($params)
    {
        //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
        $max = $this->cost($params['design_cost']);
        $design_id_arr = [];
        foreach ($params['design_types'] as $design_type) {
            //获取符合设计类型和设计费用的设计公司ID数组
            $arr = DesignItemModel::select('user_id')
                ->where('type', $params['type'])
                ->where('design_type', $design_type)
                ->where('min_price', '<=', $max)
                ->get()
                ->pluck('user_id')
                ->all();
            $design_id_arr = array_merge($arr,$design_id_arr);
        }
        $design_id_arr = array_unique($design_id_arr);
        //获取擅长的设计公司ID数组
        $design_data = DesignCompanyModel::select(['id', 'user_id'])
            ->where(['status' => 1, 'verify_status' => 1, 'is_test_data' => 0]);

        $matching = new Matching(new Item);
        $design = $design_data->whereIn('user_id', $design_id_arr)
            ->orderBy('score', 'desc')
            ->get()
            ->pluck('id')
            ->all();
        //权重
        $weight = new Weight;
        $weight_data = $weight->getWeight();
        if(empty($weight_data)){
            return [];
        }
        $designCompanys = [];
        //只要有设计公司就可以精准匹配
        if (!empty($design)) {
            //地区 第一步
            $areas = [];
            foreach ($design as $val){
                $score = 0;
                if($weight_data->area > 0){
                    //查询公司详情
                    $company = DesignCompanyModel::where('id',$val)->first();
                    if(!empty($company)){
                        if($params['province'] == $company->province){
                            //省份占比重30
                            $score = 30;
                        }
                        if($company->city == $params['city'] && $company->province == $params['province']){
                            //省份和城市都存在占比重100
                            $score = 100;
                        }
                        $area = $weight_data->area / 100;
                        if($score <= 0){
                            $score = 0;
                        }else{
                            $score = $score * $area;
                        }
                    }
                    $areas[$val] = $score;
                }else{
                    $areas[$val] = 0;
                }
            }
            //接单成功率
            $success_rate = $matching->sortSuccessRate($areas,$weight_data->success_rate);
            //评价分值
            $evaluate = $matching->sortEvaluate($success_rate,$weight_data->score);
            //案例数量
            $sase = $matching->sortSase($evaluate,$weight_data->case);
            //最近推荐时间
            //$time = $matching->sortTime($sase,$weight_data->last_time);
            //接单均价
            //$design = $matching->sortPrice($time,$weight_data->average_price);
            //人工干预
            $intervent = $matching->sortIntervene($sase);
            //取出id
            $data = array_keys($intervent);
            //测试可以给20个设计公司
            $design = array_slice($data, 0, 20);
            $designCompanys = DesignCompanyModel::query();
            $res = $designCompanys->select('id','company_name','province','city','address','contact_name','phone','company_abbreviation')
                                  ->orderByRaw(DB::raw("FIND_IN_SET(id, '" . implode(',', $design) . "'" . ')'))
                                  ->whereIn('id', $design)
                                  ->paginate(20);
            if(!empty($res)){
                foreach ($res as $designCompany){
                    $designCompany->design_statistic = $designCompany->designStatistic;
                }
            }
            return $res;
        } else {
            //匹配失败
            return $designCompanys;
        }
    }

    /**
     * 设计费用转换
     *
     * @param $design_cost
     * @return int
     */
    protected function cost($design_cost)
    {
        //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
        $max = 10000;
        switch ($design_cost) {
            case 1:
                $max = 50000;
                break;
            case 2:
                $max = 100000;
                break;
            case 3:
                $max = 200000;
                break;
            case 4:
                $max = 300000;
                break;
            case 5:
                $max = 500000;
                break;
            case 6:
                $max = 500000;
                break;
        }
        return $max;
    }

}