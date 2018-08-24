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
            ->setCellValue( 'C1', '公司简称' )
            ->setCellValue( 'D1', '用户id' )
            ->setCellValue( 'E1', '用户手机号' )
            ->setCellValue( 'F1', '网址' )
            ->setCellValue( 'G1', '详细地址' )
            ->setCellValue( 'H1', '联系人' )
            ->setCellValue( 'I1', '联系人邮箱' )
            ->setCellValue( 'J1', '擅长领域' );

        $designObj = DB::table('design_company')
            ->join('users','design_company.id', '=', 'users.design_company_id' )
            ->select(['design_company.id as ID', 'company_name as 公司全称', 'company_abbreviation as 公司简称', 'contact_name as 联系人', 'design_company.email as 联系人邮箱', 'address as 详细地址', 'web as 网址', 'good_field as 擅长领域' , 'users.id as 用户id'  , 'users.phone as 用户手机号'])
            ->get();

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
            $currentSheet->setCellValue('A'.$j,$val['ID'])->setCellValue('B'.$j,$val['公司全称'])->setCellValue('C'.$j, $val['公司简称'])->setCellValue('D'.$j, $val['用户id'])->setCellValue('E'.$j, $val['用户手机号'])->setCellValue('F'.$j, $val['网址'])->setCellValue('G'.$j, $val['详细地址'])->setCellValue('H'.$j, $val['联系人'])->setCellValue('I'.$j, $val['联系人邮箱'])->setCellValue('J'.$j, $val['擅长领域']);
            $j++; // 每循环一次换一行写入数据
        }
        $sheeetWrite = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
        $sheeetWrite->save($dir.'设计公司名称.xlsx');
    }
}
