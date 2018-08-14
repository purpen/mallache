<?php
namespace App\Service;
use App\Models\Contract;
use App\Models\DesignStatistics;
use App\Models\DesignCaseModel;
use App\Models\Item;
use App\Models\Evaluate;
/**
 * Class Statistics 设计公司信息统计
 * @package App\Service
 * @author 王松
 */
class Statistics
{
    /**
     * 关联模型到数据表
     *
     * @var string
     */
    protected $table = 'design_statistics';

    /**
     * contractAveragePrice    更新所有设计公司平均价格
     *
     * @author 王松
     * @params array      设计公司id    [1,2,3]
     * @return boolean    true|false   1|0
     */
    public function contractAveragePrice($data = [])
    {
        $contract = new Contract;
        $designstatistics = new DesignStatistics;
        foreach ($data as $key => $val){
            //查询公司所有合同
            $c_data = $contract->select('total')->where(['design_company_id'=>$val])->get();
            if(!empty($c_data)){
                $c_data = json_decode($c_data,1);
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
                $data = $designstatistics->select('id')->where(['design_company_id'=>$val])->first();
                if(empty($data)){
                    //新增公司记录
                    $res = $designstatistics->insert(['average_price'=>$price,'cooperation_count'=>$num,'design_company_id'=>$val]);
                    if(empty($res)){
                        return false;
                    }
                }else{
                    $data = json_decode($data,1);
                    //更新均价
                    $res = $designstatistics->where(['id'=>$data['id']])->update(['average_price'=>$price,'cooperation_count'=>$num]);
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
            $designstatistics = new DesignStatistics;
            $data = $designstatistics->select('id')->where(['design_company_id'=>$val])->first();
            if(empty($data)){
                //新增公司记录
                $res = $designstatistics->insert(['case'=>$num,'design_company_id'=>$val]);
                if(empty($res)){
                    return false;
                }
            }else{
                $data = json_decode($data,1);
                //更新案例数量
                $res = $designstatistics->where(['id'=>$data['id']])->update(['case'=>$num]);
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
            $item = new Item;
            $res = $item->select('recommend','ord_recommend')->where('recommend','like',"%$val%")->orWhere('ord_recommend','like',"%$val%")->get();
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
                $designstatistics = new DesignStatistics;
                $statistics = $designstatistics->select('id')->where(['design_company_id'=>$val])->first();
                if(empty($statistics)){
                    //新增公司记录
                    $res = $designstatistics->insert(['recommend_count'=>$num,'design_company_id'=>$val]);
                    if(empty($res)){
                        return false;
                    }
                }else{
                    $statistics = $statistics->toArray();
                    //更新推荐次数
                    $res = $designstatistics->where(['id'=>$statistics['id']])->update(['recommend_count'=>$num]);
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
        $designstatistics = new DesignStatistics;
        $data = $designstatistics->select('id','average_price','cooperation_count')->where(['design_company_id'=>$id])->first();
        if(!empty($data)){
            $data = json_decode($data,1);
            //总价格
            $prices = $data['average_price'] * $data['cooperation_count'];
            $prices = $prices + $price;
            $cooperation_count = $data['cooperation_count'] + 1;
            $average_price = $prices / $cooperation_count;
            $average_price = sprintf("%.2f", $average_price);
            $list = ['average_price'=>$average_price,'cooperation_count'=>$cooperation_count];
            //更新平均价格与合作次数
            $res = $designstatistics->where(['id'=>$data['id']])->update($list);
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
        $designstatistics = new DesignStatistics;
        foreach($design_company_id as $v){
            //查询公司信息是否存在
            $data = $designstatistics->select('id')->where(['design_company_id'=>$v])->first();
            if(empty($data)){
                //新增一条公司信息
                $res = $designstatistics->insert(['last_time'=>$last_time,'design_company_id'=>$v]);
                if(empty($res)){
                    return false;
                }
            }else{
                $data = json_decode($data,1);
                //更新最近接单时间
                $res = $designstatistics->where(['id'=>$data['id']])->update(['last_time'=>$last_time]);
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
            $data = $designstatistics->select('id','case')->where(['design_company_id'=>$v])->first();
            if(empty($data)){
                //新增一条设计公司信息
                $res = $designstatistics->insert(['case'=>1,'design_company_id'=>$v]);
                if(empty($res)){
                    return false;
                }
            }else{
                $data = json_decode($data,1);
                $case = (int)$data['case'] + 1;
                //更新设计公司案例数量
                $res = $designstatistics->where(['id'=>$data['id']])->update(['case'=>$case]);
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
        $data = DesignStatistics::select('id','cooperation_count','recommend_count')->where(['design_company_id'=>$id])->first();
        if(!empty($data)){
            $data = $data->toArray();
            $success_rate = $data['cooperation_count'] / $data['recommend_count'];
            $success_rate = sprintf("%.4f", $success_rate);
            if($success_rate > 0){
                //更新成功率
                $res = DesignStatistics::where(['id'=>$data['id']])->update(['success_rate'=>$success_rate]);
                if(!empty($res)){
                    return true;
                }
                return false;
            }
        }
        return false;
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
        $data = DesignStatistics::where(['design_company_id'=>$id])->first();
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
        $evaluate = new Evaluate;
        foreach ($data as $key => $id){
            $score_data = $evaluate->where('design_company_id',$id)->get();
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

}