<?php

namespace App\Http\Controllers;

use App\Helper\QiniuApi;
use App\Models\DemandCompany;
use App\Models\DesignCaseModel;
use App\Models\DesignCompanyModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    /**
     * @return string
     */
    public function index(Request $request)
    {
//        $user_id_arr = DesignCompanyModel::select('id','user_id')
//            ->where('status', 1)
//            ->get()
//            ->toArray();
//        dd($user_id_arr);

//        echo sys_get_temp_dir();

//        $designs = DesignCaseModel::get();
//        foreach ($designs as $design){
//            $design->status = 1;
//            $design->save();
//        }
//        echo "ok";

//        phpinfo();
//        $user = User::get();
//        foreach($user as $v){
//            if($v->role_id === 1){
//                $v->role_id = 20;
//                $v->save();
//            }
//        }
//        return "ok";


//        dd($request->getPathInfo());
//        $users = User::where('type', 0)->get();
//        foreach($users as $v)
//        {
//            $v->type = 1;
//            $v->save();
//        }
//
//        $des = DemandCompany::get();
//        foreach($des as $v){
//            $user = User::find($v->user_id);
//            $user->demand_company_id = $v->id;
//            $user->save();
//        }
//
//        $users = User::where(['type' => 1, 'demand_company_id' => 0])->get();
//        foreach($users as $v){
//            DemandCompany::createCompany($v->id);
//        }
//        echo 'ok';

//        $case = DesignCaseModel::where('design_company_id', 0)->get();
//        foreach($case as $v){
//            $user = User::find($v->user_id);
//            $v->design_company_id = $user->design_company_id;
//            $v->save();
//        }
//        echo 222;
//        //随机字符串(回调查询)
//        $random = [];
//        for ($i = 0; $i<2; $i++){
//            $random[] = uniqid();  //获取唯一字符串
//        }
//        $upload_url = config('filesystems.disks.qiniu.upload_url');
//        $token = QiniuApi::upToken();
//        $user_id = 1;
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

    public function create()
    {
        //随机字符串(回调查询)
        $random = [];
        for ($i = 0; $i<2; $i++){
            $random[] = uniqid();  //获取唯一字符串
        }
        $upload_url = config('filesystems.disks.qiniu.upload_url');
        $token = QiniuApi::upToken();
        $user_id = 1;
        return view('test.index',compact('token' , 'upload_url' , 'random' , 'user_id'));

    }

    /**
     * 设计公司导出
     *
     */
    public function designCompanyExcel(Request $request)
    {
        //查询订单数据集合
        $data = $this->designCompanySelect()->get();
        //导出Excel表单
        $this->createExcel($data, '设计公司');
    }

    /**
     * 设计公司查询条件
     */
    protected function designCompanySelect()
    {
        $orderObj = DB::table('design_company')->select([
            'id as ID',
            'company_name as 公司全称',
            'company_abbreviation as 公司简称',
        ]);
        return $orderObj;
    }

    /**
     * 生成导出的excel表单
     * @param $data 数据
     * @param string $message 名称
     */
    protected function createExcel($data, $message = '表单')
    {
        $message = strval($message);
        $new_data = [];
        foreach ($data as $k=>$v){
            $arr = [];
            foreach ($v as $a=>$b){
                $arr[$a] = $b;
            }
            $new_data[] = $arr;

        }
        //生成excel表单
        Excel::create($message, function ($excel) use ($new_data) {
            $excel->sheet('sheet1', function ($sheet) use ($new_data) {
                $sheet->fromArray($new_data);
            });
        })->export('xlsx');
    }
}
