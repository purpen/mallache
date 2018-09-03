<?php

namespace App\Models;

use App\Helper\QiniuApi;
use App\Helper\Yunpan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
     * 一对一关联recycle_bin 回收站
     */
    public function recycleBin()
    {
        return $this->hasOne('App\Models\RecycleBin', 'pan_director_id');
    }

    /**
     * 一对一关联pan_share 云盘分享
     */
    public function panShare()
    {
        return $this->hasOne('App\Models\PanShare', 'pan_director_id');
    }

    // 一对多相对关联项目表
    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id');
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
            'url_download' => $this->url_download,
            'user_id' => $this->user_id,
            'user_name' => $this->user ? $this->user->realname : null,
            'group_id' => json_decode($this->group_id),
            'created_at' => $this->created_at,
            'open_set' => $this->open_set,
            'width' => $this->width,
            'height' => $this->height,
//            'item_id' => $this->item_id,
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

    public function getUrlDownloadAttribute()
    {
        if ($this->type == 1) {
            return null;
        }

        $auth = QiniuApi::auth();
        // 私有空间中的外链 http://<domain>/<file_key>
        $baseUrl = config('filesystems.disks.yunpan_qiniu.url') . $this->url . '?attname=' . urlencode($this->name);
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
    public function setPermission($open_set, $group_id, $item_id, $user_id = null)
    {
        $this->open_set = $open_set;
        $this->group_id = $group_id;
        $this->item_id = $item_id;
        if ($user_id !== null) {
            $this->user_id = $user_id;
        }
        $this->save();
        if ($this->type == 2) {
            return;
        } else if ($this->type == 1) {
            $pan_dir_lists = PanDirector::where('pan_director_id', $this->id)->get();
            foreach ($pan_dir_lists as $pan_dir) {
                $pan_dir->setPermission($this->open_set, $this->group_id, $this->item_id);
            }
        }
    }

    /**
     * 文件、文件夹设置为私有
     */
    public function setPrivate()
    {
        $this->setPermission(2, null, null);
    }

    /**
     * 文件、文件夹设置为公开
     */
    public function setPublic()
    {
        $this->setPermission(1, null, null);
    }

    /**
     * 文件、文件夹设置为群组
     */
    public function setGroup(string $group_id)
    {
        $this->setPermission(1, $group_id, null);
    }

    /**
     * 递归的将文件、文件夹修改为删除中状态
     *
     * @param int $user_id 操作人
     */
    public function deletingDir()
    {
        if (2 == $this->status) {
            return false;
        }
        $this->status = 2;
        $this->save();
        if ($this->type == 2) {
            return true;
        } else if ($this->type == 1) {
            $pan_dir_lists = PanDirector::where('pan_director_id', $this->id)->get();
            foreach ($pan_dir_lists as $pan_dir) {
                $pan_dir->deletingDir();
            }
        }

        return true;
    }

    /**
     * 递归的将文件、文件夹修改为正常状态
     *
     * @param int $user_id 操作人
     */
    public function restoreDir()
    {
        $this->status = 1;
        $this->save();
        if ($this->type == 2) {
            return;
        } else if ($this->type == 1) {
            $pan_dir_lists = PanDirector::where('pan_director_id', $this->id)->get();
            foreach ($pan_dir_lists as $pan_dir) {
                $pan_dir->restoreDir();
            }
        }
    }


    /**递归的将文件、文件夹删除
     *
     * @return bool|null
     */
    public function deletedDir()
    {
        if ($this->type == 2) {
            $this->delete();
            // 源文件引用减一
            $this->panFile->fileCountDecrement();

        } else if ($this->type == 1) {
            $pan_dir_lists = PanDirector::where('pan_director_id', $this->id)->get();
            foreach ($pan_dir_lists as $pan_dir) {
                $pan_dir->deletedDir();
            }
            return $this->delete();
        }
    }

    /**
     * 文件查看、复制、移动、删除、分享、名称修改权限判定
     */
    public function isPermission(User $user)
    {
        // 系统生成文件不能删除
        if ($this->isAuto()) {
            return false;
        }

        // 文件是否是公开的
        if ($this->isPublic()) {
            return true;
        }

        // 文件是否私有
        if ($this->isPrivate($user->id)) {
            return true;
        }

        // 是否管理员
        if ($user->isDesignAdmin()) {
            return true;
        }

        // 是否是群组成员
        if ($this->isGroupPersonnel($user->id)) {
            return true;
        }

        // 是否是项目成员
        if ($this->isItemPersonnel($user->id)) {
            return true;
        }

        return false;
    }

    /**
     * 接收文件夹 复制、移动权限判定
     *
     * @param User $user
     * @return bool
     */
    public function isReceivePermission(User $user)
    {
        // 判断是否是文件夹
        if (!$this->isFolder()) {
            return false;
        }
        // 文件夹是否是公开的
        if ($this->isPublic()) {
            return true;
        }

        // 文件是否私有
        if ($this->isPrivate($user->id)) {
            return true;
        }

        // 用户是否管理员
        if ($user->isDesignAdmin()) {
            return true;
        }

        // 用户是否是群组成员
        if ($this->isGroupPersonnel($user->id)) {
            return true;
        }

        // 是否是项目成员
        if ($this->isItemPersonnel($user->id)) {
            return true;
        }

        return false;
    }

    /**
     * 判断文件文件夹是否是用户私有
     */
    public function isPrivate($user_id)
    {
        if ($this->open_set == 2 && $this->group_id == null && $this->item_id == null && $this->user_id == $user_id) {
            return true;
        }

        return false;
    }

    /**
     * 判断文件、文件夹是否公开
     */
    public function isPublic()
    {
        if ($this->open_set == 1 && $this->group_id == null && $this->item_id == null) {
            return true;
        }

        return false;
    }

    // 判断是否是项目成员
    public function isItemPersonnel($user_id)
    {
        $arr = ItemUser::getItemUserArr($this->item_id);
        if(in_array($user_id, $arr)){
            return true;
        }

        return false;
    }

    // 判断用户是否是文件群组成员
    public function isGroupPersonnel($user_id)
    {
        if ($this->open_set == 1 && $this->group_id !== null && $this->item_id == null) {
            $user_group_id_list = Group::userGroupIDList($user_id);
            if (!empty(array_intersect(json_decode($this->group_id, true), $user_group_id_list))) {
                return true;
            }
        }

        return false;
    }


    /**
     * 判断是否是文件夹
     * @return bool
     */
    public function isFolder()
    {
        return 1 == $this->type;
    }

    /**
     * 复制文件及其下级文件，返回复制的文件对象
     *
     * @param int $pid 上级文件夹ID
     * @return PanDirector
     */
    public function copyDir(int $pid)
    {
        $pan_director = new PanDirector();
        $pan_director->open_set = $this->open_set;
        $pan_director->group_id = $this->group_id;
        $pan_director->company_id = $this->company_id;
        $pan_director->pan_director_id = $pid;
        $pan_director->type = $this->type;
        $pan_director->name = $this->name;
        $pan_director->size = $this->size;
        $pan_director->sort = $this->sort;
        $pan_director->mime_type = $this->mime_type;
        $pan_director->pan_file_id = $this->pan_file_id;
        $pan_director->user_id = $this->user_id;
        $pan_director->status = $this->status;
        $pan_director->url = $this->url;
        $pan_director->width = $this->width;
        $pan_director->height = $this->height;
        $pan_director->save();
        if ($this->isFolder()) {
            $pan_dir_lists = PanDirector::where('pan_director_id', $this->id)->get();
            foreach ($pan_dir_lists as $pan_dir) {
                // 递归调用
                $pan_dir->copyDir($pan_director->id);
            }
        } else {
            $this->panFile->fileCountIncrement();
        }

        return $pan_director;
    }

    /**
     *  判断文件夹下是否有同名文件
     *
     * @param $pan_director_id integer 上级文件夹ID
     * @param $name  string 文件名
     * @param $user_id integer 用户ID
     * @return bool
     */
    public static function isSameFile($pan_director_id, $name, $user_id)
    {
        Log::info([$name, $user_id]);
        $pan_dir = PanDirector::query()
            ->where(function ($query) use ($pan_director_id, $name) {
                $query->where('pan_director_id', $pan_director_id)
                    ->where('name', trim($name))
                    ->where('open_set', 1)
                    ->where('status', 1);
            })
            ->orWhere(function ($query) use ($pan_director_id, $name, $user_id) {
                $query->where('pan_director_id', $pan_director_id)
                    ->where('name', trim($name))
                    ->where('open_set', 2)
                    ->where('user_id', $user_id)
                    ->where('status', 1);
            })
            ->first();

        if ($pan_dir) {
            return true;
        } else {
            return false;
        }
    }


    /**
     *  验证当前文件是否是传入的文件ID的下属文件或本身
     *
     * @param array $arr_id 文件ID数组
     * @return bool
     */
    public function isChild(array $arr_id)
    {
        if (in_array($this->id, $arr_id)) {
            return true;
        }
        $pid = $this->pan_director_id;
        while ($pid > 0) {
            if (in_array($pid, $arr_id)) {
                return true;
            }
            $pan_dir = PanDirector::find($pid);
            $pid = $pan_dir->pan_director_id;
        }

        return false;
    }


    /**
     * 创建项目文件夹根目录并将目录ID写入设计公司信息表
     *
     * @param DesignCompanyModel $company
     * @param int $user_id
     * @return PanDirector
     */
    public static function projectBaseDir(DesignCompanyModel &$company)
    {
        $pan_director = new PanDirector();
        $pan_director->open_set = 1;
        $pan_director->group_id = null;
        $pan_director->company_id = $company->id;
        $pan_director->pan_director_id = 0;
        $pan_director->type = 1;
        $pan_director->name = '项目';
        $pan_director->size = 0;
        $pan_director->sort = 0;
        $pan_director->mime_type = '';
        $pan_director->pan_file_id = 0;
        $pan_director->user_id = 0;
        $pan_director->status = 1;
        $pan_director->url = '';
        $pan_director->is_auto = 2;
        $pan_director->save();

        $company->project_dir_id = $pan_director->id;
        $company->save();

        return $pan_director;
    }


    /**
     * 系统创建新建项目云盘文件夹
     *
     * @param DesignCompanyModel $company
     * @param DesignProject $design_project
     * @param int $user_id
     * @return PanDirector
     */
    public static function createProjectDir(DesignCompanyModel &$company, DesignProject &$design_project)
    {
        if ($company->project_dir_id === null) {
            PanDirector::projectBaseDir($company);
        }

        $pan_director = new PanDirector();
        $pan_director->open_set = 1;
        $pan_director->group_id = null;
        $pan_director->company_id = $company->id;
        $pan_director->pan_director_id = $company->project_dir_id;
        $pan_director->type = 1;
        $pan_director->name = $design_project->name;
        $pan_director->size = 0;
        $pan_director->sort = 0;
        $pan_director->mime_type = '';
        $pan_director->pan_file_id = 0;
        $pan_director->user_id = 0;
        $pan_director->status = 1;
        $pan_director->url = '';
        $pan_director->item_id = $design_project->id;
        $pan_director->is_auto = 2;
        $pan_director->save();

        // 将新建的云盘文件夹ID 写入项目表
        $design_project->pan_director_id = $pan_director->id;
        $design_project->save();

        return $pan_director;
    }

}
