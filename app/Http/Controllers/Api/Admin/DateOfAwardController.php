<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\DateOfAwardTransformer;
use App\Models\DateOfAward;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class DateOfAwardController extends BaseController
{

    /**
     * @api {get} /admin/dateOfAward/list 日历列表
     * @apiVersion 1.0.0
     * @apiName AdminDateOfAward index
     * @apiGroup AdminDateOfAward
     *
     * @apiParam {integer} status 状态 -1.禁用；0.全部；1.启用；
     * @apiParam {integer} type 分类
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {string} token
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
              "status_code": 200,
              "pagination": {
                "total": 2,
                "count": 2,
                "per_page": 10,
                "current_page": 1,
                "total_pages": 1,
                "links": []
            }
        }
     */
    public function index(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type = $request->input('type') ? (int)$request->input('type') : 0;
        $status = $request->input('status') ? (int)$request->input('status') : 0;
        $kind = $request->input('kind') ? (int)$request->input('kind') : 0;

        switch ($status) {
            case -1:
                $status = 0;
                break;
            case 0:
                $status = null;
                break;
            case 1:
                $status = 1;
                break;
            default:
                $status = 1;
        }

        $query = array();
        if ($type) $query['type'] = $type;
        if ($status) $query['status'] = $status;

        $lists = DateOfAward::where($query)
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return $this->response->paginator($lists, new DateOfAwardTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {post} /admin/dateOfAward 添加日期奖项
     * @apiVersion 1.0.0
     * @apiName AdminDateOfAward store
     * @apiGroup AdminDateOfAward
     *
     * @apiParam {integer} type 状态 1.设计大赛；2.节日；3.展会；4.事件
     * @apiParam {string} name 名称
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiParam {string} summary 描述
     * @apiParam {string} url 地址
     * @apiParam {string} token token
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|integer',
            'name' => 'required|max:50',
            'summary' => 'required|max:500',
            'start_time' => 'required',
            'end_time' => 'required',

        ];

        $user_id = $this->auth_user_id;
        $params = array(
            'name' => $request->input('name'),
            'summary' => $request->input('summary'),
            'user_id' => $user_id,
            'type' => $request->input('type'),
            'url' => $request->input('url'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
        );

        $validator = Validator::make($params, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if (!$dateOfAward = DateOfAward::create($params)) {
            return $this->response->array($this->apiError('添加失败', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /admin/dateOfAward 日期奖项详情
     * @apiVersion 1.0.0
     * @apiName AdminDateOfAward dateOfAward
     * @apiGroup AdminDateOfAward
     *
     * @apiParam {integer} id 日期奖项id
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
        $id = $request->input('id');

        $dateOfAward = DateOfAward::where('id' , $id)->first();
        if (!$dateOfAward) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($dateOfAward, new DateOfAwardTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {put} /admin/dateOfAward 更改日期奖项
     * @apiVersion 1.0.0
     * @apiName AdminDateOfAward update
     * @apiGroup AdminDateOfAward
     *
     * @apiParam {integer} id 项目奖项id
     * @apiParam {integer} type 状态 1.设计大赛；2.节日；3.展会；4.事件
     * @apiParam {string} name 名称
     * @apiParam {string} start_time 开始时间
     * @apiParam {string} end_time 结束时间
     * @apiParam {string} summary 描述
     * @apiParam {string} token token
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function update(Request $request)
    {
        $rules = [
            'type' => 'required|integer',
            'name' => 'required|max:50',
            'summary' => 'required|max:500',
            'start_time' => 'required',
            'end_time' => 'required',

        ];

        $params = array(
            'name' => $request->input('name'),
            'summary' => $request->input('summary'),
            'type' => $request->input('type'),
            'url' => $request->input('url'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'content' => $request->input('content'),
        );

        $validator = Validator::make($params, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $dateOfAward = DateOfAward::find($request->input('id'));
        if (!$dateOfAward) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$dateOfAward->update($params)) {
            return $this->response->array($this->apiError('更新失败', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /admin/dateOfAward/week 日期奖项周
     * @apiVersion 1.0.0
     * @apiName AdminDateOfAward week
     * @apiGroup AdminDateOfAward
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
     * @api {get} /admin/dateOfAward/month 日期奖项月
     * @apiVersion 1.0.0
     * @apiName AdminDateOfAward month
     * @apiGroup AdminDateOfAward
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

    /**
     * @api {delete} /admin/dateOfAward 删除日期奖项
     * @apiVersion 1.0.0
     * @apiName AdminDateOfAward delete
     * @apiGroup AdminDateOfAward
     *
     * @apiParam {integer} id 日期奖项ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function delete(Request $request)
    {
        $id = (int)$request->input('id');

        if(!DateOfAward::destroy($id)){
            return $this->response->array($this->apiError('删除失败', 412));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/dateOfAward/changeStatus 变更状态
     * @apiVersion 1.0.0
     * @apiName AdminDateOfAward changeStatus
     * @apiGroup AdminDateOfAward
     *
     * @apiParam {integer} id ID
     * @apiParam {integer} status 状态 0.禁用；1.启用；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function changeStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status') ? (int)$request->input('status') : 0;

        $award = DateOfAward::find($id);
        if (!$award) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $award->status = $status;
        if (!$award->save()) {
            return $this->response->array($this->apiError('Error', 500));
        } else {
            return $this->response->array($this->apiSuccess());
        }
    }

}

