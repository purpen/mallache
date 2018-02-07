<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Classes\PHPExcel;


class designCompanyExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'designCompany:excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导出设计公司excel';

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
        ->setCellValue( 'B1', '公司全称' )
            ->setCellValue( 'C1', '公司简称' );

        $designObj = DB::table('design_company')->select([
            'id as ID',
            'company_name as 公司全称',
            'company_abbreviation as 公司简称',
        ])->get();

        $new_data = [];
        foreach ($designObj as $k=>$v){
            $arr = [];
            foreach ($v as $a=>$b){
                $arr[$a] = $b;
            }
            $new_data[] = $arr;

        }

        $j = 2;
        foreach($new_data as $val){
            $currentSheet->setCellValue('A'.$j,$val['ID'])->setCellValue('B'.$j,$val['公司全称'])->setCellValue('C'.$j, $val['公司简称']);
            $j++; // 每循环一次换一行写入数据
        }
        $sheeetWrite = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
        $sheeetWrite->save($dir.'设计公司名称.xlsx');
    }
}
