<?php

namespace App\Http\Controllers;

use App\Helper\QiniuApi;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * @return string
     */
    public function index()
    {
        //随机字符串(回调查询)
        $random = [];
        for ($i = 0; $i<2; $i++){
            $random[] = uniqid();  //获取唯一字符串
        }
        $upload_url = config('filesystems.disks.qiniu.upload_url');
        $token = QiniuApi::upToken();
        return view('test.index',compact('token' , 'upload_url' , 'random'));
    }

    public function create()
    {
        $uptoken = QiniuApi::upToken();

    }
}
