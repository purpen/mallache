<?php

/**
 * 云盘相关配置
 */
return [
    // 文件分类正则配置
    'mime_type' => [
        'image' => '^image',
        'pdf' => 'pdf$',
        'document' => '^text|msword$',  // 文档
        'sheet' => 'spreadsheet|ms-excel$',  // 电子表格
        'powerpoint' => 'powerpoint$',   // 演示文稿
        'video' => '^video',     // 视频
        'audio' => '^audio',        // 音频
    ],

];