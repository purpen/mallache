<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetModel extends BaseModel
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
    protected $fillable = ['user_id', 'name', 'random', 'size', 'width', 'height', 'mime', 'domain', 'path', 'target_id', 'type'];

    /**
     * 获取图片列表
     *
     * @param integer $target_id 目标ID
     * @param integer $type 图片类型 附件类型: 1.默认；2.用户头像；3.企业法人营业执照；4.需求项目设计附件；5.案例图片；6.设计公司logo;7.需求公司logo；8.项目阶段附件;9.需求公司附件;10.证件图片; 12.栏目位封面；13.文章封面； 14.文章编辑器；15.大赛； 16.趋势报告封面；17.趋势报告pdf; 18.常用网站图片；25.奖项案例; 26.奖项案例编辑器； 28.系统通知图; 30.报价单附件 31.阶段节点附件 32.线下合同文件
     * @param int $sort 排序（可选，默认倒序）：1.倒序；2.正序；
     * @param null $limit 数量（可选）：获取数量
     * @return array
     */
    public static function getImageUrl(int $target_id, int $type, int $sort = 1, int $limit = null): array
    {
        if ($sort === 1) {
            $sort = 'desc';
        } else {
            $sort = 'asc';
        }

        $query = self::select('id', 'path', 'name', 'created_at', 'summary', 'mime' , 'size')
            ->where(['target_id' => $target_id, 'type' => $type])
            ->orderBy('id', $sort);
        if ($limit !== null) {
            $query = $query->limit($limit);
        }
        $assets = $query->get();

        $images = [];
        foreach ($assets as $asset) {
            $images[] = AssetModel::assetFormat($asset);
        }

        return $images;
    }

    /**
     *  返回格式
     *
     * @param AssetModel $asset
     * @return array
     */
    public static function assetFormat(AssetModel $asset)
    {
        return [
            'id' => $asset->id,
            'name' => $asset->name,
            'created_at' => $asset->created_at,
            'summary' => $asset->summary,
            'size' => $asset->size,
            'file' => config('filesystems.disks.qiniu.url') . $asset->path,
            'small' => config('filesystems.disks.qiniu.url') . $asset->path . config('filesystems.disks.qiniu.small'),
            'big' => config('filesystems.disks.qiniu.url') . $asset->path . config('filesystems.disks.qiniu.big'),
            'logo' => config('filesystems.disks.qiniu.url') . $asset->path . config('filesystems.disks.qiniu.logo'),
            'middle' => config('filesystems.disks.qiniu.url') . $asset->path . config('filesystems.disks.qiniu.middle'),
        ];
    }

    /**
     * 获取第一张图片
     *
     * @param integer $target_id 目标ID
     * @param integer $type 图片类型 附件类型: 1.默认；2.用户头像；3.企业法人营业执照；4.需求项目设计附件；5.案例图片；6.设计公司logo;7.需求公司logo；8.项目阶段附件;9.需求公司附件;10.证件图片; 12.栏目位；15.大赛作品；15.--
     * @param int $sort 排序（可选，默认倒序）：1.倒序；2.正序；
     * @return
     */
    public static function getOneImageUrl(int $target_id, int $type, int $sort = 1)
    {
        if ($sort === 1) {
            $sort = 'desc';
        } else {
            $sort = 'asc';
        }

        $asset = self::select('id', 'path', 'name', 'created_at', 'summary', 'mime' , 'size')
            ->where(['target_id' => $target_id, 'type' => $type])
            ->orderBy('id', $sort)
            ->first();

        if (empty($asset)) {
            return null;
        }

        return AssetModel::assetFormat($asset);
    }

    //根据ID查询附件信息
    public static function getOneImage($id)
    {
        if (!$asset = self::find($id)) {
            return null;
        }

        return AssetModel::assetFormat($asset);
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

        if ($assets->isEmpty()) {
            return true;
        }

        foreach ($assets as $asset) {
            $asset->target_id = $id;
            $asset->save();
        }

        return true;
    }

    /**
     * 获取附件ID
     *
     * @param int $type 附件类型
     * @param string $random 随机数
     * @return int 附件ID
     */
    public function getAssetId(int $type, string $random)
    {
        $asset = AssetModel::where(['type' => $type, 'random' => $random])->first();
        if ($asset) {
            return $asset->id;
        } else {
            return 0;
        }
    }

}
