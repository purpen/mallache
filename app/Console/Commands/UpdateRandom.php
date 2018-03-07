<?php

namespace App\Console\Commands;

use App\Models\DesignCaseModel;
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
        echo "update count: $total.....";

    }
}
