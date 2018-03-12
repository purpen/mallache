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

    /**
     * 判断用户是否可以创建当前文件\文件夹
     *
     * @param int $user_id 用户ID
     * @param int $pan_director_id 上级文件ID
     * @param int $open_set 隐私设置：1.公开 2.个人
     * @param int $company_id 所在公司ID
     * @param int $group_id 所在项目组ID
     * @return bool
     */
    public static function isCreate(int $user_id, int $pan_director_id, int $open_set, int $company_id, int $group_id)
    {
        // 个人文件
        if ($open_set === 2 && $group_id === 0) {
            if ($pan_director_id === 0) {   // 一级目录
                return true;
            } else {
                // 判断上级目录是否存在
                $count = PanDirector::where(['user_id' => $user_id, 'id' => $pan_director_id, 'open_set' => $open_set, 'type' => 1, 'status' => 1])->count();
                if ($count > 0) {
                    return true;
                }
            }

        } elseif ($open_set === 1) {        // 公共文件
            if ($pan_director_id === 0) {   // 一级目录
                if ($group_id === 0) {
                    return true;
                } else {
                    if (Yunpan::isItem($user_id, $group_id)) {
                        return true;
                    }
                }

            } elseif ($pan_director_id > 0) {
                // 全部可见
                if ($group_id === 0) {
                    $count = PanDirector::where(['company_id' => $company_id, 'id' => $pan_director_id, 'open_set' => $open_set, 'type' => 1, 'status' => 1, 'group_id' => $group_id])->count();
                    if ($count > 0) {
                        return true;
                    }
                    // 同项目人员可见
                } elseif ($group_id > 0) {
                    if (Yunpan::isItem($user_id, $group_id)) {
                        $count = PanDirector::where(['company_id' => $company_id, 'id' => $pan_director_id, 'open_set' => $open_set, 'type' => 1, 'status' => 1, 'group_id' => $group_id])->count();
                        if ($count > 0) {
                            return true;
                        }
                    }
                }

            }
        }

        return false;
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
            'url' => $this->url_small,
            'user_id' => $this->user_id,
            'user_name' => $this->user->username,
            'group_id' => $this->group_id,
            'created_at' => $this->created_at,
        ];
    }

    /**
     * 资源缩略图访问修改器
     */
    public function getUrlSmallAttribute()
    {
        $auth = QiniuApi::auth();
        // 私有空间中的外链 http://<domain>/<file_key>
        $baseUrl = null;
        if (strpos($this->mime_type, 'image')) {
            $baseUrl = config('filesystems.disks.yunpan_qiniu.url') . $this->url . config('filesystems.disks.yunpan_qiniu.small');
        } else if (strpos($this->mime_type, 'video')) {
            $baseUrl = config('filesystems.disks.yunpan_qiniu.url') . $this->url . config('filesystems.disks.yunpan_qiniu.video_small');
        }

        // 对链接进行签名
        $signedUrl = $auth->privateDownloadUrl($baseUrl);
        return $signedUrl;
    }


}
