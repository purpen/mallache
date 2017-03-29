<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

        'qiniu' => [
            'driver'  => 'qiniu',
            'upload_url' => 'https://up-z1.qbox.me',
//            'domains' => [
//                'default'   => 'oni525j96.bkt.clouddn.com', // 你的七牛域名
////                'https'     => 'saas.taihuoniao.com',         // 你的HTTPS域名
//                'custom'    => 'http://oni525j96.bkt.clouddn.com',         // 你的自定义域名
//            ],
            'access_key'=> 'AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i',  //AccessKey
            'secret_key'=> 'F_g7diVuv1X4elNctf3o3bNjhEAe5MR3hoCk7bY6',  //SecretKey
            'bucket'    => 'frmallache',  //Bucket名字
            'domain' => 'saas',                                              //存储域
            'call_back_url' => 'http://sa.taihuoniao.com/asset/callback',
//            'url' => '',                    //图片服务器
            'small' => '-sm'              //缩略图
        ],

    ],

];
