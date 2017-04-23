<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetModel extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * 关联模型到数据表
     * @var string
     */
    protected $table = 'assets';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = ['user_id','name','random','size','width','height','mime','domain','path','target_id','type'];

    /**
     * 获取图片列表
     *
     * @param integer $target_id  目标ID
     * @param integer $type 图片类型 附件类型: 1.默认；2.用户头像；3.企业法人营业执照；4.需求项目设计附件；5.案例图片；6.设计公司logo
     * @param int $sort  排序（可选，默认倒序）：1.倒序；2.正序；
     * @param null $limit 数量（可选）：获取数量
     * @return array
     */
    public static function getImageUrl(int $target_id, int $type, int $sort = 1, int $limit = null) :array
    {
        if($sort === 1){
            $sort = 'desc';
        }else{
            $sort = 'asc';
        }

        $query = self::select('id','path')
                    ->where(['target_id' => $target_id, 'type' => $type])
                    ->orderBy('id', $sort);
        if($limit !== null){
            $query = $query->limit($limit);
        }
        $assets = $query->get();

        $images = [];
        foreach($assets as $asset)
        {
            $images[] = [
                'id' => $asset->id,
                'name' => $asset->name,
                'file' => config('filesystems.disks.qiniu.url') . $asset->path,
                'small' => config('filesystems.disks.qiniu.url') . $asset->path . config('filesystems.disks.qiniu.small'),
                'big' => config('filesystems.disks.qiniu.url') . $asset->path . config('filesystems.disks.qiniu.big'),
                'logo' => config('filesystems.disks.qiniu.url') . $asset->path . config('filesystems.disks.qiniu.logo'),
            ];
        }

        return $images;
    }

    /**
     * @param integer $id 目标ID
     * @param string $random 随机数值
     * @return bool
     */
    public static function setRandom($id, $random)
    {
        $assets = AssetModel::select('id')
            ->where('random', $random)
            ->get();

        if($assets->isEmpty()){
            return true;
        }

        foreach($assets as $asset){
            $asset->target_id = $id;
            $asset->save();
        }

        return true;
    }

}
