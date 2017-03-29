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
        $upload_url = config('filesystems.disks.qiniu.upload_url');
        $upToken = QiniuApi::upToken();
        return view('test.index',compact('upToken' , 'upload_url'));
    }

    public function create()
    {
        $uptoken = QiniuApi::upToken();

    }
}
