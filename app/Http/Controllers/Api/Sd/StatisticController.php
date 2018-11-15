<?php
/**
 * Created by PhpStorm.
 * User: cailiguang
 * Date: 2018/11/15
 * Time: 10:42 AM
 */
namespace App\Http\Controllers\Api\Sd;

use App\Models\Statistic;
use Illuminate\Http\Request;

class StatisticController extends BaseController
{
    /**
     * @api {get} /sd/touristWilling  游客,志向数量
     * @apiVersion 1.0.0
     * @apiName statistics touristWilling
     * @apiGroup statistics
     *
     * @apiParam {integer} type 默认1 1.游客 2.有志向
     */
    public function touristWilling(Request $request)
    {
        $type = $request->input('type') ?? 1;
        if ($type == 1) {
            $statistic = Statistic::find(1);
            if ($statistic) {
                $tourist = $statistic->tourist_count;
                $tourist += 1;
                $new_tourist = $tourist;
                $statistic->tourist_count = $new_tourist;
                $statistic->save();
                return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistic));
            } else {
                $statistic = new Statistic();
                $statistic->tourist_count = 1;
                $statistic->save();
                return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistic));
            }
        } else {
            $statistic = Statistic::find(1);
            if ($statistic) {
                $willing = $statistic->willing_count;
                $willing += 1;
                $new_willing = $willing;
                $statistic->willing_count = $new_willing;
                $statistic->save();
                return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistic));
            } else {
                $statistic = new Statistic();
                $statistic->willing_count = 1;
                $statistic->save();
                return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistic));
            }
        }

    }

    /**
     * @api {get} /sd/statistics  展示
     * @apiVersion 1.0.0
     * @apiName statistics index
     * @apiGroup statistics
     */
    public function index()
    {
        $statistic = Statistic::find(1);
        if(!$statistic){
            $statistic['tourist_count'] = 0;
            $statistic['willing_count'] = 0;
        }
        return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistic));

    }

}