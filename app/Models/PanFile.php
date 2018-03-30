<?php

namespace App\Models;

use App\Helper\QiniuApi;
use Illuminate\Database\Eloquent\Model;

class PanFile extends BaseModel
{
    protected $table = 'pan_file';

    /**
     * 一对多关联文件层级表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function panDirector()
    {
        return $this->hasMany('App\Models\PanDirector', 'pan_file_id');
    }

    /**
     * 根据MD5值查询源文件
     *
     * @param string $md5
     * @return Model|null|static
     */
    static public function getFlie(string $md5)
    {
        $file = PanFile::where('md5', $md5)->first();
        if (!$file) {
            return null;
        }
        return $file;
    }

    /**
     *源文件引用加1
     */
    public function fileCountIncrement()
    {
        $this->increment('count');
    }

    /**
     * 源文件引用减1
     */
    public function fileCountDecrement()
    {
        $this->decrement('count');

        // 如果引用为零
        if ($this->count == 0) {
            $this->delete();

            // 删除七牛中的云盘文件
            QiniuApi::yunPanDelete($this->url);
        }
    }


}
