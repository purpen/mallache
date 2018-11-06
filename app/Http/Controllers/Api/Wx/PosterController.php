<?php
/**
 * Created by PhpStorm.
 * User: cailiguang
 * Date: 2018/11/6
 * Time: 11:00 AM
 */
namespace App\Http\Controllers\Api\Wx;


use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
class PosterController extends BaseController
{
    /**
     * @api {post} /wechat/poster  生成海报
     * @apiVersion 1.0.0
     * @apiName WxPoster poster
     * @apiGroup Wx
     *
     * @apiParam {string} company_img 设计公司头像
     * @apiParam {string} company_name 设计公司名称
     * @apiParam {string} small_img 小程序图片
     * @apiParam {string} innovate 创新力
     * @apiParam {string} innovate_avg 创新力平均
     * @apiParam {string} serve 服务
     * @apiParam {string} serve_avg 服务平均
     * @apiParam {string} age 经营年限
     * @apiParam {string} age_avg 经营年限平均
     * @apiParam {string} scale 规模
     * @apiParam {string} scale_avg 规模平均
     *
     */
    public function poster(Request $request)
    {
        //设计公司头像
        $company_img = $request->input('company_img');
        //设计公司名称
        $company_name = $request->input('company_name');
        //小程序图片
        $small_img = $request->input('small_img');
        //创新力
        $innovate = $request->input('innovate');
        //创新力平均
        $innovate_avg = $request->input('innovate_avg');
        //服务
        $serve = $request->input('serve');
        //服务平均
        $serve_avg = $request->input('serve_avg');
        //经营年限
        $age = $request->input('age');
        //经营年限平均
        $age_avg = $request->input('age_avg');
        //规模
        $scale = $request->input('scale');
        //规模平均
        $scale_avg = $request->input('scale_avg');

        //裁剪公司头像为圆形
        $src_img = imagecreatefromstring(file_get_contents($company_img));
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
        $new_company_img = $manager->make('/tmp/'.$save_name.'.jpg')->resize(162,162);

        $blue_img->insert($new_company_img, 'top-left' , 360 ,380);
        //设计公司名称 *变量
        $blue_img->text($company_name , 444, 620, function($font) {
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
        $blue_img->text($innovate , 444, 798, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#FF6E73');
        });
        //创新力指数,行业平均数*变量
        $blue_img->text($innovate_avg , 612, 798, function($font) {
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
        $blue_img->text($serve , 444, 885, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#FF6E73');
        });
        //服务分,行业平均数*变量
        $blue_img->text($serve_avg , 612, 885, function($font) {
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
        $blue_img->text($age , 444, 972, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#FF6E73');
        });
        //经营年限,行业平均数*变量
        $blue_img->text($age_avg , 612, 972, function($font) {
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
        $blue_img->text($scale , 444, 1059, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#FF6E73');
        });
        //团队规模,行业平均数*变量
        $blue_img->text($scale_avg , 612, 1059, function($font) {
            $font->file(base_path('storage/fonts/SourceHanSerifCN-Bold1.ttf'));
            $font->size(42);
            $font->color('#F9DB59');
        });
        //小程序头像
        $blue_img->circle(205, 190, 1290, function ($draw) {
            $draw->background('#FFFFFF');
        });
        $new_small_img = $manager->make($small_img)->resize(198,198);

        $blue_img->insert($new_small_img, 'top-left' , 90 ,1190);

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
        $merge_img_name = uniqid('merge');
        $blue_img->save('/tmp/'.$merge_img_name.'.jpg');

        $accessKey = config('filesystems.disks.qiniu.access_key');
        $secretKey = config('filesystems.disks.qiniu.secret_key');
        $auth = new Auth($accessKey, $secretKey);

        $bucket = config('filesystems.disks.qiniu.bucket');

        $token = $auth->uploadToken($bucket);
        $key = 'smallPoster/'.date("Ymd").'/'.uniqid();
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 put 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, '/tmp/'.$merge_img_name.'.jpg');
        $posterImg = config('filesystems.disks.qiniu.url').$key;
        unlink('/tmp/'.$save_name.'.jpg');
        unlink('/tmp/'.$merge_img_name.'.jpg');

        return $this->response->array($this->apiSuccess('获取成功', 200 , compact('posterImg')));

    }
}