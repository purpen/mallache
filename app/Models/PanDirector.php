<?php

namespace App\Models;

use App\Helper\QiniuApi;
use App\Helper\Yunpan;
use Illuminate\Database\Eloquent\Model;

class PanDirector extends BaseModel
{
    protected $table = 'pan_director';

    /**
     * 一对多相对关联源文件表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function panFile()
    {
        return $this->belongsTo('App\Models\PanFile', 'pan_file_id');
    }

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


    // 返回文件/文件夹详细信息
    public function info()
    {
        return [
            'id' => $this->id,
            'pan_director_id' => $this->pan_director_id,
            'type' => $this->type,
            'name' => $this->name,
            'size' => $this->size,
            'mime_type' => $this->mime_type,
            'url_small' => $this->url_small,
            'url_file' => $this->url_file,
            'user_id' => $this->user_id,
            'user_name' => $this->user->username,
            'group_id' => $this->group_id,
            'created_at' => $this->created_at,
            'open_set' => $this->open_set,
        ];
    }

    /**
     * 资源缩略图访问修改器
     */
    public function getUrlSmallAttribute()
    {
        if ($this->type == 1) {
            return null;
        }
        $auth = QiniuApi::auth();
        // 私有空间中的外链 http://<domain>/<file_key>
        $baseUrl = null;
        if (strpos($this->mime_type, 'image') !== false) {
            $baseUrl = config('filesystems.disks.yunpan_qiniu.url') . $this->url . config('filesystems.disks.yunpan_qiniu.small');
        } else if (strpos($this->mime_type, 'video') !== false) {
            $baseUrl = config('filesystems.disks.yunpan_qiniu.url') . $this->url . config('filesystems.disks.yunpan_qiniu.video_small');
        }

        // 对链接进行签名
        $signedUrl = $auth->privateDownloadUrl($baseUrl);
        return $signedUrl;
    }

    /**
     * 资源访问修改器
     */
    public function getUrlFileAttribute()
    {
        if ($this->type == 1) {
            return null;
        }

        $auth = QiniuApi::auth();
        // 私有空间中的外链 http://<domain>/<file_key>
        $baseUrl = config('filesystems.disks.yunpan_qiniu.url') . $this->url;
        // 对链接进行签名
        $signedUrl = $auth->privateDownloadUrl($baseUrl);
        return $signedUrl;
    }

    /*
     * 判断文件/文件夹是否是系统创建
     */
    public function isAuto()
    {
        return 2 == $this->is_auto;
    }

    /**
     * 文件、文件夹设置指定权限(子目录递归修改)
     *
     * @param $open_set integer 隐私设置：1.公开 2.私有
     * @param $group_id string|null  所属群组ID json数组
     */
    public function setPermission($open_set, $group_id)
    {
        $this->open_set = $open_set;
        $this->group_id = $group_id;
        $this->save();
        if ($this->type == 2) {
            return;
        } else if ($this->type == 1) {
            $pan_dir_lists = PanDirector::where(['pan_director_id', $this->id])->first();
            foreach ($pan_dir_lists as $pan_dir) {
                $pan_dir->setPermission($this->open_set, $this->group_id);
            }
        }
    }

    /**
     * 文件、文件夹设置为私有
     */
    public function setPrivate()
    {
        $this->setPermission(2, null);
    }

    /**
     * 文件、文件夹设置为公开
     */
    public function setPublic()
    {
        $this->setPermission(1, null);
    }

    /**
     * 文件、文件夹设置为公开
     */
    public function setGroup()
    {
        $this->setPermission(1, null);
    }

}
