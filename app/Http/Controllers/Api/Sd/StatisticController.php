<?php
/**
 * Created by PhpStorm.
 * User: cailiguang
 * Date: 2018/11/15
 * Time: 10:42 AM
 */
namespace App\Http\Controllers\Api\Sd;

use App\Models\Statistic;

class StatisticController extends BaseController
{
    /**
     * @api {get} /sd/tourist  游客数量
     * @apiVersion 1.0.0
     * @apiName statistics touristAdd
     * @apiGroup statistics
     */
    public function tourist()
    {
        $statistic = Statistic::find(1);
        if($statistic){
            $tourist = $statistic->tourist;
            $tourist += 1;
            $new_tourist = $tourist;
            $statistic->tourist = $new_tourist;
            $statistic->save();
            return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistic));
        }else{
            $statistic = new Statistic();
            $statistic->tourist = 1;
            $statistic->save();
            return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistic));
        }
    }

    /**
     * @api {get} /sd/willing  有志向数量
     * @apiVersion 1.0.0
     * @apiName statistics willingAdd
     * @apiGroup statistics
     */
    public function willing()
    {
        $statistic = Statistic::find(1);
        if($statistic){
            $willing = $statistic->willing;
            $willing += 1;
            $new_willing = $willing;
            $statistic->willing = $new_willing;
            $statistic->save();
            return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistic));
        }else{
            $statistic = new Statistic();
            $statistic->willing = 1;
            $statistic->save();
            return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistic));
        }
    }

}