<?php

namespace App\Http\Controllers\Api\Jd;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Jdcloud\Credentials\Credentials;
use Jdcloud\Result;
use Jdcloud\Vm\VmClient;


class JdCodeController extends BaseController
{
    /**
     * @api {get} /jd/code 创建应用
     * @apiVersion 1.0.0
     * @apiName JdCode code
     * @apiGroup JdCode
     *
     */
    public function code()
    {
        $url = 'https://oauth2.jdcloud.com/authorize';
        $post_data = 'client_id=9651541661345895&redirect_uri=http://jdyun.taihuoniao.com&response_type=code&state=22222&code_challe nge_method=S256&code_challenge=Vuu-tYpwl_4xB8miLyRO2p__zQoADgG1A40LoYCYsgU';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        $output_array = json_decode($output,true);

        Log::info($output_array);

    }
}
