<?php

namespace App\Jobs;

use App\Helper\WeightedSort;
use App\Models\DesignCompanyModel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

/**
 * 计算设计公司权重值队列
 * Class CalculationWeight
 * @package App\Jobs
 */
class CalculationWeight implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 3;

    //用户ID
    protected $user_id = null;

    // 设计公司ID
    protected $design_company_id = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $design_company_id)
    {
        $this->user_id = (int)$user_id;
        $this->design_company_id = (int)$design_company_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!$design = DesignCompanyModel::where('user_id', $this->user_id)->first()){
            return;
        }
        //计算权重值
        $weighted = new WeightedSort($this->user_id, $this->design_company_id);
        $score = $weighted->getScore();

        try{
            $design->score = $score;
            $design->save();
        }
        catch (\Exception $e){
            throw new \Exception('设计公司权重值保存失败',500);
        }
    }

    public function failed(\Exception $exception)
    {
        Log::error('code:' . $exception->getCode() . '; message:' . $exception->getMessage());
    }

}
