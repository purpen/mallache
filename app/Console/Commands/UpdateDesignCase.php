<?php

namespace App\Console\Commands;

use App\Models\DesignCaseModel;
use Illuminate\Console\Command;

class UpdateDesignCase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:design_case {--evt=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新案例作品: --evt=1  [默认1 1.状态自动启用；2.状态自动启用；]';

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

        $evt = (int)$this->option('evt');
        echo "currnet evt : $evt.\n";
        if ($evt === 1) {
          return $this->updateStatus();
        }elseif($evt === 2) {
          return $this->updateStatus();
        } else {
          echo "error params evt.\n";
        }

    }


    /**
     * 更新状态
     */
    protected function updateStatus()
    {
        // 奖项作品
        $page = 1;
        $size = 200;
        $is_end = false;
        $total = 0;
        while(!$is_end){
            $offset = ($page - 1) * $size;
            $list = DesignCaseModel::select('id','random','status','open','open_time')
                ->where('status', 1)
                ->where('open', 0)
                ->skip($offset)
                ->limit($size)
                ->get();

            if(empty($list)){
                echo "get designCase list is null,exit......\n";
                break;
            }
            $max = count($list);
            for ($i=0; $i < $max; $i++) {
                $id = $list[$i]->id;
                echo "set designCase[". $id ."]..........\n";
                $now = date("Y-m-d H:i:s");
                $ok = true;
                //$ok = DesignCaseModel::where('id', $id)->update(['open'=>1, 'open_time'=>$now]);
                if($ok) $total++;
            }
            if($max < $size){
                echo "designCase list is end!!!!!!!!!,exit.\n";
                break;
            }
            $page++;
            echo "page [$page] updated---------\n";
        }
        echo "designCase update status count: $total.....";
    
    }

}
