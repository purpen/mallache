<?php

namespace App\Http\Controllers\Api\Admin;



use App\Http\AdminTransformer\AdminTrendReportsTransformer;
use App\Models\AssetModel;
use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\TrendReports;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrendReportsController extends BaseController
{
    /**
     * @api {post} /admin/trendReports 添加趋势报告
     * @apiVersion 1.0.0
     * @apiName trendReports trendReportsStore
     * @apiGroup AdminTrendReports
     *
     * @apiParam {string} title 趋势报告标题
     * @apiParam {integer} cover_id 封面图ID
     * @apiParam {integer} pdf_id pdf文件ID
     * @apiParam {array} tag 标签
     * @apiParam {string} summary 描述
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
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|max:100',
            'tag' => 'required',
            'cover_id' => 'integer',
            'pdf_id' => 'integer',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $all['status'] = 0;
        $tags = $request->input('tag') ? $request->input('tag') : [];
        if(!empty($tags)){
            $all['tag'] = implode(',' , $tags);
        }else{
            $all['tag'] = '';
        }
        $all['user_id'] = $this->auth_user_id;
        $all['summary'] = $request->input('summary') ? $request->input('summary') : '';
        if (!$trendReports = TrendReports::create($all)) {
            return $this->response->array($this->apiError('添加失败', 500));
        }

        if($random = $request->input('random')){
            AssetModel::setRandom($trendReports->id, $random);
        }


        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/trendReports 更新趋势报告
     * @apiVersion 1.0.0
     * @apiName trendReports trendReportsUpdate
     * @apiGroup AdminTrendReports
     *
     * @apiParam {integer} id 趋势报告ID
     * @apiParam {string} title 趋势报告标题
     * @apiParam {integer} cover_id 封面图ID
     * @apiParam {integer} pdf_id pdf文件ID
     * @apiParam {array} tag 标签
     * @apiParam {string} summary 描述
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
    public function update(Request $request)
    {
        $rules = [
            'title' => 'required|max:100',
            'cover_id' => 'integer',
            'pdf_id' => 'integer',
            'tag' => 'required',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $tags = $request->input('tag') ? $request->input('tag') : [];
        if(!empty($tags)){
            $all['tag'] = implode(',' , $tags);
        }else{
            $all['tag'] = '';
        }
        $all['summary'] = $request->input('summary') ? $request->input('summary') : '';

        $trendReports = TrendReports::find($request->input('id'));
        if (!$trendReports) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$trendReports = $trendReports->update($all)) {
            return $this->response->array($this->apiError('更新失败', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /admin/trendReports 趋势报告详情
     * @apiVersion 1.0.0
     * @apiName trendReports trendReports
     * @apiGroup AdminTrendReports
     *
     * @apiParam {integer} id 趋势报告id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "data": {
     *          "title": "这是第一篇",
     *          "cover_id": 1,
     *          "image": []
     *      },
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function show(Request $request)
    {
        $id = $request->input('id');

        $trendReports = TrendReports::find($id);
        if (!$trendReports) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($trendReports, new AdminTrendReportsTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /admin/trendReports/lists 趋势报告列表
     * @apiVersion 1.0.0
     * @apiName trendReports trendReportsLists
     * @apiGroup AdminTrendReports
     *
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
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
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;

        $query = TrendReports::query();

        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new AdminTrendReportsTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /admin/trendReports 趋势报告删除
     * @apiVersion 1.0.0
     * @apiName trendReports delete
     * @apiGroup AdminTrendReports
     *
     * @apiParam {integer} id 趋势报告ID
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
        TrendReports::destroy($id);

        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {put} /admin/trendReports/verifyStatus 启用/禁用
     * @apiVersion 1.0.0
     * @apiName trendReports verifyStatus
     * @apiGroup AdminTrendReports
     *
     * @apiParam {integer} id
     * @apiParam {integer} status 状态 0.禁用 1.启用
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
    public function verifyStatus(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'status' => 'required|integer',
        ]);

        $works = TrendReports::find($request->input('id'));
        if (!$works) {
            return $this->response->array($this->apiSuccess('not found', 404));
        }

        if ($request->input('status')) {
            $works->status = 1;
        } else {
            $works->status = 0;
        }
        $works->save();

        return $this->response->array($this->apiSuccess());
    }

}