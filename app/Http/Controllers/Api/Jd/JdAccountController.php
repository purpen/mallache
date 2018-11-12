<?php

namespace App\Http\Controllers\Api\Jd;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Jdcloud\Credentials\Credentials;
use Jdcloud\Result;
use Jdcloud\Vm\VmClient;


class JdAccountController extends BaseController
{
    /**
     * @api {get} /jd/jdAccount 获取京东account
     * @apiVersion 1.0.0
     * @apiName jdAccount token
     * @apiGroup jdAccount
     *
     * @apiParam {string} code
     */
    public function account(Request $request)
    {
        $code = $request->input('code');
        if(empty($code)){
            return $this->response->array($this->apiError('code值不能为空', 416));
        }
        $client_id = config('constant.client_id');
        $client_secret = config('constant.client_secret');
        $url = 'https://oauth2.jdcloud.com/token?client_id='.$client_id.'&grant_type=authorization_code&code='.$code.'&client_secret='.$client_secret;
        //获取access_token
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $date = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($date, true);
        $access_token = $response['access_token'];
        if(!empty($response['error'])){
            return $this->response->array($this->apiError($response['error_description'], 416));
        }
        //拿token获取用户
        $account_url = 'https://oauth2.jdcloud.com/userinfo';
        $aHeader = array(
            'Authorization:Bearer '.$access_token,
        );
        $account_ch = curl_init();
        curl_setopt($account_ch, CURLOPT_HTTPGET, true);
        curl_setopt($account_ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($account_ch, CURLOPT_URL, $account_url);
        curl_setopt($account_ch, CURLOPT_HTTPHEADER, $aHeader);
        $account = curl_exec($account_ch);
        curl_close($account_ch);
        $response_account = json_decode($account, true);
        return $this->response->array($this->apiSuccess('获取成功', 200 , $response_account));
    }
}
