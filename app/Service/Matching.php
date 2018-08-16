<?php

namespace App\Service;

use App\Models\Item;
use App\Models\Weight;
use App\Service\Statistics;
use App\Models\DemandCompany;
use App\Models\DesignItemModel;
use App\Events\ItemStatusEvent;
use App\Models\DesignStatistics;
use App\Models\DesignCompanyModel;
use Illuminate\Support\Facades\Log;

class Matching
{
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * 匹配执行
     * @params $uid    int    当前登录用户id
     */
    public function handle()
    {
        //设计类型
        $type = (int)$this->item->type;
        $design_types = json_decode($this->item->design_types, true);
        //产品设计
        $design = $this->productDesign($type, $design_types);
        //权重
        $weight = new Weight;
        $weight_data = $weight->getWeight();
        if (count($design) > 0) {
            //剔除已推荐的
            $ord_recommend = $this->item->ord_recommend;
            if (!empty($ord_recommend)) {
                $ord_recommend_arr = explode(',', $ord_recommend);
                $design = array_diff($design, $ord_recommend_arr);
            }
            //大于4个则会执行精准匹配
            if(count($design) > 4){
                //地区 第一步
                $area = $this->sortArea($design,$weight_data->area);
                //接单成功率
                $success_rate = $this->sortSuccessRate($area,$weight_data->success_rate);
                //评价分值
                $evaluate = $this->sortEvaluate($success_rate,$weight_data->score);
                //案例数量
                $sase = $this->sortSase($evaluate,$weight_data->case);
                //最近推荐时间
                //$time = $this->sortTime($sase,$weight_data->last_time);
                //接单均价
                //$design = $this->sortPrice($sase,$weight_data->average_price);
                //人工干预 最后步骤
                $intervent = $this->sortIntervene($sase);
                //最多取4个设计公司
                $design = array_slice($intervent, 0, 4);
            }
            //更新设计公司的最近推荐时间
            foreach ($design as $id) {
                $statistics = new Statistics;
                $statistics->recommendTime($id);
            }
            //判断是否匹配到设计公司
            if (empty($design)) {
                //临时处理 永不匹配失败
                //$this->failAction();
                //匹配失败
                $this->itemFail();
            } else {
                $recommend = implode(',', $design);
                $this->item->recommend = $recommend;
                //判断需求公司资料是否审核
                $demand_company = $this->item->user->demandCompany;
                if ($demand_company->verify_status == 1) {
                    $this->item->status = 3;   //已匹配设计公司
                } else {
                    $this->item->status = 2;  //2.人工干预
                }
                //保存匹配信息,更改状态
                $this->item->save();
                // 特殊用户处理
                $this->PSTestAction();
                //触发项目状态变更事件
                event(new ItemStatusEvent($this->item));
            }
        } else {
            //匹配失败处理
            $this->itemFail();
        }
        //注销变量
        unset($design_type, $field, $design_id_arr, $design, $recommend);
    }

    //匹配失败
    protected function itemFail()
    {
        if (config('constant.item_recommend_lose')) {
            $this->item->status = 2;  //等待后台人工干预
        } else {
            $this->item->status = -2;  //匹配失败
        }
        $this->item->save();
        // 特殊用户处理
        $this->PSTestAction();
        //触发项目状态变更事件
        event(new ItemStatusEvent($this->item));
    }

    // 匹配失败后随机匹配2个设计公司
    protected function failAction()
    {
        $design_id_arr = DesignCompanyModel::select('id')
            ->where(['status' => 1, 'verify_status' => 1])
            ->get()
            ->pluck('id')->all();
        $key_arr = array_rand($design_id_arr, 2);
        $design_id_arr_rand = [];
        foreach ($key_arr as $value) {
            $design_id_arr_rand[] = $design_id_arr[$value];
        }
        $recommend = implode(',', $design_id_arr_rand);
        $this->item->recommend = $recommend;

        //判断需求公司资料是否审核
        $demand_company = $this->item->user->demandCompany;
        if ($demand_company->verify_status == 1) {
            $this->item->status = 3;   //已匹配设计公司
        } else {
            $this->item->status = 2;  //2.人工干预
        }
        $this->item->save();
        // 特殊用户处理
        $this->PSTestAction();
        //触发项目状态变更事件
        event(new ItemStatusEvent($this->item));
    }

    // 为测试账号默认匹配固定设计公司
    protected function PSTestAction()
    {
        // 特定需求公司user_id
        $user_id = 66;

        // 设计公司ID
        $design_id = 32;

        if ((int)$user_id === (int)$this->item->user_id) {
            $this->item->recommend = $this->item->recommend . "," . $design_id;
            $this->item->save();
        }
    }

