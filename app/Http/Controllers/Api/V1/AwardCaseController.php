<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\AdminTransformer\AwardCaseListTransformer;
use App\Http\AdminTransformer\AwardCaseTransformer;
use App\Models\AssetModel;
use App\Models\AwardCase;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AwardCaseController extends BaseController
{
    /**
     * @api {post} /awardCase 添加奖项案例
     * @apiVersion 1.0.0
     * @apiName awardCase awardCaseStore
     * @apiGroup AwardCase
     *
     * @apiParam {string} title *标题
     * @apiParam {integer} category_id *所属奖项ID
     * @apiParam {string} summary 简述
     * @apiParam {text} content 内容
     * @apiParam {string} url 链接
     * @apiParam {string} grade 奖项等级
     * @apiParam {string} time_at 获奖时间
     * @apiParam {string} images_url 多个图片链接用@@分隔
     * @apiParam {string} auth_code 接口验证标识
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
        $auth_code = $request->input('auth_code') ? $request->input('auth_code') : '';
        if (empty($auth_code) || $auth_code != 'tHnD3IN2017') {
            return $this->response->array($this->apiError('授权失败！', 401));
        }
        $rules = [
            'title' => 'required|max:30',
            'category_id' => 'required|integer',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        // 验证是否有重复
        $hasOne = AwardCase::where(['title'=>$all['title'], 'category_id'=>$all['category_id']])->first();
        if ($hasOne) {
            return $this->response->array($this->apiError('不能重复添加！', 402));       
        }
        if (!$awardCase = AwardCase::create($all)) {
            return $this->response->array($this->apiError('添加失败', 500));
        }

        if($random = $request->input('random')){
            // AssetModel::setRandom($awardCase->id, $random);
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /awardCase 奖项案例详情
     * @apiVersion 1.0.0
     * @apiName awardCase awardCaseShow
     * @apiGroup AwardCase
     *
     * @apiParam {integer} id 文章id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "data": {
     *          "type": 1,
     *          "category_id": 2,
     *          "category_name": "红点奖",
     *          "title": "产品名称",
     *          "summary": "产品简介",
     *          "user_id": 255, // 创建人ID
     *          "content": "产品内容",
     *          "url": "www.baidu.com",
     *          "grade": "金奖",
     *          "time_at": "2017-12-12",
     *          "recommended": 1,             // 是否推荐：0.否；1.是；
     *          "recommended_on": 119288392,  // 推荐时间
     *          "status": 0,
     *          "cover_id": 1,
     *          "cover": null,
     *          "images": []
     *      },
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function show(Request $request)
    {
        $auth_code = $request->input('auth_code') ? $request->input('auth_code') : '';
        if (empty($auth_code) || $auth_code != 'tHnD3IN2017') {
            return $this->response->array($this->apiError('授权失败！', 401));
        }
        $id = $request->input('id');

        $awardCase = AwardCase::find($id);
        if (!$awardCase) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($awardCase, new AwardCaseTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /awardCase/list 奖项案例列表
     * @apiVersion 1.0.0
     * @apiName awardCase awardCaseList
     * @apiGroup AwardCase
     *
     * @apiParam {integer} type 类型；0.全部；
     * @apiParam {integer} category_id 奖项分类：0.全部；1.--; 2.--;
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
        $type = $request->input('type') ? (int)$request->input('type') : 0;
        $status = $request->input('status') ? (int)$request->input('status') : 0;
        $category_id = $request->input('category_id') ? (int)$request->input('category_id') : 0;

        $auth_code = $request->input('auth_code') ? $request->input('auth_code') : '';
        if (empty($auth_code) || $auth_code != 'tHnD3IN2017') {
            return $this->response->array($this->apiError('授权失败！', 401));
        }

        $query = array();
        if ($type) $query['type'] = $type;
        if ($category_id) $query['category_id'] = $category_id;
        if ($status) $query['status'] = $status;

        $lists = AwardCase::where($query)
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return $this->response->paginator($lists, new AwardCaseListTransformer)->setMeta($this->apiMeta());
    }

}
