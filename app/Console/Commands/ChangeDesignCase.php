<?php

namespace App\Console\Commands;

use App\Models\DesignCaseModel;
use Illuminate\Console\Command;

class ChangeDesignCase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'designCase:change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设计公司案例数据结构变更';

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
        DesignCaseModel::chunk(100, function ($cases){
            foreach ($cases as $case){
                if($case->prizes == null){
                    $prize = $case->prize;
                    $prize_time = $case->prize_time;
                    if(!empty($prize) && !empty($prize_time)){
                        $case->prizes = json_encode([['time' => $prize_time,'type' => $prize]]);
                        $case->save();
                    }

                }
            }
        });

        $this->info('设计公司案例数据结构变更完毕');
    }
}
