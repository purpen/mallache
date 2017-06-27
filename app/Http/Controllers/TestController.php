<?php

namespace App\Http\Controllers;

use App\Helper\QiniuApi;
use App\Models\DemandCompany;
use App\Models\DesignCaseModel;
use App\Models\DesignCompanyModel;
use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * @return string
     */
    public function index(Request $request)
    {
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
