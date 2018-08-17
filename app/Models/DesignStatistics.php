<?php

namespace App\Models;


class DesignStatistics extends BaseModel
{
    /**
     * 关联模型到数据表
     *
     * @var string
     */
    protected $table = 'design_statistics';

    /**
     * 一对一关联设计公司
     */
    public function companyName()
    {
        return $this->hasOne('App\Models\DesignCompanyModel','id','design_company_id');
    }

    /**
     * saveRecommendedTimes        更新设计公司推荐次数
     *
     * @author yuluo
     * @params 设计公司id
     * @return true|false
     */
    public function saveRecommendedTimes($params)
    {
        $data = $this->select('id','recommend_count')->where(['design_company_id'=>$params])->first();
        if(empty($data)){
            $res = $this->insert(['recommend_count'=>1,'design_company_id'=>$params]);
            if(empty($res)){
                return false;
            }
        }
        $data = json_decode($data,1);
        $list['recommend_count'] = (int)$data['recommend_count'] + 1;
        $res = $this->where(['id'=>$data['id']])->update($list);
        if($res > 0){
            return true;
        }
        return false;
    }

    /**
     * saveDesignInfo       新增设计公司信息
     *
     * @author yuluo
     * @params $id     int  设计公司id
     * @return boolean      1|0
     */
    public function saveDesignInfo($id)
    {
        $data = $this::where(['design_company_id'=>$id])->first();
        if(empty($data)){
            $res = $this::insert(['design_company_id'=>$id]);
            if(empty($res)){
                return false;
            }
        }
        return true;
    }
}
