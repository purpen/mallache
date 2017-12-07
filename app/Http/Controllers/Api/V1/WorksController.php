<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\WorksListsTransformer;
use App\Http\Transformer\WorksTransformer;
use App\Models\AssetModel;
use App\Models\Works;
use App\Models\DesignCaseModel;
use App\Models\DesignCompanyModel;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WorksController extends BaseController
{
    /**
     * @api {get} /works  公司个人中心查看所属作品
     * @apiVersion 1.0.0
     * @apiName works index
     * @apiGroup works
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "user_id": 12,
     *      "company_id": 22,
     *      "tags": [],   // 标签
     *      "title": "杯子",
     *      "summary":  "备注",   // 备注
     *      "images": [],   // 图片列表
     *      "type": 1,  // 类型：1.默认；
     *      "cover": "",  // 封面图
     *      "cover_id": 2,
     *      "view_count": 22,   // 浏览量
     *      "published": 1, // 是否发布：0.否；1.是；
     *      "status": 0,  // 状态：0.禁用；1.启用；
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     *
     */
    public function index()
    {
        $user_id = intval($this->auth_user_id);
        $works = Works::where('user_id', $user_id)->get();

        return $this->response->collection($works, new WorksListTransformer())->setMeta($this->apiMeta());

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @api {post} /works 保存作品
     * @apiVersion 1.0.0
     * @apiName  works store
     * @apiGroup works
     * @apiParam {string} title 标题
     * @apiParam {string} content 作品介绍
     * @apiParam {integer} type 类型:1.默认;
     * @apiParam {integer} match_id 所属大赛ID
     * @apiParam {string} tags 标签
     * @apiParam {integer} cover_id 封面图ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *    "data": {
     *      "id": 23,
     *      "user_id": 12,
     *      "company_id": 22,
     *      "tags": [],   // 标签
     *      "title": "杯子",
     *      "summary":  "备注",   // 备注
     *      "images": [],   // 图片列表
     *      "type": 1,  // 类型：1.默认；
     *      "cover": "",  // 封面图
     *      "cover_id": 2,
     *      "view_count": 22,   // 浏览量
     *      "published": 1, // 是否发布：0.否；1.是；
     *      "status": 0,  // 状态：0.禁用；1.启用；
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *  }
     */
    public function store(Request $request)
    {
        // 验证规则
        $rules = [
            'title' => 'required|max:50',
            'content' => 'max:1000',
            'cover_id' => 'required|integer',
            'type' => 'integer'
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '最多50字符',
            'content.max' => '内容最多1000字符',
            'type.integer' => '类型必须为整型',
            'cover_id.required' => '请设置一张封面图',
            'cover_id.integer' => '封面ID为整型',
        ];


        $type = $request->input('type') ? (int)$request->input('type') : 1;
        $published = $request->input('published') ? (int)$request->input('published') : 1;
        $cover_id = $request->input('cover_id') ? (int)$request->input('cover_id') : null;

        $params = array(
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'published' => $published,
            'status' => 1,
            'cover_id' => $cover_id,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }


        $design_company = Works::where('user_id', $this->auth_user_id)->first();
        if ($design_company) $params['company_id'] = $design_company->id;

        try {
            $works = Works::create($params);
            $random = $request->input('random') ?? '';
            AssetModel::setRandom($works->id, $random);
        } catch (\Exception $e) {

            throw new HttpException($e->getMessage());
        }

        return $this->response->item($works, new WorksTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /works/{id}  作品详情
     * @apiVersion 1.0.0
     * @apiName works show
     * @apiGroup works
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *    "data": {
     *      "id": 23,
     *      "user_id": 12,
     *      "company_id": 22,
     *      "tags": [],   // 标签
     *      "title": "杯子",
     *      "summary":  "备注",   // 备注
     *      "images": [],   // 图片列表
     *      "type": 1,  // 类型：1.默认；
     *      "cover": "",  // 封面图
     *      "cover_id": 2,
     *      "view_count": 22,   // 浏览量
     *      "published": 1, // 是否发布：0.否；1.是；
     *      "status": 0,  // 状态：0.禁用；1.启用；
     *      }
     *   }
     */
    public function show($id)
    {
        $id = intval($id);
        $works = Works::find($id);

        if (!$works) {
            return $this->response->array($this->apiError('not found', 404));
        }

        //判断是否有有权限查看案例详情
        $design_company = new DesignCompanyModel();
        // 此参数用来判断是否返回设计公司的联系方式
        $is_phone = true;
        if (($this->auth_user_id == null) || !$design_company->isRead($this->auth_user_id, $works->company_id)) {
            $is_phone = false;
        }

        if($is_phone){
            return $this->response->item($works, new WorksTransformer())->setMeta($this->apiMeta());
        }else{
            return $this->response->item($works, new WorksOpenTransformer())->setMeta($this->apiMeta());
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DesignCaseModel $designCaseModel
     * @return \Illuminate\Http\Response
     */
    public function edit(DesignCaseModel $works)
    {
        //
    }

    /**
     * @api {put} /works/12 更新
     * @apiVersion 1.0.0
     * @apiName works update
     * @apiGroup works
     * @apiParam {string} title 标题
     * @apiParam {string} content 作品介绍
     * @apiParam {integer} type 类型:1.默认;
     * @apiParam {integer} match_id 所属大赛ID
     * @apiParam {string} tags 标签
     * @apiParam {integer} cover_id 封面图ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
     *    "data": {
     *      "id": 23,
     *      "user_id": 12,
     *      "company_id": 22,
     *      "tags": [],   // 标签
     *      "title": "杯子",
     *      "summary":  "备注",   // 备注
     *      "images": [],   // 图片列表
     *      "type": 1,  // 类型：1.默认；
     *      "cover": "",  // 封面图
     *      "cover_id": 2,
     *      "view_count": 22,   // 浏览量
     *      "published": 1, // 是否发布：0.否；1.是；
     *      "status": 0,  // 状态：0.禁用；1.启用；
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     */
    public function update(Request $request, $id)
    {
        // 验证规则
        $rules = [
            'title' => 'required|max:50',
            'content' => 'max:1000',
            'cover_id' => 'required|integer',
            'type' => 'integer'
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '最多50字符',
            'content.max' => '内容最多1000字符',
            'type.integer' => '类型必须为整型',
            'cover_id.required' => '请设置一张封面图',
            'cover_id.integer' => '封面ID为整型',
        ];

        $type = $request->input('type') ? (int)$request->input('type') : 1;
        $published = $request->input('published') ? (int)$request->input('published') : 1;
        $cover_id = $request->input('cover_id') ? (int)$request->input('cover_id') : null;

        $params = array(
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'published' => $published,
            'status' => 1,
            'cover_id' => $cover_id,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        //检验是否存在该作品
        $works = Works::find($id);
        if (!$works) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的案例
        if ($works->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //$designCase = DesignCaseModel::where('id', intval($id))->first();
        $works->update($all);
        if (!$works) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($works, new WorksTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /works/3 删除
     * @apiVersion 1.0.0
     * @apiName works delete
     * @apiGroup works
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *
     */
    public function destroy($id)
    {
        //检验是否存在
        $works = Works::find($id);
        if (!$works) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的作品
        if ($works->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $ok = $works->delete();
        if (!$ok) {
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }

}
