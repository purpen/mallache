<?php

namespace App\Http\Controllers;

use App\Helper\QiniuApi;
use App\Models\DesignCompanyModel;
use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * @return string
     */
    public function index()
    {

        $design_s = DesignCompanyModel::select(['id','user_id'])->get();

        foreach($design_s as $v){
            $user = User::find($v->user_id);
            $user->design_company_id = $v->id;
            $user->save();
        }

        echo 'true';
        //随机字符串(回调查询)
//        $random = [];
//        for ($i = 0; $i<2; $i++){
//            $random[] = uniqid();  //获取唯一字符串
//        }
//        $upload_url = config('filesystems.disks.qiniu.upload_url');
//        $token = QiniuApi::upToken();
//        $user_id = 1;
//        return view('test.index',compact('token' , 'upload_url' , 'random' , 'user_id'));
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
}
