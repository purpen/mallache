<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Classes\PHPExcel;


class userExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导出用户excel';

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
        $excelObj = new PHPExcel();
        $dir = '/tmp/';

        // 给表格添加数据
        $excelObj->setActiveSheetIndex(0); // 从0开始
        $currentSheet = $excelObj->getActiveSheet(); // 获取当前活动sheet
        $currentSheet->setCellValue( 'A1', 'ID' )         //给表的单元格设置数据
        ->setCellValue( 'B1', '账户' )
            ->setCellValue( 'C1', '密码' )
            ->setCellValue( 'D1', '用户属性' )
            ->setCellValue( 'E1', '微信openid' )
            ->setCellValue( 'F1', '状态' );


        $designObj = DB::table('users')
            ->select(['users.id as ID', 'account as 账户', 'password as 密码', 'kind as 用户属性', 'wx_open_id as 微信openid', 'status as 状态'])
            ->get();

        $new_data = [];
        foreach ($designObj as $k=>$v){
            $arr = [];
            foreach ($v as $a=>$b){
                if ($a == "状态") {
                    if($b == 0){
                        $b = 1;
                    }else {
                        $b = 0;
                    }
                }
                $arr[$a] = $b;
            }
            $new_data[] = $arr;
        }

        $j = 2;
        foreach($new_data as $val){
            $currentSheet->setCellValue('A'.$j,$val['ID'])->setCellValue('B'.$j,$val['账户'])->setCellValue('C'.$j, $val['密码'])->setCellValue('D'.$j, $val['用户属性'])->setCellValue('E'.$j, $val['微信openid'])->setCellValue('F'.$j, $val['状态']);
            $j++; // 每循环一次换一行写入数据
        }
        $sheeetWrite = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
        $sheeetWrite->save($dir.'用户表信息.xlsx');
    }
}
