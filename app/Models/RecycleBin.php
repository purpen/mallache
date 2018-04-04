<?php

namespace App\Models;


class RecycleBin extends BaseModel
{
    protected $table = 'recycle_bin';

    /**
     * 一对一相对关联文件层级表
     */
    public function panDirector()
    {
        return $this->belongsTo('App\Models\PanDirector', 'pan_director_id');
    }

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * 添加回收站记录
     *
     * @param PanDirector $panDirector 文件层级对象
     * @param int $user_id 操作用户ID
     * @return bool
     */
    public static function addRecycle(PanDirector $panDirector, int $user_id)
    {
        $recycle_bin = new RecycleBin();
        $recycle_bin->pan_director_id = $panDirector->id;
        $recycle_bin->type = $panDirector->type;
        $recycle_bin->name = $panDirector->name;
        $recycle_bin->size = $panDirector->size;
        $recycle_bin->mime_type = $panDirector->mime_type;
        $recycle_bin->user_id = $user_id;
        $recycle_bin->company_id = $panDirector->company_id;
        return $recycle_bin->save();
    }

    /**
     * 彻底删除文件（文件夹）并删除回收站记录
     */
    public function deleteRecycle()
    {
        if (!$this->panDirector) {
            return $this->delete();
        } else if ($this->panDirector->deletedDir() === false || $this->delete() === false) {
            return false;
        }

        return true;
    }

    /**
     * 恢复文件（文件夹）并删除回收站记录
     */
    public function restoreRecycle()
    {
        if (!$this->panDirector) {
            return $this->delete();
        }

        if ($this->panDirector->restoreDir() === false || $this->delete() === false) {
            return false;
        }
        return true;
    }


    /**
     * 文件详细信息
     *
     */
    public function info()
    {
        return [
            'id' => $this->id,
            'pan_director_id' => $this->pan_director_id,
            'type' => $this->type,
            'name' => $this->name,
            'size' => $this->size,
            'mime_type' => $this->mime_type,
            'user_id' => $this->user_id,
            'user_name' => $this->user->realname,
            'created_at' => $this->created_at,
        ];
    }
}
