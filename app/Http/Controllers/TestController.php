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
use Intervention\Image\Image;
use Intervention\Image\Imagick\Font;
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\ImageManager;

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
//        $excelObj = new PHPExcel();
//        $dir = '/tmp/';
//
//        // 给表格添加数据
//        $excelObj->setActiveSheetIndex(0); // 从0开始
//        $currentSheet = $excelObj->getActiveSheet(); // 获取当前活动sheet
//        $currentSheet->setCellValue( 'A1', 'ID' )         //给表的单元格设置数据
//        ->setCellValue( 'B1', '公司全称' )
//        ->setCellValue( 'C1', '公司简称' );
//
//        $designObj = DB::table('design_company')->select([
//            'id as ID',
//            'company_name as 公司全称',
//            'company_abbreviation as 公司简称',
//        ])->get();
//
//        $new_data = [];
//        foreach ($designObj as $k=>$v){
//            $arr = [];
//            foreach ($v as $a=>$b){
//                $arr[$a] = $b;
//            }
//            $new_data[] = $arr;
//
//        }
//
//        $j = 2;
//        foreach($new_data as $val){
//            $currentSheet->setCellValue('A'.$j,$val['ID'])->setCellValue('B'.$j,$val['公司全称'])->setCellValue('C'.$j, $val['公司简称']);
//            $j++; // 每循环一次换一行写入数据
//        }
//        $sheeetWrite = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
//        $sheeetWrite->save($dir.'设计公司名称.xlsx');

        //裁剪图片为圆形
        $src_img = imagecreatefromstring(file_get_contents('/tmp/WechatIMG66.jpeg'));
        $w = imagesx($src_img);
        $h = imagesy($src_img);
        $w = $h = min($w, $h);

        $img = imagecreatetruecolor($w, $h);
        //这一句一定要有
        imagesavealpha($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $bg);
        $r   = $w / 2; //圆半径
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
        $save_name = uniqid();
        //输出图片到文件
        imagepng ($img,'/tmp/'.$save_name.'.jpg');
        //释放空间
        imagedestroy($src_img);
        imagedestroy($img);
        // create empty canvas with background color
        $manager = new ImageManager(array('driver' => 'imagick'));
        //蓝色底
        $blue_img = $manager->canvas(900, 1470, '#223A94');
        //上面的大图
        $new_img = $manager->make('https://p4.taihuoniao.com/asset/180929/5baf4f0a3ffca2b3728b45bf-1');
        //调整图片大小
        $new_img->resize(840,405);
        //插入第一张图
        $blue_img->insert($new_img, 'top' , 0 ,30);

        //黄色的背景
        $yellow_img =  $manager->canvas(780, 630, '#F9DB59');

        //插入黄色背景
        $blue_img->insert($yellow_img, 'top' , 60 , 465);

        //设计公司头像  * 变量
        $blue_img->circle(162, 440, 460, function ($draw) {
            $draw->border(10, '#223A94');
        });
        $company_img = $manager->make('/tmp/'.$save_name.'.jpg')->resize(162,162);

        $blue_img->insert($company_img, 'top-left' , 360 ,380);
        //设计公司名称 *变量
        $blue_img->text('杭州飞鱼工业设计', 444, 620, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(45);
            $font->color('#223A94');
            $font->align('center');
        });
        //公司指数
        $blue_img->text('公司指数', 444, 735, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(30);
            $font->color('#223A94');
        });
        //行业平均数
        $blue_img->text('行业平均数', 612, 735, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(30);
            $font->color('#223A94');
        });
        //小蓝色的背景
        $small_blue_img =  $manager->canvas(678, 75, '#223A94');

        //插入小蓝色背景1
        $blue_img->insert($small_blue_img, 'top' , 111 , 750);
        //创新力指数
        $blue_img->text('创新力指数', 135, 798, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#F9DB59');
        });
        //创新力指数,公司指数*变量
        $blue_img->text('—' , 444, 798, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#FF6E73');
        });
        //创新力指数,行业平均数*变量
        $blue_img->text(580 , 612, 798, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#F9DB59');
        });
        //插入小蓝色背景2
        $blue_img->insert($small_blue_img, 'top' , 111 , 837);
        //服务分
        $blue_img->text('服务分', 135, 885, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#F9DB59');
        });
        //服务分,公司指数*变量
        $blue_img->text(580 , 444, 885, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#FF6E73');
        });
        //服务分,行业平均数*变量
        $blue_img->text(580 , 612, 885, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#F9DB59');
        });
        //插入小蓝色背景3
        $blue_img->insert($small_blue_img, 'top' , 111 , 924);
        //经营年限
        $blue_img->text('经营年限', 135, 972, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#F9DB59');
        });
        //经营年限,公司指数*变量
        $blue_img->text(20 , 444, 972, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#FF6E73');
        });
        //经营年限,行业平均数*变量
        $blue_img->text(20 , 612, 972, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#F9DB59');
        });
        //插入小蓝色背景4
        $blue_img->insert($small_blue_img, 'top' , 111 , 1011);
        //团队规模
        $blue_img->text('团队规模', 135, 1059, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#F9DB59');
        });
        //团队规模,公司指数*变量
        $blue_img->text(50 , 444, 1059, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#FF6E73');
        });
        //团队规模,行业平均数*变量
        $blue_img->text(50 , 612, 1059, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#F9DB59');
        });
        //小程序头像
        $blue_img->circle(205, 190, 1290, function ($draw) {
            $draw->background('#FFFFFF');
        });
        $small_img = $manager->make('/tmp/er.png')->resize(198,198);

        $blue_img->insert($small_img, 'top-left' , 90 ,1190);

        //长按图片识别小程序
        $blue_img->text('长按图片识别小程序', 360, 1260, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#FFFFFF');
        });
        //立即查看榜单
        $blue_img->text('立即查看榜单', 360, 1350, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#FFFFFF');
        });
        $blue_img->save('/tmp/shengcheng.jpg');
        unlink('/tmp/'.$save_name.'.jpg');
        return $blue_img->response('jpg');
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
