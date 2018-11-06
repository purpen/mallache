<?php

namespace App\Http\Controllers\Api\Jd;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Jdcloud\Credentials\Credentials;
use Jdcloud\Result;
use Jdcloud\Vm\VmClient;


class JdCreateAppController extends BaseController
{
    /**
     * @api {get} /jd/create/app 创建应用
     * @apiVersion 1.0.0
     * @apiName JdCreateApp test
     * @apiGroup JdCreateApp
     *
     */
    public function test()
    {
        $vm = new VmClient([
            'credentials' => new Credentials('3F1AA9CF28626825E429650F65A0E652', '23F5AF109781B0302317F9ED1DD1837C'),
            'version' => 'latest',
            'scheme' => 'https',
//            'http'    => [
//                'verify' => 'C:/ca-bundle.crt'
//            ]
        ]);

        try{
            $res = $vm->createInstances([
                'regionId'  => 'cn-north-1',
                'instanceSpec' => [
                    'az' => 'cn-north-1a',
                    'imageId' => '8e187a0a-ea7c-4ad1-ba32-f21e52fb8926',
                    'instanceType' =>  'g.n2.medium',
                    'name' => 'phpcreate',
                    'primaryNetworkInterface' => [
                        'networkInterface' => [
                            'subnetId' => 'subnet-ll47yy373i'
                        ]
                    ],
                    'systemDisk' => [
                        'diskCategory' => 'local'
                    ]
                ]
            ]);
            dd($res);
            print_r($res);
            print("Request Id: ". $res['requestId']. "\n");
            print_r($res['result']);
        }catch (\Jdcloud\Exception\JdcloudException $e) {
            print("Detail Message: " . $e->getMessage(). "\n");
            print("Request Id: ". $e->getJdcloudRequestId(). "\n");
            print("Error Type: ". $e->getJdcloudErrorType(). "\n");
            print("Error Code: " . $e->getJdcloudErrorCode(). "\n");
            print("Error Detail Status: ". $e->getJdcloudErrorStatus(). "\n");
            print("Error Detail Message: ". $e->getJdcloudErrorMessage(). "\n");
        }

    }
}
