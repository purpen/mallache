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
            'upload_url' => env('QINIU_UPLOAD_URL','http://up-z1.qiniu.com'),
            'access_key'=> 'AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i',  //AccessKey
            'secret_key'=> 'F_g7diVuv1X4elNctf3o3bNjhEAe5MR3hoCk7bY6',  //SecretKey
            'bucket'    => 'frmallache',  //Bucket名字
            'domain' => 'saas',
            'call_back_url' => env('QINIU_CALL_BACK_URL', 'https://sa.taihuoniao.com/asset/callback'),
            //
            'url' => 'https://d3g.taihuoniao.com/',                    //图片服务器
            'small' => '-p280x210.jpg',       //缩略图
            'big' => '-p800.jpg',                 //大图
            'logo' => '-p180x180.jpg',      //头像
            'middle' => '-p450x255',        // 中图
        ],

        // 云盘配置
        'yunpan_qiniu' => [
            'bucket' => 'yunpanmallache',
            'domain' => 'saas_yunpan',
            'call_back_url' => env('QINIU_YUNPAN_CALL_BACK_URL', 'https://sa.taihuoniao.com/yunpanCallback'),
            'url' => 'https://pan.d3ingo.com/', //图片服务器
            'small' => '-small',       //缩略图

            'video_small' => '?vframe/png/offset/1/w/60/h/60',  // 视频缩略图
        ],

    ],

];