    /**
     * 产品设计推荐 设计公司ID
     *
     * @param $design_type
     * @return array
     */
    protected function productDesign($type, array $design_types)
    {
        //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
        $max = $this->cost($this->item->design_cost);
        $design_id_arr = [];
        foreach ($design_types as $design_type) {
            //获取符合设计类型和设计费用的设计公司ID数组
            $design_id_arr = DesignItemModel::select('user_id')
                ->where('type', $type)
                ->where('design_type', $design_type)
                ->where('min_price', '<=', $max)
                ->get()
                ->pluck('user_id')
                ->all();
        }
        //Log::info($design_id_arr);
        //获取擅长的设计公司ID数组
        $design = DesignCompanyModel::select(['id', 'user_id'])
            ->where(['status' => 1, 'verify_status' => 1, 'is_test_data' => 0]);

        $design_user_id_arr = $design->whereIn('user_id', $design_id_arr)
            ->orderBy('score', 'desc')
            ->get()
            ->pluck('id')
            ->all();
        return $design_user_id_arr;
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

    //地区分值
    public function sortArea($design=[],$area=0)
    {
        $design_company = new DesignCompanyModel;
        //总的排序内容
        $data = [];
        //循环处理分设计公司的分值
        foreach ($design as $key => $item) {
            $sore['id'] = $item;
            $sore['sore'] = 0;
            //权重必须大于0
            if($area > 0){
                //查询公司详情
                $company = $design_company->where('id',$item)->first();
                $score = 0;
                if(!empty($company)){
                    if($this->item->item_province == $company->province){
                        //省份占比重30
                        $score = 30;
                    }
                    if($company->city == $this->item->item_city && $company->province == $this->item->item_province){
                        //省份和城市都存在占比重100
                        $score = 100;
                    }
                    $weight = $area / 100;
                    if($score <= 0){
                        $score = 0;
                    }else{
                        $score = $score * $weight;
                    }
                }
                $sore['sore'] = $score;
            }
            //单个
            $data[$key] = $sore;
        }
        $id = array_column($data, 'id');
        array_multisort($id,SORT_ASC,$data);
        return $data;
    }

    //推荐和接单次数分值
    public function sortSuccessRate($design=[],$success_rate=0)
    {
        //总的排序内容
        $data = [];
        //未推荐过的
        $recommend = [];
        //权重必须大于0
        if($success_rate > 0){
            //循环处理分设计公司的分值
            foreach ($design as $key => $item) {
                $id = $item['id'];
                $sore['id'] = $id;
                $sore['sore'] = 0;
                $res = DesignStatistics::select('recommend_count','cooperation_count')->where(['design_company_id'=>$id])->first();
                if(!empty($res)){
                    //推荐次数
                    if($res->recommend_count == 0){
                        $recommend[] = $key;
                    }
                    //接单次数
                    $score = (int)$res->cooperation_count / (int)$res->recommend_count;
                    if($score == 0){
                        //推荐过,未接单的
                        $sore['sore'] = 0;
                    }else{
                        //正常的
                        $sore['sore'] = $score;
                    }
                    $data[$key] = $sore;
                }
            }
            $sore = array_column($data, 'sore');
            array_multisort($sore,SORT_DESC,$data);
            $num = 100;
            $success_rate = $success_rate / 100;
            foreach ($data as $k => $v){
                if($num <= 0){
                    $data[$k]['sore'] = 0;
                }else{
                    $weight = $num * $success_rate;
                    $data[$k]['sore'] = $weight;
                }
                $num--;
            }
            $id = array_column($data, 'id');
            array_multisort($id,SORT_ASC,$data);
            foreach ($data as $key => $val){
                $design[$key]['sore'] += $val['sore'];
            }
        }
        return $design;
    }

    //评价
    public function sortEvaluate($design=[],$weight_score=0)
    {
        //总的排序内容
        $data = [];
        if($weight_score > 0){
            //循环处理分设计公司的分值
            foreach ($design as $key => $item) {
                $id = $item['id'];
                $sore['id'] = $id;
                $sore['sore'] = 0;
                $res = DesignStatistics::select('score')->where(['design_company_id'=>$id])->first();
                if(!empty($res)){
                    $sore['sore'] = (int)$res->score;
                }
                $data[$key] = $sore;
            }
            $sore = array_column($data, 'sore');
            array_multisort($sore,SORT_DESC,$data);
            $num = 100;
            $weight_score = $weight_score / 100;
            foreach ($data as $k => $v){
                if($num <= 0){
                    $data[$k]['sore'] = 0;
                }else{
                    $weight = $num * $weight_score;
                    $data[$k]['sore'] = $weight;
                }
                $num--;
            }
            $id = array_column($data, 'id');
            array_multisort($id,SORT_ASC,$data);
            foreach ($data as $key => $val){
                $design[$key]['sore'] += $val['sore'];
            }
        }
        return $design;
    }

    //案例数量
    public function sortSase($design=[],$case=0)
    {
        //总的排序内容
        $data = [];
        if($case > 0){
            //循环处理分设计公司的分值
            foreach ($design as $key => $item) {
                $id = $item['id'];
                $sore['id'] = $id;
                $sore['sore'] = 0;
                $res = DesignStatistics::select('case')->where(['design_company_id'=>$id])->first();
                if(!empty($res)){
                    $sore['sore'] = (int)$res->case;
                }
                $data[$key] = $sore;
            }
            $sore = array_column($data, 'sore');
            array_multisort($sore,SORT_DESC,$data);
            $num = 100;
            $case = $case / 100;
            foreach ($data as $k => $v){
                if($num <= 0){
                    $data[$k]['sore'] = 0;
                }else{
                    $weight = $num * $case;
                    $data[$k]['sore'] = $weight;
                }
                $num--;
            }
            //按id排序并加案例数量分值
            $id = array_column($data, 'id');
            array_multisort($id,SORT_ASC,$data);
            foreach ($data as $key => $val){
                $design[$key]['sore'] += $val['sore'];
            }
        }
        return $design;
    }

    //最近推荐时间
    public function sortTime($design=[],$time=0)
    {
        //总的排序内容
        $data = [];
        if($time > 0){
            //循环处理分设计公司的分值
            foreach ($design as $key => $item) {
                $id = $item['id'];
                $sore['id'] = $id;
                $sore['sore'] = 0;
                $res = DesignStatistics::select('last_time')->where(['design_company_id'=>$id])->first();
                if(!empty($res)){
                    $sore['sore'] = $res->last_time;
                }
                $data[$key] = $sore;
            }
            //从远的开始算
            $sore = array_column($data, 'sore');
            array_multisort($sore,SORT_ASC,$data);
            dd($data);
            $num = 100;
            $time = $time / 100;
            foreach ($data as $k => $v){
                if($num <= 0){
                    $data[$k]['sore'] = 0;
                }else{
                    $weight = $num * $time;
                    $data[$k]['sore'] = $weight;
                }
                $num--;
            }
            //按照id升序排序
            $id = array_column($data, 'id');
            array_multisort($id,SORT_ASC,$data);
            //循环增加分值
            foreach ($data as $key => $val){
                $design[$key]['sore'] += $val['sore'];
            }
        }
        return $design;
    }

    //接单均价
    public function sortPrice($design=[],$average_price=0)
    {
        //总的排序内容
        $data = [];
        if($average_price > 0){
            //循环处理分设计公司的分值
            foreach ($design as $key => $item) {
                $id = $item['id'];
                $sore['id'] = $id;
                $sore['sore'] = 0;
                $res = DesignStatistics::select('average_price')->where(['design_company_id'=>$id])->first();
                if(!empty($res)){
                    $abs = $average_price - $res->average_price;
                    $sore['sore'] = abs($abs);
                    return $sore;
                }
                $data[$key] = $sore;
            }
            $sore = array_column($data, 'sore');
            array_multisort($sore,SORT_ASC,$data);
            return $data;
            $num = 100;
            $average_price = $average_price / 100;
            foreach ($data as $k => $v){
                if($num <= 0){
                    $data[$k]['sore'] = 0;
                }else{
                    $weight = $num * $average_price;
                    $data[$k]['sore'] = $weight;
                }
                $num--;
            }
            $id = array_column($data, 'id');
            array_multisort($id,SORT_ASC,$data);
            foreach ($data as $key => $val){
                $design[$key]['sore'] += $val['sore'];
            }
        }
        return $design;
    }

    //人工干预 最后处理步骤
    public function sortIntervene($design=[])
    {
        //循环处理分设计公司的分值
        foreach ($design as $key => $item) {
            $res = DesignStatistics::select('intervene')->where(['design_company_id'=>$item['id']])->first();
            if(!empty($res)){
                $design[$key]['sore'] += (int)$res->intervene;
            }
        }
        //按分数降序排序
        $sore = array_column($design, 'sore');
        array_multisort($sore,SORT_DESC,$design);
        //把排序后的id取出来
        $design = array_column($design, 'id');
        return $design;
    }

}