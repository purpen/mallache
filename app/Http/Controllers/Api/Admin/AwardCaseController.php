<?php

namespace App\Http\Controllers\Api\Admin;


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
     * @api {post} /admin/awardCase 添加奖项案例
     * @apiVersion 1.0.0
     * @apiName awardCase awardCaseStore
     * @apiGroup AdminAwardCase
     *
     * @apiParam {string} title *标题
     * @apiParam {string} summary 简述
     * @apiParam {text} content 内容
     * @apiParam {string} url 链接
     * @apiParam {string} tags 标签
     * @apiParam {string} grade 奖项等级
     * @apiParam {string} time_at 获奖时间
     * @apiParam {integer} category_id 所属奖项ID
     * @apiParam {integer} cover_id 封面图ID
     * @apiParam {string} images_url 多个图片链接用@@分隔
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
            'category_id' => 'required|integer',
            'cover_id' => 'required|integer',
            'tags' => 'array',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $all['user_id'] = $this->auth_user_id;
        $all['tags'] = $request->input('tags') ? implode(',', $request->input('tags')) : '';
        if (!$awardCase = AwardCase::create($all)) {
            return $this->response->array($this->apiError('添加失败', 500));
        }

        if($random = $request->input('random')){
            AssetModel::setRandom($awardCase->id, $random);
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/awardCase 更新奖项案例
     * @apiVersion 1.0.0
     * @apiName awardCase awardCaseUpdate
     * @apiGroup AdminAwardCase
     *
     * @apiParam {integer} id 文章ID
     * @apiParam {string} title *标题
     * @apiParam {string} summary 简述
     * @apiParam {text} content 内容
     * @apiParam {string} url 链接
     * @apiParam {string} tags 标签
     * @apiParam {string} grade 奖项等级
     * @apiParam {string} time_at 获奖时间
     * @apiParam {integer} category_id 所属奖项ID
     * @apiParam {integer} cover_id 封面图ID
     * @apiParam {string} images_url 多个图片链接用@@分隔
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
            'id' => 'required|integer',
            'title' => 'required|max:100',
            'category_id' => 'required|integer',
            'cover_id' => 'required|integer',
            'tags' => 'array',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $awardCase = AwardCase::find($request->input('id'));
        if (!$awardCase) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $all['tags'] = $request->input('tags') ? implode(',', $request->input('tags')) : '';
        if (!$awardCase = $awardCase->update($all)) {
            return $this->response->array($this->apiError('更新失败', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /admin/awardCase 奖项案例详情
     * @apiVersion 1.0.0
     * @apiName awardCase awardCaseShow
     * @apiGroup AdminAwardCase
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
     *          "tags": [
     *            "美丽",
     *            "智慧"
     *          ],
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
        $id = $request->input('id');

        $awardCase = AwardCase::find($id);
        if (!$awardCase) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($awardCase, new AwardCaseTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /admin/awardCase/list 奖项案例列表
     * @apiVersion 1.0.0
     * @apiName awardCase awardCaseList
     * @apiGroup AdminAwardCase
     *
     * @apiParam {integer} type 类型: 0.全部；；
     * @apiParam {integer} category_id 奖项类型: 0.全部；
     * @apiParam {integer} status 状态 -1.未发布；0.全部；1.发布；
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

        $query = array();
        if ($type) $query['type'] = $type;
        if ($category_id) $query['category_id'] = $category_id;
        if ($status) {
            if ($status === -1) {
                $query['status'] = 0;
            } else {
                $query['status'] = 1;
            }
        }

        $lists = AwardCase::where($query)
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return $this->response->paginator($lists, new AwardCaseListTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /admin/awardCase/changeStatus 奖项案例变更状态
     * @apiVersion 1.0.0
     * @apiName awardCase awardCaseChangeStatus
     * @apiGroup AdminAwardCase
     *
     * @apiParam {integer} id ID
     * @apiParam {integer} status 状态 0.未发布；1.发布；
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
        $id = $request->input("id");
        $status = $request->input("status");

        $awardCase = AwardCase::find($id);
        if (!$awardCase) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($status) {
            $awardCase->status = 1;
        } else {
            $awardCase->status = 0;
        }
        if (!$awardCase->save()) {
            return $this->response->array($this->apiError('Error', 500));
        } else {
            return $this->response->array($this->apiSuccess());
        }
    }

    /**
     * @api {put} /admin/awardCase/changeRecommended 奖项案例推荐操作
     * @apiVersion 1.0.0
     * @apiName awardCase awardCaseChangeRecommended
     * @apiGroup AdminAwardCase
     *
     * @apiParam {integer} id ID
     * @apiParam {integer} evt 推荐： 0.取消推荐；1.推荐；
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
    public function changeRecommended(Request $request)
    {
        $id = $request->input("id");
        $evt = $request->input("evt");

        $awardCase = AwardCase::find($id);
        if (!$awardCase) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($evt) {
            $awardCase->recommended = 1;
            $awardCase->recommended_on = time();
        } else {
            $awardCase->recommended = 0;
        }
        if (!$awardCase->save()) {
            return $this->response->array($this->apiError('Error', 500));
        } else {
            return $this->response->array($this->apiSuccess());
        }
    }

    /**
     * @api {delete} /admin/awardCase 奖项案例删除
     * @apiVersion 1.0.0
     * @apiName awardCase awardCaseDelete
     * @apiGroup AdminAwardCase
     *
     * @apiParam {integer} id ID
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
        AwardCase::destroy($id);

        return $this->response->array($this->apiSuccess());
    }

}
