<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DateOfAwardTransformer;
use App\Models\DateOfAward;
use Illuminate\Http\Request;


class DateOfAwardController extends BaseController
{

    /**
     * @api {get} /dateOfAward 日期奖项详情
     * @apiVersion 1.0.0
     * @apiName DateOfAward dateOfAward
     * @apiGroup DateOfAward
     *
     * @apiParam {string} day 日期 //2017-01-11
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 1,
                "type": 1,
                "type_value": "设计大赛",
                "name": "设计大奖",
                "summary": "设计大奖",
                "start_time": "2017-11-01",
                "end_time": "2017-11-30"
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     *
     */
    public function show(Request $request)
    {
        $day = $request->input('day');

        $dateOfAwards = DateOfAward::where('start_time' , $day)->get();
        if (!$dateOfAwards) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($dateOfAwards, new DateOfAwardTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /dateOfAward/week 日期奖项周
     * @apiVersion 1.0.0
     * @apiName DateOfAward week
     * @apiGroup DateOfAward
     *
     * @apiParam {string} token token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": [
                {
                    "id": 1,
                    "type": 2,
                    "type_value": "节日",
                    "name": "设计奖",
                    "summary": "描述",
                    "start_time": "2017-12-10",
                    "end_time": "2017-12-02"
                },
                {
                    "id": 2,
                    "type": 2,
                    "type_value": "节日",
                    "name": "2test",
                    "summary": "2test",
                    "start_time": "2017-12-09",
                    "end_time": "2017-12-22"
                }
            ],
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function week()
    {
        //当前日期
        $sdefaultDate = date("Y-m-d");
        //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $first=1;
        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w=date('w',strtotime($sdefaultDate));
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $week_start=date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days'));
        //本周结束日期
        $week_end=date('Y-m-d',strtotime("$week_start +6 days"));

        $dateOfAward = DateOfAward::whereBetween('start_time' , [$week_start , $week_end])->orderBy('start_time','asc')->get();
        return $this->response->collection($dateOfAward, new DateOfAwardTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /dateOfAward/month 日期奖项月
     * @apiVersion 1.0.0
     * @apiName DateOfAward month
     * @apiGroup DateOfAward
     *
     * @apiParam {string} yearMonth 年月 //2017-01
     * @apiParam {string} token token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": [
                {
                    "id": 1,
                    "type": 2,
                    "type_value": "节日",
                    "name": "设计奖",
                    "summary": "描述",
                    "start_time": "2017-12-10",
                    "end_time": "2017-12-02"
                },
                {
                    "id": 2,
                    "type": 2,
                    "type_value": "节日",
                    "name": "2test",
                    "summary": "2test",
                    "start_time": "2017-12-09",
                    "end_time": "2017-12-22"
                }
            ],
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function month(Request $request)
    {
        $yearMonth = $request->input('yearMonth') ? $request->input('yearMonth') : date('Y-m');
        //月初
        $firstday = date($yearMonth.'-01', strtotime(date("Y-m-d")));
        //月末
        $lastday = date($yearMonth.'-d', strtotime("$firstday +1 month -1 day"));

        $dateOfAward = DateOfAward::whereBetween('start_time' , [$firstday , $lastday])->orderBy('start_time','asc')->get();
        return $this->response->collection($dateOfAward, new DateOfAwardTransformer())->setMeta($this->apiMeta());
    }

}

