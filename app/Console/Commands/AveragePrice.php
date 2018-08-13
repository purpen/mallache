<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\Statistics;
use Illuminate\Support\Facades\DB;

class AveragePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计设计公司信息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //获取所有设计公司
        $data = DB::table('design_company')->get()->pluck('id');
        if(!empty($data)){
            $statistics = new Statistics;
            //更新所有设计公司平均价格
            $statistics->contractAveragePrice($data);
            //更新所有设计公司案例数量
            $statistics->companyDesignCase($data);
            //更新所有设计公司推荐次数
            $statistics->saveRecommend($data);
            //更新所有设计公司成功率
            $statistics->successRate($data);
            //评价平均分
            $statistics->evaluationScore($data);
        }
    }

}
