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
        $design_type = (string)$this->item->design_type;

        //产品设计
        if(in_array($design_type, [1, 2, 3])){
            $design = $this->productDesign($design_type);
        }else{
            $design = $this->uDesign($design_type);
        }


//Log::info($design);
        if($count = count($design) > 0){
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
    protected function productDesign($design_type)
    {
        //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
        $max = $this->cost($this->item->productDesign->design_cost);

        //所属领域
        $field =  $this->item->productDesign->field;
        //获取符合设计类型和设计费用的设计公司ID数组
        $design_id_arr = DesignItemModel::select('user_id')
            ->where('design_type', $design_type)
            ->where($max, '>', 'min_price')
            ->get()
            ->pluck('user_id')->all();

//Log::info($design_id_arr);
        //获取擅长的设计公司ID数组
        $design = DesignCompanyModel::select('user_id')
            ->where('status','=', 1)
            ->whereIn('user_id',$design_id_arr)
            ->whereRaw('find_in_set(' . $field . ', good_field)')
            ->orderBy('score', 'desc')
            ->get()
            ->pluck('user_id')
            ->all();

        return $design;
    }

    //UI UX 设计 推荐设计公司ID数组
    protected function uDesign($design_type)
    {
        //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
        $max = $this->cost($this->item->uDesign->design_cost);

        //所属领域
        $field =  $this->item->productDesign->field;
        //获取符合设计类型和设计费用的设计公司ID数组
        $design_id_arr = DesignItemModel::select('user_id')
            ->where('design_type', $design_type)
            ->where($max, '>', 'min_price')
            ->get()
            ->pluck('user_id')->all();

//Log::info($design_id_arr);
        //获取擅长的设计公司ID数组
        $design = DesignCompanyModel::select('user_id')
            ->where('status','=', 1)
            ->whereIn('user_id',$design_id_arr)
            ->orderBy('score', 'desc')
            ->get()
            ->pluck('user_id')
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
        //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
        $max = 10000;
        switch ($design_cost){
            case 1:
                $max = 10000;
                break;
            case 2:
                $max = 50000;
                break;
            case 3:
                $max = 100000;
                break;
            case 4:
                $max = 200000;
                break;
            case 5:
                $max = 300000;
                break;
            case 6:
                $max = 500000;
                break;
            case 7:
                $max = 500000;
                break;
        }

        return $max;
    }

}
