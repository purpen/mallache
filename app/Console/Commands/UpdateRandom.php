<?php

namespace App\Console\Commands;

use App\Models\DesignCaseModel;
use App\Models\AwardCase;
use Illuminate\Console\Command;

class UpdateRandom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'random:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订时更新案列作品、文章等随机数，用于列表随机排序';

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

        // 案例作品
        $page = 1;
        $size = 200;
        $is_end = false;
        $total = 0;
        while(!$is_end){
            $offset = ($page - 1) * $size;
            $list = DesignCaseModel::select('id','random','open')
                ->where('open', 1)
                ->skip($offset)
                ->limit($size)
                ->get();

            if(empty($list)){
                echo "get designCase list is null,exit......\n";
                break;
            }
            $max = count($list);
            for ($i=0; $i < $max; $i++) {
                $random = random_int(1000, 9999);
                $id = $list[$i]->id;
                echo "set designCase[". $id ."]..........\n";
                $ok = DesignCaseModel::find($id)->update(['random'=>$random]);
                if($ok) $total++;
            }
            if($max < $size){
                echo "designCase list is end!!!!!!!!!,exit.\n";
                break;
            }
            $page++;
            echo "page [$page] updated---------\n";
        }
        echo "designCase update count: $total.....";


        // 奖项作品
        $page1 = 1;
        $size1 = 200;
        $is_end1 = false;
        $total1 = 0;
        while(!$is_end1){
            $offset = ($page1 - 1) * $size1;
            $list = AwardCase::select('id','random','status')
                ->where('status', 1)
                ->skip($offset)
                ->limit($size1)
                ->get();

            if(empty($list)){
                echo "get awardCase list is null,exit......\n";
                break;
            }
            $max = count($list);
            for ($i=0; $i < $max; $i++) {
                $random = random_int(1000, 9999);
                $id = $list[$i]->id;
                echo "set awardCase[". $id ."]..........\n";
                $ok = AwardCase::find($id)->update(['random'=>$random]);
                if($ok) $total1++;
            }
            if($max < $size1){
                echo "awardCase list is end!!!!!!!!!,exit.\n";
                break;
            }
            $page1++;
            echo "page1 [$page1] updated---------\n";
        }
        echo "awardCase update count: $total1.....";

    }
}
