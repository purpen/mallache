<?php

namespace App\Service;
use App\Events\ItemStatusEvent;
use App\Models\DesignCaseModel;
use App\Models\DesignCompanyModel;
use App\Models\DesignItemModel;
use App\Models\Item;
use Illuminate\Support\Facades\Log;
use Lib\AiBaiDu\Api;
use App\Models\Weight;
use App\Models\DesignStatistics;
use App\Models\DemandCompany;
class Matching
{
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * 匹配执行
     */
    public function handle($uid)
    {
        //设计类型
        $type = (int)$this->item->type;
        $design_types = json_decode($this->item->design_types, true);
        //产品设计
        $design = $this->productDesign($type, $design_types);
        //权重
        $weight = new Weight;
        $weight_data = $weight->getWeight();
        //地区
        $area = $this->sortArea($design,$uid,$weight_data->area);
        //接单成功率
        $success_rate = $this->sortSuccessRate($area,$weight_data->success_rate);
        //评价分值
        $evaluate = $this->sortEvaluate($success_rate,$weight_data->score);
        //案例数量
        $sase = $this->sortSase($evaluate,$weight_data->case);
        /*
         //最近推荐时间
        $time = $this->sortTime($sase,$weight_data->last_time);
        */
        //接单均价
        //$design = $this->sortPrice($sase,$weight_data->average_price);
        if (count($design) > 0) {
            //剔除已推荐的
            $ord_recommend = $this->item->ord_recommend;
            if (!empty($ord_recommend)) {
                $ord_recommend_arr = explode(',', $ord_recommend);
                $design = array_diff($design, $ord_recommend_arr);
            }
            $design = array_slice($design, 0, 4);
            foreach ($design as $id){
                //更新最近推荐时间
                Statistics::recommendTime($id);
            }
            //判断是否匹配到设计公司
            if (empty($design)) {
                 //临时处理 永不匹配失败
                 //$this->failAction();

                 //原处理
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

                $this->item->save();

                // 特殊用户处理
                $this->PSTestAction();

                //触发项目状态变更事件
                event(new ItemStatusEvent($this->item));
            }

        } else {
            // 临时处理 永不匹配失败
            // $this->failAction();

            // 原处理
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
        $design_id_arr = DesignCompanyModel::select('id')->where(['status' => 1, 'verify_status' => 1])
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

        //所属领域
        $field = $this->item->productDesign->field;
//        //周期
//        $cycle = $this->item->cycle;
//        //项目公司地点
//        $item_info = $this->item->itemInfo();
//        $province = $item_info['province'];
//        $city = $item_info['city'];

        /*$province = 210000;
        $city = 210600;
        //权重
        $weight = new Weight;
        $weight_data = $weight->getWeight();
        $arr = [];*/
        foreach ($design_types as $design_type) {
            //获取符合设计类型和设计费用的设计公司ID数组
            $design_id_arr = DesignItemModel::select('user_id')
                ->where('type', $type)
                ->where('design_type', $design_type)
                ->where('min_price', '<=', $max)
                ->get()
                ->pluck('user_id')->all();

            if (empty($arr)) {
                $arr = $design_id_arr;
            } else {
                $arr = array_intersect($arr, $design_id_arr);
            }
        }
        $design_id_arr = $arr;
        /*$design_company = new DesignCompanyModel;
        $design_statistics = new DesignStatistics;
        //没有推荐过的
        $rate = [];
        //总的排序内容
        $data = array();
        //成功总数
        $success_score = 0;
        //成功总数
        $area_num = 0;
        //循环处理分设计公司的分值
        foreach ($design_id_arr as $key => $item) {
            $sore['id'] = $item;
            $sore['sore'] = 0;
            //查询公司详情
            $company = $design_company->where('id',$item)->first();
            $statistics = $design_statistics->where('design_company_id',$item)->first();
            if(empty($company)){
                //地区分值计算
                $sore['sore'] += $this->sortArea($company->province,$company->city,$weight_data->area,$province,$city);
            }
            //公司统计信息
            if(empty($statistics)){

            }else{
                //推荐和接单次数分值
                $sort_success_rate = $this->sortSuccessRate($statistics->recommend_count,$statistics->cooperation_count,$weight_data->success_rate);
                if($sort_success_rate == 0){
                    //没有推荐过的
                    $rate[] = $key;
                }elseif($sort_success_rate != -1){
                    //成功率总分
                    $success_score += $sort_success_rate;
                    $area_num ++;
                    $sore['sore'] += $sort_success_rate;
                }
            }
            //单个
            $data[$key] = $sore;
        }
        dd($data);
        if($success_score > 0){
            //成功率平均分值
            $average_area = $success_score / $area_num;
            //只要平均分大于0,就把没有推荐过的加上平均分
            if($rate > 0){
                foreach ($rate as $v){
                    $data[$v]['sore'] += $average_area;
                }
            }
        }
        //排序分值
        $sore = array_column($data, 'sore');
        array_multisort($sore,SORT_DESC,$data);
        $id = array_slice(array_column($data, 'id'),0,4);
        dd($id);*/

//Log::info($design_id_arr);
        //获取擅长的设计公司ID数组
        $design = DesignCompanyModel::select(['id', 'user_id'])
            ->where(['status' => 1, 'verify_status' => 1, 'is_test_data' => 0]);

//        if($province && $province != -1){
//            $design->where('province', $province)
//                ->where('city', $city);
//        }
        $design_user_id_arr = $design->whereIn('user_id', $design_id_arr)
//            ->whereRaw('find_in_set(' . $field . ', good_field)')  // 擅长领域
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

    // 计算需求和设计公司案例的匹配程度，重新排序
    public function againSort($design_company_id_arr)
    {
//        $ai_api = new Api();

        $item_info = $this->item->itemInfo();
        $text_1 = $item_info['name'];

        $data = [];
        foreach ($design_company_id_arr as $id) {
            $score = 0; // 设计公司案例最高匹配度

            $design = DesignCompanyModel::find($id);

            $design_case = DesignCaseModel::query()
                ->select('title', 'profile')
                ->where('design_company_id', $id)->get();

            foreach ($design_case as $case) {
                $text_2 = $case->title;

                /*$result = $ai_api->nlp($text_1, $text_2); // 调用百度ai接口
                  if (isset($result['score'])) {
                      if ($result['score'] > $score) {
                          $score = $result['score'];
                      }
                  }*/

                $result = $this->similarScore($text_1, $text_2); // 调用本地相似度方法
                if (isset($result)) {
                    if ($result > $score) {
                        $score = $result;
                    }
                }

            }

            $data[$id] = $design->score * $score;
        }

        arsort($data, SORT_NUMERIC);

        $result_data = [];
        foreach ($data as $k => $v) {
            $result_data[] = $k;
        }

        return $result_data;
    }

    // 计算短语之间的相似性
    public function similarScore($text_1, $text_2)
    {
        $text_1_len = strlen($text_1);
        $text_2_len = strlen($text_2);

        $n = levenshtein($text_1, $text_2);

        if ($text_1_len > $text_2_len) {
            return (($text_1_len - $n) / $text_1_len);
        } else {
            return (($text_2_len - $n) / $text_2_len);
        }
    }

    //地区分值
    public function sortArea($design = [],$uid=0,$area = 0)
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
                $demand = DemandCompany::select('province','city')->where('user_id',$uid)->first();
                $score = 0;
                if(!empty($demand) && !empty($company)){
                    if($company->province == $demand->province){
                        //省份占比重30
                        $score = 30;
                    }
                    if($company->city == $demand->city){
                        //城市占比重70
                        $score = 70;
                    }
                    if($company->city == $demand->city && $company->province == $demand->province){
                        //省份和城市都存在占比重100
                        $score = 100;
                    }
                    $area = $area / 100;
                    $score = $score * $area;
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
    public function sortSuccessRate($design = [],$success_rate)
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
    public function sortEvaluate($design = [],$weight_score)
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
    public function sortSase($design = [],$case)
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
            $id = array_column($data, 'id');
            array_multisort($id,SORT_ASC,$data);
            foreach ($data as $key => $val){
                $design[$key]['sore'] += $val['sore'];
            }
        }
        return $design;
    }

    //最近推荐时间
    public function sortTime($design = [],$time)
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
            $id = array_column($data, 'id');
            array_multisort($id,SORT_ASC,$data);
            foreach ($data as $key => $val){
                $design[$key]['sore'] += $val['sore'];
            }
        }
        return $design;
    }

    //接单均价
    public function sortPrice($design = [],$average_price)
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



}