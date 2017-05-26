<?php

namespace App\Jobs;

use App\Models\DesignCompanyModel;
use App\Models\DesignItemModel;
use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 推荐设计公司队列
 * Class Recommend
 * @package App\Jobs
 */
class Recommend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 3;

    protected $item;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //设计类型
        $type = (int)$this->item->type;
        $design_type = (int)$this->item->design_type;

        //产品设计
        if($type === 1){
            $design = $this->productDesign($type, $design_type);
        }else{
            $design = $this->uDesign($type, $design_type);
        }


//Log::info($design);
        if($count = count($design) > 0){
            //剔除已推荐的
            $ord_recommend = $this->item->ord_recommend;
            if(!empty($ord_recommend)){
                $ord_recommend_arr = explode(',', $ord_recommend);
                $design = array_diff($design, $ord_recommend_arr);
            }

            $design = array_slice($design, 0, 5);
            $recommend = implode(',',$design);

            $this->item->recommend = $recommend;
            $this->item->save();
        }

        //注销变量
        unset($design_type, $field, $design_id_arr, $design, $recommend);

    }


    /**
     * 产品设计推荐 设计公司ID
     *
     * @param $design_type
     * @return array
     */
    protected function productDesign($type, $design_type)
    {
        //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
        $max = $this->cost($this->item->productDesign->design_cost);

        //所属领域
        $field =  $this->item->productDesign->field;
        //周期
        $cycle = $this->item->productDesign->cycle;
        //项目公司地点
        $city = $this->item->itemInfo()['city'];

        //获取符合设计类型和设计费用的设计公司ID数组
        $design_id_arr = DesignItemModel::select('user_id')
            ->where('type', $type)
            ->where('design_type', $design_type)
            ->where('min_price', '<', $max)
            ->where('project_cycle', $cycle)
            ->get()
            ->pluck('user_id')->all();

//Log::info($design_id_arr);
        //获取擅长的设计公司ID数组
        $design = DesignCompanyModel::select(['id', 'user_id'])
            ->where(['status' => 1, 'verify_status' => 1])
            ->where('city', $city)
            ->whereIn('user_id',$design_id_arr)
//            ->whereRaw('find_in_set(' . $field . ', good_field)')  // 擅长领域
            ->orderBy('score', 'desc')
            ->get()
            ->pluck('id')
            ->all();

        return $design;
    }

    //UI UX 设计 推荐设计公司ID数组
    protected function uDesign($type, $design_type)
    {
        //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
        $max = $this->cost($this->item->uDesign->design_cost);

        //所属领域
        $field =  $this->item->productDesign->field;
        //周期
        $cycle = $this->item->productDesign->cycle;

        //项目公司地点
        $city = $this->item->itemInfo()['city'];

        //获取符合 设计类型 和 设计费用 的设计公司ID数组
        $design_id_arr = DesignItemModel::select('user_id')
            ->where('type', $type)
            ->where('design_type', $design_type)
            ->where('min_price', '<', $max)
            ->where('project_cycle', $cycle)
            ->get()
            ->pluck('user_id')->all();

//Log::info($design_id_arr);
        //获取 擅长 的设计公司ID数组
        $design = DesignCompanyModel::select(['id', 'user_id'])
            ->where(['status' => 1, 'verify_status' => 1])
            ->where('city', $city)
            ->whereIn('user_id',$design_id_arr)
            ->orderBy('score', 'desc')
            ->get()
            ->pluck('id')
            ->all();

        return $design;
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
        switch ($design_cost){
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
