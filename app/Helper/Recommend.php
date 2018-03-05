<?php
namespace App\Helper;

use App\Events\ItemStatusEvent;
use App\Models\DesignCompanyModel;
use App\Models\DesignItemModel;
use App\Models\Item;

class Recommend
{
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * 匹配执行
     */
    public function handle()
    {
        //设计类型
        $type = (int)$this->item->type;
        $design_types = json_decode($this->item->design_types, true);

        //产品设计
        if ($type == 1) {
            $design = $this->productDesign($type, $design_types);
        } else if ($type == 2) {
            $design = $this->uDesign($type, $design_types);
        }


//Log::info($design);
        if (count($design) > 0) {
            //剔除已推荐的
            $ord_recommend = $this->item->ord_recommend;
            if (!empty($ord_recommend)) {
                $ord_recommend_arr = explode(',', $ord_recommend);
                $design = array_diff($design, $ord_recommend_arr);
            }

            $design = array_slice($design, 0, 5);

            //判断是否匹配到设计公司
            if (empty($design)) {
                // 临时处理 永不匹配失败
                $this->failAction();

                // 原处理
//                $this->itemFail();
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
            $this->failAction();

            // 原处理
//            $this->itemFail();
        }

        //注销变量
        unset($design_type, $field, $design_id_arr, $design, $recommend);

    }

    //匹配失败
    protected function itemFail()
    {
        if(config('constant.item_recommend_lose')){
            $this->item->status = 2;  //等待后台人工干预
        }else{
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

        $design_id_arr = array_rand($design_id_arr, 2);

        $recommend = implode(',', $design_id_arr);
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

        if((int)$user_id === (int)$this->item->user_id){
            $this->item->recommend =  $this->item->recommend . "," . $design_id;
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
        $max = $this->cost($this->item->productDesign->design_cost);

        //所属领域
        $field =  $this->item->productDesign->field;
        //周期
        $cycle = $this->item->productDesign->cycle;
        //项目公司地点
        $item_info = $this->item->itemInfo();
        $province = $item_info['province'];
        $city = $item_info['city'];


        $arr = [];
        foreach ($design_types as $design_type){
            //获取符合设计类型和设计费用的设计公司ID数组
            $design_id_arr = DesignItemModel::select('user_id')
                ->where('type', $type)
                ->where('design_type', $design_type)
                ->where('min_price', '<=', $max)
                ->where('project_cycle', $cycle)
                ->get()
                ->pluck('user_id')->all();

            if(empty($arr)){
                $arr =  $design_id_arr;
            }else{
                $arr = array_intersect($arr, $design_id_arr);
            }
        }
        $design_id_arr = $arr;


//Log::info($design_id_arr);
        //获取擅长的设计公司ID数组
        $design = DesignCompanyModel::select(['id', 'user_id'])
            ->where(['status' => 1, 'verify_status' => 1]);

        if($province && $province != -1){
            $design->where('province', $province)
                ->where('city', $city);
        }

        $design_user_id_arr = $design->whereIn('user_id', $design_id_arr)
            ->whereRaw('find_in_set(' . $field . ', good_field)')  // 擅长领域
            ->orderBy('score', 'desc')
            ->get()
            ->pluck('id')
            ->all();

        return $design_user_id_arr;
    }

    //UI UX 设计 推荐设计公司ID数组
    protected function uDesign($type, array $design_types)
    {
        //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
        $max = $this->cost($this->item->uDesign->design_cost);

        //所属领域
//        $field =  $this->item->productDesign->field;
        //周期
        $cycle = $this->item->uDesign->cycle;

        //项目公司地点
        $item_info = $this->item->itemInfo();
        $province = $item_info['province'];
        $city = $item_info['city'];

        $arr = [];
        foreach ($design_types as $design_type){
            //获取符合 设计类型 和 设计费用 的设计公司ID数组
            $design_id_arr = DesignItemModel::select('user_id')
                ->where('type', $type)
                ->where('design_type', $design_type)
                ->where('min_price', '<=', $max)
                ->where('project_cycle', $cycle)
                ->get()
                ->pluck('user_id')->all();

            if(empty($arr)){
                $arr =  $design_id_arr;
            }else{
                $arr = array_intersect($arr, $design_id_arr);
            }
        }
        $design_id_arr = $arr;

//Log::info($design_id_arr);
        //获取 擅长 的设计公司ID数组
        $design = DesignCompanyModel::select(['id', 'user_id'])
            ->where(['status' => 1, 'verify_status' => 1]);

        if($province && $province != -1){
            $design->where('province', $province)
                ->where('city', $city);
        }

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
}