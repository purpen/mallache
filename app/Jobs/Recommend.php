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
        //领域
        $field = $this->item->field;

        //获取擅长领域的设计公司ID数组
        $design_id_arr = DesignItemModel::select('user_id')
            ->where('good_field', $field)
            ->get()
            ->pluck('user_id')->all();

Log::info($design_id_arr);
        //获取符合设计类型的设计公司ID数组
        $design = DesignCompanyModel::select('user_id')
            ->where('status','=', 1)
            ->whereIn('user_id',$design_id_arr)
            ->whereRaw('find_in_set(' . $design_type . ', design_type)')
            ->orderBy('score', 'desc')
            ->get()
            ->pluck('user_id')
            ->all();

Log::info($design);
        if($count = count($design) > 0){
            $design = array_slice($design, 0, 5);
            $recommend = implode(',',$design);

            $this->item->recommend = $recommend;
            $this->item->save();
        }else{
            $recommend = '';
        }

        //注销变量
        unset($design_type, $field, $design_id_arr, $design, $recommend);

    }

}
