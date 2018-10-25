<?php

namespace App\Models;

use App\Models\AssetModel;

class DesignResult extends BaseModel
{
    /**
     * 关联模型到设计成果表
     *
     * @var string
     */
    protected $table = 'design_result';
    protected $datas = ['deleted_at'];

    /**
     * 修改设计成果关注数量
     * @author 王松
     * @param $id 设计成果ID
     * @param $type 类型 1:增加,2:减少
     * @return bool
     */
    public function save_follow_count($id,$type)
    {
        $design_result = $this->find($id);
        if(!empty($design_result)){
            if($type == 1){
                $design_result->follow_count = $design_result->follow_count + 1;
                return $design_result->save();
            }elseif($type == 2){
                if($design_result->follow_count >= 0){
                    $design_result->follow_count = 0;
                    return true;
                }else{
                    $design_result->follow_count = $design_result->follow_count - 1;
                }
                return $design_result->save();
            }else{
                return false;
            }
        }
        return false;
    }

    /**
     * 常用网站封面图
     */
    public function getCoverAttribute()
    {
        return AssetModel::getOneImage($this->cover_id) ?? AssetModel::getOneImageUrl($this->id, 18, 1);
    }
}
